<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
	public function findByPublished($request)
    {
        $filterTitle = $request->get('Title');
        
        $qb = $this->createQueryBuilder('users');
        
        if($filterTitle != ''){    
            $qb->andWhere('users.username LIKE :filterTitle')
                ->setParameter('filterTitle','%'.$filterTitle.'%');
        }
        
        return  $qb->getQuery()
                  ->execute();
    }
}