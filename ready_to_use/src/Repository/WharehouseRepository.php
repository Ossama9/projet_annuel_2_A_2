<?php

namespace App\Repository;

use App\Entity\Wharehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Wharehouse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wharehouse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wharehouse[]    findAll()
 * @method Wharehouse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WharehouseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wharehouse::class);
    }

    // /**
    //  * @return Wharehouse[] Returns an array of Wharehouse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Wharehouse
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
