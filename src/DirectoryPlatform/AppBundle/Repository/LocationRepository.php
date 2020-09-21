<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
{
	public function findParent() {
		$qb = $this->createQueryBuilder('location');

		return $qb->andWhere('location.location is NULL')
		          ->getQuery()
		          ->execute();
	}

	public function findRecent($count = -1, $offset = 0) 
    {
    	$qb = $this->createQueryBuilder('location');

    	if (-1 !== $count) {
    		$qb->setFirstResult($offset);
		    $qb->setMaxResults($count);
		}

		return $qb->andWhere('location.location is NULL')
		          ->getQuery()
		          ->execute();
    }

    
    public function findByPublished($request)
    {
        // $filterListCate = $request->get('filter');
        $filterName = $request->get('name');
        $filterSlug = $request->get('slug');

        $qb = $this->createQueryBuilder('location');

        $qb->andWhere('location.location is NULL');

        if($filterName != ''){
            $qb->andWhere('location.name LIKE :filterListCate')
                ->setParameter('filterListCate', '%'.$filterName.'%');
        }
        if($filterSlug != ''){
            $qb->andWhere('location.slug LIKE :filterSlug')
                ->setParameter('filterSlug', '%'.$filterSlug.'%');
        }
        return $qb->getQuery()
           ->execute();
    }

}