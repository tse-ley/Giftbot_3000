<?php

namespace App\Repository;

use App\Entity\Gift;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gift>
 */
class GiftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gift::class);
    }


    public function search(array $criteria): array
    {
        $qb = $this->createQueryBuilder('g');

        if (!empty($criteria['category'])) {
            $qb->andWhere('g.category = :category')
               ->setParameter('category', $criteria['category']);
        }

        if (!empty($criteria['label'])) {
            $qb->andWhere('g.label = :label')
               ->setParameter('label', $criteria['label']);
        }

        if (!empty($criteria['age'])) {
            $qb->andWhere('g.age = :age')
               ->setParameter('age', $criteria['age']);
        }

        return $qb->getQuery()->getResult();
    }
}
