<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AmenityRepository extends EntityRepository
{
	public function findAll() {
		$qb = $this->createQueryBuilder('amenity');

		return $qb->orderBy('amenity.name', 'ASC')
		          ->getQuery()
		          ->execute();		
	}

	public function findByPublished($request)
	{
		$filterName = $request->get('Name');
		$filterSlug = $request->get('Slug');
		$qb = $this->createQueryBuilder('amenity')
		            ->orderBy('amenity.name', 'ASC');
		if($filterName != ''){
		    $qb->andWhere('amenity.name LIKE :filterName')
		         ->setParameter('filterName','%'.$filterName.'%');
		}
		if($filterSlug != ''){
		    $qb->andWhere('amenity.slug LIKE :filterSlug')
		         ->setParameter('filterSlug', '%'.$filterSlug.'%');
		}
		return  $qb->getQuery()
		          ->execute();
	}

}