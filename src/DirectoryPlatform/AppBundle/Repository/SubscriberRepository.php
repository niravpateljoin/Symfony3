<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SubscriberRepository extends EntityRepository
{
	public function findByPublished($request)
	{
		$filterName = $request->get('Name');
		$qb = $this->createQueryBuilder('subscriber')
		            ->orderBy('subscriber.email', 'ASC');
		if($filterName != ''){
		    $qb->andWhere('subscriber.email LIKE :filterName')
		         ->setParameter('filterName','%'.$filterName.'%');
		}
		return  $qb->getQuery()
		          ->execute();
	}

}