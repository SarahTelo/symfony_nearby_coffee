<?php

namespace App\Repository;

use App\Entity\Coffee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Coffee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coffee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coffee[]    findAll()
 * @method Coffee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoffeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coffee::class);
    }

    /**
     * *Rajout du détail de roasting dans la recherche de tous les cafés
     *
     * @return array
     */
    public function findAllDetailsList()
    {
        //SELECT * FROM coffee (as coffees => pour queryBuilder)
        $queryBuilder = $this->createQueryBuilder('coffees');
        //LEFT JOIN roasting ON coffee.roasting_id = roasting.id
        $queryBuilder->leftJoin('coffees.roasting', 'roasting');
        //ajout dans la recherche
        $queryBuilder->addSelect('roasting');
        //stockage de la réponse
        $query = $queryBuilder->getQuery();
        //envoi des résultats
        return $query->getResult();
    }

    /**
     * *Recherche de tous les cafés pour chaque torréfaction
     *
     * @param integer $roastingId
     * @return void
     */
    public function findAllByRoastingId(int $roastingId) 
    {
        //SELECT * FROM coffee WHERE roasting_id = 1
        $queryBuilder = $this->createQueryBuilder('coffees');
        //propriété de l'entité "coffee": "roasting"
        //stockage de la réponse
        $query = $queryBuilder->where("coffees.roasting = $roastingId")->getQuery();
        //envoi des résultats
        return $query->getResult(); 
    }


    // /**
    //  * @return Coffee[] Returns an array of Coffee objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Coffee
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
