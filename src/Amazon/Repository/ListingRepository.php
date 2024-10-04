<?php

namespace Amazon\Repository;

use Amazon\Entity\Listing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Listing>
 */
class ListingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Listing::class);
    }

    public function findBySellerSku($sellerSku) {
        $listingResult = $this->findBy(array('seller_sku' => $sellerSku));

        return $listingResult[0];
    }   
}
