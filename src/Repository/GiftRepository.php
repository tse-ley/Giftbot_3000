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

    /**
     * Finds gifts by a dynamic set of criteria.
     *
     * @param array $criteria An associative array of search criteria.
     *                        Expected keys: 'category', 'label'.
     * @return Gift[] Returns an array of Gift objects
     */
    public function search(array $criteria): array
    {
        // Start building the query. 'g' is an alias for the Gift entity.
        $qb = $this->createQueryBuilder('g');

        // Dynamically add conditions only if the criteria are provided.
        if (!empty($criteria['category'])) {
            $qb->andWhere('g.category = :category')
               ->setParameter('category', $criteria['category']);
        }

        if (!empty($criteria['label'])) {
            $qb->andWhere('g.label = :label')
               ->setParameter('label', $criteria['label']);
        }


        // Add a default order to the results for a consistent user experience.
        $qb->orderBy('g.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}