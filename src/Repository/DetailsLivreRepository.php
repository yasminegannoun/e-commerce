<?php

namespace App\Repository;

use App\Entity\DetailsLivre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsLivre>
 *
 * @method DetailsLivre|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsLivre|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsLivre[]    findAll()
 * @method DetailsLivre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsLivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsLivre::class);
    }

//    /**
//     * @return DetailsLivre[] Returns an array of DetailsLivre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DetailsLivre
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
