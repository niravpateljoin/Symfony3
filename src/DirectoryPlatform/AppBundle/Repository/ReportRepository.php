<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ReportRepository extends EntityRepository
{
	public function findByPublished($request)
    {
        $filterListing = $request->get('Listing');
        $filterMessage = $request->get('Message');
        $filterUser = $request->get('User');
        $filterApproved = $request->get('Approved');
        
        $qb = $this->createQueryBuilder('report')
                    ->orderBy('report.created', 'DESC');
        
        if($filterListing != ''){    
            $qb->andWhere('report.listing = :filterListing')
                ->setParameter('filterListing',$filterListing);
        }
        if($filterMessage != ''){    
            $qb->andWhere('report.message LIKE :filterMessage')
                 ->setParameter('filterMessage','%'.$filterMessage.'%');
        }
        
        if($filterUser != ''){  
            $qb->andWhere('report.user = :filterUser')
                 ->setParameter('filterUser',$filterUser);
        }
        
        if($filterApproved != ''){  
            if($filterApproved == 0){
                $qb->andWhere('report.isApproved = :filterApproved OR report.isApproved is NULL ');
            }else{
                $qb->andWhere('report.isApproved = :filterApproved');
            }
            $qb->setParameter('filterApproved', $filterApproved);
        }
        
        return  $qb->getQuery()
                  ->execute();
    }
}