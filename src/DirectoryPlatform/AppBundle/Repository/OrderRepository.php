<?php

namespace DirectoryPlatform\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OrderRepository extends EntityRepository
{
    public function findAll() 
    {
        $qb = $this->createQueryBuilder('orders');

        return $qb->orderBy('orders.created', 'DESC')
                  ->getQuery()
                  ->execute();
    }

    public function findAllByUser($user)
    {
        $qb = $this->createQueryBuilder('orders');

        return $qb->andWhere('orders.user = :user')
            ->setParameter('user', $user)
            ->orderBy('orders.created', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function getInvoiceId($order)
    {
        $qb = $this->createQueryBuilder('orders');
        $date = date_create(date('Y')."-01-01");
        $fromDate = date_format($date, "Y/m/d H:i:s");

        $orders = $qb->andWhere('orders.created BETWEEN :fromDate AND :orderDate')
            ->setParameters(
                [
                    'fromDate' => $fromDate,
                    'orderDate' => $order->getCreated(),
                ]
            )
            ->getQuery()
            ->execute();

        $number = str_pad(count($orders), 5, '0', STR_PAD_LEFT);

        return date('Y').$number;
    }

    public function findByPublished($request)
    {
        $filterId = $request->get('Id');
        $filterPrice = $request->get('Price');
        $filterType = $request->get('Type');
        
        $qb = $this->createQueryBuilder('orders')
                    ->orderBy('orders.created', 'DESC');
        
        if($filterId != ''){    
            $qb->andWhere('orders.id = :filterId')
                ->setParameter('filterId',$filterId);
        }
        if($filterPrice != ''){    
            $qb->andWhere('orders.price LIKE :filterPrice')
                 ->setParameter('filterPrice',$filterPrice.'%');
        }
        if($filterType != ''){    
            $qb->andWhere('orders.status LIKE :filterType')
                 ->setParameter('filterType','%'.$filterType.'%');
        }
        
        
        return  $qb->getQuery()
                  ->execute();
    }
}