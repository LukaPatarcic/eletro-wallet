<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        $emConfig->addCustomDatetimeFunction('DATE', 'DoctrineExtensions\Query\Mysql\Date');

    }

    /**
     * @param string $email
     * @param int $limit
     * @return mixed
     */
    public function findLastFiveTransactions(string $email, int $limit = 5)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('u.email = :email')
            ->join('t.transactionType','tt')
            ->join('t.user','u')
            ->addSelect('tt')
            ->setParameter (':email',$email)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $email
     * @return array|null
     */
    public function findAllTransactionForChart(string $email)
    {
        return $this->addUserAndTransactionTypeWithEmailJoin()
            ->setParameter (':email',$email)
            ->select('SUM(t.amount) as amount,tt.name,tt.type, COUNT(tt.id) as number')
            ->groupBy('tt.name,tt.type')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param string $email
     * @param int $year
     * @param string $type
     * @return array|null
     */
    public function findAllTransactionByYearForChart(string $email, int $year, string $type)
    {
        return $this->addUserAndTransactionTypeWithEmailJoin()
            ->andWhere('YEAR(t.createdAt) = :year')
            ->andWhere('tt.type = :type')
            ->select('SUM(t.amount) as amount,tt.name,tt.type, COUNT(tt.id) as number')
            ->setParameter(':email',$email)
            ->setParameter(':year',$year)
            ->setParameter(':type',$type)
            ->groupBy('tt.name,tt.type')
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @param string $email
     * @param int $year
     * @param int $month
     * @param string $type
     * @return array|null
     */
    public function findAllTransactionByMonthForChart(string $email, int $year, int $month, string $type)
    {
        return $this->addUserAndTransactionTypeWithEmailJoin()
            ->andWhere('YEAR(t.createdAt) = :year')
            ->andWhere('MONTH(t.createdAt) = :month')
            ->andWhere('tt.type = :type')
            ->select('SUM(t.amount) as amount,tt.name,tt.type, COUNT(tt.id) as number')
            ->setParameter(':email',$email)
            ->setParameter(':year',$year)
            ->setParameter(':month',$month)
            ->setParameter(':type',$type)
            ->groupBy('tt.name,tt.type')
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @param string $email
     * @param string $date
     * @param string $type
     * @return array|null
     */
    public function findAllTransactionsByDateForChart(string $email,string $date,string $type)
    {
        return $this->addUserAndTransactionTypeWithEmailJoin()
            ->andWhere('DATE(t.createdAt) = :date')
            ->andWhere('tt.type = :type')
            ->select('SUM(t.amount) as amount,tt.name,tt.type, COUNT(tt.id) as number')
            ->setParameter(':email',$email)
            ->setParameter(':date',$date)
            ->setParameter(':type',$type)
            ->groupBy('tt.name,tt.type')
            ->getQuery()
            ->getResult()
            ;

    }

    public function findAllMonthByYearForComparison(string $email,string $year,string $month,string $type)
    {
        return $this->addUserAndTransactionTypeWithEmailJoin()
            ->andWhere('YEAR(t.createdAt) = :year')
            ->andWhere('MONTH(t.createdAt) = :month')
            ->andWhere('tt.type = :type')
            ->select('SUM(t.amount) as amount, DATE(t.createdAt) as groupByDate, COUNT(tt.id) as number')
            ->setParameter(':email',$email)
            ->setParameter(':year',$year)
            ->setParameter(':month',$month)
            ->setParameter(':type',$type)
            ->groupBy('groupByDate')
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @param string $email
     * @param string $date
     * @param string $type
     * @return array|null
     */
    public function findAllTransactionsByMonthForChart(string $email,string $date,string $type)
    {
        return $this->addUserAndTransactionTypeWithEmailJoin()
            ->andWhere('MONTH(t.createdAt) = :date')
            ->andWhere('tt.type = :type')
            ->select('SUM(t.amount) as amount,tt.name,tt.type, COUNT(tt.id) as number')
            ->setParameter(':email',$email)
            ->setParameter(':date',$date)
            ->setParameter(':type',$type)
            ->groupBy('tt.name,tt.type')
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @param string $email
     * @param int $year
     * @return array|null
     */
    public function findAllRelevantMonths(string $email,int $year)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('u.email = :email')
            ->andWhere('YEAR(t.createdAt) = :year')
            ->join('t.user','u')
            ->select ('DISTINCT MONTH(t.createdAt) as month')
            ->setParameter(':email',$email)
            ->setParameter(':year',$year)
            ->orderBy('month','ASC')
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @param string $email
     * @return array|null
     */
    public function findAllRelevantYears(string $email)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('u.email = :email')
            ->join('t.user','u')
            ->select ('DISTINCT YEAR(t.createdAt) as year')
            ->setParameter(':email',$email)
            ->orderBy('year','ASC')
            ->getQuery()
            ->getResult()
            ;

    }

    private function addUserAndTransactionTypeWithEmailJoin(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('u.email = :email')
            ->join('t.transactionType','tt')
            ->join('t.user','u')
            ->addSelect('tt')
            ->addSelect('t')
            ;

    }

    private function getOrCreateQueryBuilder(QueryBuilder $qb = null)
    {
        return $qb ?: $this->createQueryBuilder('t');
    }
}
