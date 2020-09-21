<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ReviewRepository extends EntityRepository
{
	public function findAll() {
		$qb = $this->createQueryBuilder('review');

		return $qb->orderBy('review.created', 'DESC')
		          ->getQuery()
		          ->execute();
	}

	public function findForListing($listing_id, $count = 10) {
		$qb = $this->createQueryBuilder('review');
		$qb->andWhere('review.isPublished = 1')
		   ->andWhere('review.listing = :listing')
		   ->orderBy('review.created', 'DESC')
		   ->setParameters([
				'listing' => $listing_id,
		   ]);

		if ($count !== -1) {
			$qb->setMaxResults($count);
		}

		return $qb->getQuery()
		          ->execute();
	}

	public function findByPublished($request)
    {
        $filterTitle = $request->get('Title');
        $filterAuthor = $request->get('Author');
        $filterDate = $request->get('Date');
        $filterPublished = $request->get('Published');
        
        $qb = $this->createQueryBuilder('review')
                    ->orderBy('review.created', 'DESC');
        
        if($filterTitle != ''){    
            $qb->andWhere('review.listing = :filterTitle')
                ->setParameter('filterTitle',$filterTitle);
        }
        if($filterAuthor != ''){    
            $qb->andWhere('review.user = :filterAuthor')
                 ->setParameter('filterAuthor',$filterAuthor);
        }
        
        if($filterDate != ''){  
            $qb->andWhere('review.created LIKE :filterDate')
                 ->setParameter('filterDate','%'.$filterDate.'%');
        }
        
        if($filterPublished != ''){  
            if($filterPublished == 0){
                $qb->andWhere('review.isPublished = :filterPublished OR review.isPublished is NULL ');
            }else{
                $qb->andWhere('review.isPublished = :filterPublished');
            }
            $qb->setParameter('filterPublished', $filterPublished);
        }
        
        return  $qb->getQuery()
                  ->execute();
    }
}