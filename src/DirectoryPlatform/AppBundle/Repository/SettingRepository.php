<?php

namespace DirectoryPlatform\AppBundle\Repository;

/**
 * SettingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SettingRepository extends \Doctrine\ORM\EntityRepository
{
	public function findByPublished($request)
    {
        $filterKey = $request->get('Key');
        $filterValue = $request->get('Value');
        
        $qb = $this->createQueryBuilder('settings');
        
        if($filterKey != ''){    
            $qb->andWhere('settings.keyVal LIKE :filterKey')
                ->setParameter('filterKey','%'.$filterKey.'%');
        }
        if($filterValue != ''){    
            $qb->andWhere('settings.value LIKE :filterValue')
                ->setParameter('filterValue','%'.$filterValue.'%');
        }
        
        return  $qb->getQuery()
                  ->execute();
    }
}