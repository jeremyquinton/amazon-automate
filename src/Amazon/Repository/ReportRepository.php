<?php

namespace Amazon\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Amazon\Entity\Report;

/**
 * @extends ServiceEntityRepository<Report>
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    //    /**
    //     * @return Report[] Returns an array of Report objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Report
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // public function findAllNew()
    // {
    //     return $this->findBy(array('username' => 'ASC'));
    // }
    public function findByStatusNull() {
        return $this->findBy(array('processing_status' => null));
    }

    public function findByAmazonReportId($amazonReportId) {
        $reportResult = $this->findBy(array('amazon_report_id' => $amazonReportId));

        return $reportResult[0];
    }
}
