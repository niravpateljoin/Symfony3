<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
	public function findParent() 
	{
		$qb = $this->createQueryBuilder('category');

		return $qb->andWhere('category.category is NULL')
				  ->orderBy('category.name', 'ASC')
		          ->getQuery()
		          ->execute();
	}

	public function findPopular($count = 6) 
	{
		$qb = $this->createQueryBuilder('category')
				   ->andWhere('category.category is NULL')
				   ->innerJoin('category.listings', 'listings')
				   ->andWhere('listings.isPublished = 1')
				   ->addSelect('COUNT(listings) as listings_count')
				   ->groupBy('category.id')
				   ->orderBy('listings_count', 'DESC');
				   // ->orderBy('category.name', 'ASC');

		if (-1 !== $count) {
			$qb->setMaxResults($count);
		}

		$categories = [];
		$results = $qb->getQuery()->execute();		

		foreach ($results as $value) {
			$categories[] = $value[0];
		}
		
		return $categories;
	}

	public function findByPublished($request)
    {
        // $filterListCate = $request->get('filter');
        $filterName = $request->get('Name');
        $filterSlug = $request->get('Slug');

        $qb = $this->createQueryBuilder('category');

        $qb->andWhere('category.category is NULL')
				  ->orderBy('category.name', 'ASC');

        if($filterName != ''){
            $qb->andWhere('category.name LIKE :filterName')
                ->setParameter('filterName', '%'.$filterName.'%');
        }
        if($filterSlug != ''){
            $qb->andWhere('category.slug LIKE :filterSlug')
                ->setParameter('filterSlug', '%'.$filterSlug.'%');
        }
        return $qb->getQuery()
           ->execute();
    }

}