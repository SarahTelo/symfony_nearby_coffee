<?php

namespace App\Repository;

use App\Entity\Roasting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Roasting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Roasting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Roasting[]    findAll()
 * @method Roasting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoastingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Roasting::class);
    }

    // /**
    //  * @return Roasting[] Returns an array of Roasting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Roasting
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
