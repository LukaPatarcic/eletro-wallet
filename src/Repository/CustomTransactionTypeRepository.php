<?php

namespace App\Repository;

use App\Entity\CustomTransactionType;
use App\Entity\TransactionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomTransactionType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomTransactionType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomTransactionType[]    findAll()
 * @method CustomTransactionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomTransactionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomTransactionType::class);
    }

    public function findTransactionList($type, $userId)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin(TransactionType::class, 'tt', Join::WITH, 'tt = c.transactionType')
            ->where('tt.type = :type')
            ->andWhere('c.user = :user')
            ->setParameter('type', $type)
            ->setParameter('user', $userId)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return CustomTransactionType[] Returns an array of CustomTransactionType objects
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
    public function findOneBySomeField($value): ?CustomTransactionType
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
