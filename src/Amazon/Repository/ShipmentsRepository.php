<?php

namespace Amazon\Repository;

use Amazon\Entity\Shipments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Shipments>
 */
class ShipmentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shipments::class);
    }

    public function findByShipmentId($shipmentId) {
        
        $shipmentResult = $this->findBy(array('amazonShipmentId' => $shipmentId));
        if (empty($shipmentResult)) {
            return FALSE;
        }

        return $shipmentResult[0];
    }

}
