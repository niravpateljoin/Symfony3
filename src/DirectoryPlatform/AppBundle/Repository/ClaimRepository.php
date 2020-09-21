<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ClaimRepository extends EntityRepository
{
	public function findByPublished($request)
    {
        $filterListing = $request->get('Listing');
        $filterMessage = $request->get('Message');
        $filterUser = $request->get('User');
        $filterApproved = $request->get('Approved');
        
        $qb = $this->createQueryBuilder('claim')
                    ->orderBy('claim.created', 'DESC');
        
        if($filterListing != ''){    
            $qb->andWhere('claim.listing = :filterListing')
                ->setParameter('filterListing',$filterListing);
        }
        if($filterMessage != ''){    
            $qb->andWhere('claim.message LIKE :filterMessage')
                 ->setParameter('filterMessage','%'.$filterMessage.'%');
        }
        
        if($filterUser != ''){  
            $qb->andWhere('claim.user = :filterUser')
                 ->setParameter('filterUser',$filterUser);
        }
        
        if($filterApproved != ''){  
            if($filterApproved == 0){
                $qb->andWhere('claim.isApproved = :filterApproved OR claim.isApproved is NULL ');
            }else{
                $qb->andWhere('claim.isApproved = :filterApproved');
            }
            $qb->setParameter('filterApproved', $filterApproved);
        }
        
        return  $qb->getQuery()
                  ->execute();
    }
}