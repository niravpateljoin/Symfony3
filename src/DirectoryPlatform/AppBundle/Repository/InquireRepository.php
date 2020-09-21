<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class InquireRepository extends EntityRepository
{
	public function findAll() 
    {
		$qb = $this->createQueryBuilder('inquire');

		return $qb->orderBy('inquire.created', 'DESC')
		          ->getQuery()
		          ->execute();
	}

	public function findByPublished($request)
    {
        $filterName = $request->get('Name');
        $filterListing = $request->get('Listing');
        $filterDate = $request->get('Date');
        
        $qb = $this->createQueryBuilder('inquire')
                    ->orderBy('inquire.created', 'DESC');
        
        if($filterName != ''){    
            $qb->andWhere('inquire.name LIKE :filterName')
                ->setParameter('filterName','%'.$filterName.'%');
        }
        if($filterListing != ''){    
            $qb->andWhere('inquire.listing = :filterListing')
                 ->setParameter('filterListing',$filterListing);
        }
        if($filterDate != ''){    
            $qb->andWhere('inquire.created LIKE :filterDate')
                 ->setParameter('filterDate','%'.$filterDate.'%');
        }
        
        
        return  $qb->getQuery()
                  ->execute();
    }	
}