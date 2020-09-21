<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
	public function findAll() {
		$qb = $this->createQueryBuilder('post');

		return $qb->orderBy('post.created', 'DESC')
		          ->getQuery()
		          ->execute();
	}

	public function findRecent($count = -1, $published = true) {
		$qb = $this->createQueryBuilder('post');

		if ($published) {
			$qb->andWhere('post.isPublished = 1');
		}		

		if (-1 !== $count) {
			$qb->setMaxResults($count);
		}
		
		return $qb->orderBy('post.created', 'DESC')		          
		          ->getQuery()
		          ->execute();
	}

	public function findByPublished($request)
    {
        $filterTitle = $request->get('title');
        $filterCategory = $request->get('category');
        $filterPublished = $request->get('published');
        
        $qb = $this->createQueryBuilder('post')
                    ->orderBy('post.created', 'DESC');
        
        if($filterTitle != ''){    
            $qb->andWhere('post.name LIKE :filterTitle')
                 ->setParameter('filterTitle','%'.$filterTitle.'%');
        }
        
        if($filterCategory != ''){  
            $qb->andWhere('post.category = :filterCategory')
                 ->setParameter('filterCategory',$filterCategory);
        }
        
        if($filterPublished != ''){  
            if($filterPublished == 0){
                $qb->andWhere('post.isPublished = :filterPublished OR post.isPublished is NULL ');
            }else{
                $qb->andWhere('post.isPublished = :filterPublished');
            }
            $qb->setParameter('filterPublished', $filterPublished);
        }
        
        return  $qb->getQuery()
                  ->execute();
    }
}