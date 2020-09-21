<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ListingRepository extends EntityRepository
{
	public function findAll() 
    {
		$qb = $this->createQueryBuilder('listing');

		return $qb->orderBy('listing.created', 'DESC')
		          ->getQuery()
		          ->execute();
	}

    public function unfeature()
    {
        $qb = $this->createQueryBuilder('listing');

        return $qb->update()
            ->set('listing.isFeatured', '0')
            ->andWhere('listing.featuredUntil > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->execute();
    }

    public function unpublish()
    {
        $qb = $this->createQueryBuilder('listing');

        return $qb->update()
            ->set('listing.isPublished', '0')
            ->andWhere('listing.publishedUntil > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->execute();
    }

    public function findByUser(\DirectoryPlatform\AppBundle\Entity\User $user)
    {
        if (!$user) {
            return false;
        }

        $qb = $this->createQueryBuilder('listing');

        return $qb->andWhere('listing.user = :user')
            ->setParameter('user', $user)
            ->orderBy('listing.created', 'DESC')
            ->addOrderBy('listing.id', 'DESC')
            ->getQuery()
            ->execute();
    }

	public function findRecent($count = -1, $offset = 0, $published = true) 
    {
		$qb = $this->createQueryBuilder('listing');

        if ($published) {
            $qb->andWhere('listing.isPublished = 1');
        }		

		if (-1 !== $count) {
            $qb->setFirstResult($offset);
		    $qb->setMaxResults($count);
		}

		return $qb->orderBy('listing.created', 'DESC')
                  ->getQuery()
                  ->execute();
	}

    public function findByFilterQuery($request)
    {
        $qb = $this->createQueryBuilder('listing');
        $qb->andWhere('listing.isPublished = 1');

        // Radius search
        if (!empty($request->query->get('radius_enabled')) && 
            !empty($request->query->get('place_latitude')) 
            && !empty($request->query->get('place_longitude'))) {

            $radius_km = 6371;
            $radius_mi = 3959;
            $distance = !empty($request->query->get('radius')) ? $request->query->get('radius') : 25;

            $qb->andWhere(
                '(' . $radius_km . ' * acos(cos(radians(' . $request->query->get('place_latitude') . '))' .
                    '* cos(radians(listing.latitude))' .
                    '* cos(radians(listing.longitude)' .
                    '- radians(' . $request->query->get('place_longitude') . '))' .
                    '+ sin(radians(' . $request->query->get('place_latitude') . '))' .
                    '* sin(radians(listing.latitude)))) < :distance'
            )->setParameter('distance', $distance);
        }

        // Keyword
        if (!empty($request->query->get('keyword'))) {
            $qb->andWhere('listing.name LIKE :filterKeyword OR listing.description LIKE :filterKeyword')
                ->setParameter('filterKeyword', '%'.$request->query->get('keyword').'%');
        }

        // Price from
        if (!empty($request->query->get('price_enabled'))) {            
            if (!empty($request->query->get('price_from'))) { 
                if (0 != $request->query->get('price_from')) {                    
                    $qb->andWhere('listing.price >= :priceFrom')
                        ->setParameter('priceFrom', $request->query->get('price_from'));
                }
            }

            // Price to
            if (!empty($request->query->get('price_to'))) {
                if (0 != $request->query->get('price_from')) {
                    $qb->andWhere('listing.price <= :priceTo')
                        ->setParameter('priceTo', $request->query->get('price_to'));
                } else {
                    $qb->andWhere('listing.price <= :priceTo OR listing.price IS NULL')
                        ->setParameter('priceTo', $request->query->get('price_to'));
                }
            }
        }

        // Category
        if (!empty($request->query->get('category'))) {
            $qb->leftJoin('listing.category', 'category')
                ->andWhere('category.id = :category')
                ->setParameter('category', $request->query->get('category'));
        }

        // Type filter
        if( !empty($request->query->get('product_type')) ){
            $qb->andWhere('listing.productType = :productType')
                ->setParameter('productType', $request->query->get('product_type')); 
        }

        $results = $qb->addOrderBy('listing.isFeatured', 'DESC')
            ->addOrderBy('listing.created', 'DESC')
            ->getQuery()
            ->execute();

        
        return $results;
    }

    public function findRecentCategory( $count = -1, $objCategory , $offset = 0 ) 
    {
        $qb = $this->createQueryBuilder('listing');

        if (-1 !== $count) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($count);
        }

        return $qb->andWhere('listing.category = :objCategory')
                  ->andWhere('listing.isPublished = 1')   
                  ->setParameter('objCategory', $objCategory)
                  ->orderBy('listing.name', 'ASC')
                  ->getQuery()
                  ->execute();
    }

    public function findRecentCategory1($lat,$long, $count = -1, $objCategory , $offset = 0 ) 
    {
        $qb = $this->createQueryBuilder('listing');

        $qb->addSelect('((ACOS(SIN(:lat * (22/7) / 180) * SIN(listing.latitude * (22/7) / 180) + COS(:lat * (22/7) / 180) * COS(listing.latitude * (22/7) / 180) * COS((:lng - listing.longitude) * (22/7) / 180)) * 180 / (22/7)) * 60 * 1.1515) as HIDDEN distance');
         

        if (-1 !== $count) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($count);
        }

        return $qb->andWhere('listing.category = :objCategory')
                  ->andWhere('listing.isPublished = 1')   
                  ->setParameter('objCategory', $objCategory)
                  ->setParameter('lat', $lat)
                  ->setParameter('lng', $long)
                  ->orderBy('listing.name', 'ASC')
                  ->orderBy('distance')
                  ->getQuery()
                  ->execute();
    }


    public function findUsingSortBy($request, $objCategory){
        
        $filterData = $request->get('filterData');
        $list = $request->get('list');
        $clientIp = getenv('REMOTE_ADDR');
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$clientIp  ));

        $lat = $ipdat->geoplugin_latitude;
        $long = $ipdat->geoplugin_longitude;
        // $categories = array();
        $qb = $this->createQueryBuilder('listing');

        if($filterData == ''){
            $qb->addSelect('((ACOS(SIN(:lat * (22/7) / 180) * SIN(listing.latitude * (22/7) / 180) + COS(:lat * (22/7) / 180) * COS(listing.latitude * (22/7) / 180) * COS((:lng - listing.longitude) * (22/7) / 180)) * 180 / (22/7)) * 60 * 1.1515) as HIDDEN distance')
               ->setParameter('lat', $lat)
               ->setParameter('lng', $long);
        }

        if($list == 'location'){
            $qb->andWhere('listing.location = :objCategory');
        }
        if($list == 'category'){
            $qb->andWhere('listing.category = :objCategory');     
        }
        if($list == 'agent'){
            $qb->andWhere('listing.user = :objCategory');
        }
                   
        $qb->setParameter('objCategory', $objCategory);
        $qb->andWhere('listing.isPublished = 1');
        
        if($filterData == 0){
            $qb->orderBy('distance');
        }

        if($filterData == 0){
            $qb->orderBy('listing.modified', 'DESC');
        }

        if($filterData == 1){
            $qb->orderBy('listing.created', 'DESC');
        }

        if($filterData == 2){
            $qb->orderBy('listing.price', 'ASC');
        }

        if($filterData == 3){
            $qb->orderBy('listing.price', 'DESC');
        }
                   
        return $qb->getQuery()->execute();

    }

    public function findByPublished($request)
    {
        $filterTitle = $request->get('title');
        $filterListCate = $request->get('category');
        $filterListPublis = $request->get('published');
        $filterListFeature = $request->get('featured');
        $filterListVerify = $request->get('verfied');
        // $ascWithTitle = $request->get('ascWithTitle');
        // print_r($ascWithTitle);exit;
        
        $qb = $this->createQueryBuilder('listing')
                    ->orderBy('listing.created', 'DESC');
        
        if($filterTitle != ''){    
            $qb->andWhere('listing.name LIKE :filterTitle')
                 ->setParameter('filterTitle','%'.$filterTitle.'%');
        }
        if($filterListCate != ''){    
            $qb->andWhere('listing.category = :filtercatelist')
                 ->setParameter('filtercatelist', $filterListCate);
        }
        
        if($filterListFeature != ''){  
            if($filterListFeature == 0){
                $qb->andWhere('listing.isFeatured = :isFeatured OR listing.isFeatured is NULL ');
            }else{
                $qb->andWhere('listing.isFeatured = :isFeatured');
            }
            $qb->setParameter('isFeatured', $filterListFeature);
        }

        if($filterListVerify != ''){

            if($filterListVerify == 0){
                $qb->andWhere('listing.isVerified = :isVerified OR listing.isVerified is NULL ');
            }else{
                $qb->andWhere('listing.isVerified = :isVerified');
            }
            $qb->setParameter('isVerified', $filterListVerify);
        }

        if($filterListPublis != ''){
            if($filterListPublis == 0){
                $qb->andWhere('listing.isPublished = :isPublished OR listing.isPublished is NULL ');
            }else{
                $qb->andWhere('listing.isPublished = :isPublished');
            }
            $qb->setParameter('isPublished', $filterListPublis);
        }
        // if($ascWithTitle == 'asc'){
        //     $qb->orderBy('listing.name', 'ASC'); 
        // }
        // if($ascWithTitle == 'desc'){
        //     $qb->orderBy('listing.name', 'DESC'); 
        // }
        return  $qb->getQuery()
                  ->execute();
    }


    public function findRecentLocation1($lat,$long, $count = -1, $objLocation , $offset = 0 ) 
    {
        $qb = $this->createQueryBuilder('listing');

        $qb->addSelect('((ACOS(SIN(:lat * (22/7) / 180) * SIN(listing.latitude * (22/7) / 180) + COS(:lat * (22/7) / 180) * COS(listing.latitude * (22/7) / 180) * COS((:lng - listing.longitude) * (22/7) / 180)) * 180 / (22/7)) * 60 * 1.1515) as HIDDEN distance');
         

        if (-1 !== $count) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($count);
        }

        return $qb->andWhere('listing.location = :objLocation')
                  ->andWhere('listing.isPublished = 1')
                  ->setParameter('objLocation', $objLocation)
                  ->setParameter('lat', $lat)
                  ->setParameter('lng', $long)
                  ->orderBy('listing.name', 'ASC')
                  ->orderBy('distance')
                  ->getQuery()
                  ->execute();
    }

    public function findRecentLocation( $count = -1, $objLocation , $offset = 0 ) 
    {
        $qb = $this->createQueryBuilder('listing');

        if (-1 !== $count) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($count);
        }

        return $qb->andWhere('listing.location = :objLocation')
                  ->andWhere('listing.isPublished = 1')
                  ->setParameter('objLocation', $objLocation)
                  ->orderBy('listing.name', 'ASC')
                  ->getQuery()
                  ->execute();
    }



}