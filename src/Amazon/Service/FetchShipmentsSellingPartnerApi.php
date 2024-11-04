<?php

namespace Amazon\Service;

use Amazon\Entity\ShipmentItems;
use Amazon\Entity\Shipments;
use Doctrine\ORM\EntityManagerInterface;
use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;

class FetchShipmentsSellingPartnerApi
{
    private $entityManager;

    private $apiConnector;

    private $marketplaceId;

    private $shipmentIdtoShipmentData;

    public function __construct(EntityManagerInterface $entityManager, Config $config)
    {
        $this->entityManager = $entityManager;

        $configOptions = $config->getConfig();
        //A1OHGF3S9PRL28
        $this->apiConnector = SellingPartnerApi::seller(
            clientId: $configOptions['clientId'],
            clientSecret: $configOptions['clientSecret'],
            refreshToken: $configOptions['refreshToken'],
            endpoint: Endpoint::EU
        );

        $this->marketplaceId = $configOptions['marketplaceId'];
    }

    function fetchShipmentData()
    {
    
        $shipments = $this->getAllShipments();

        foreach ($shipments as $shipmentObjectData) {

            $shipmentObject = $this->entityManager->getRepository("Amazon\Entity\Shipments")->findByShipmentId($shipmentObjectData->shipmentId); 
            if (empty($shipmentObject)) {
                $shipmentObject = new Shipments();
            }
    
            $shipmentObject->setAmazonShipmentId($shipmentObjectData->shipmentId);
            $shipmentObject->setShipmentName($shipmentObjectData->shipmentName);
            $shipmentObject->setDestinationFulfillmentCenterId($shipmentObjectData->destinationFulfillmentCenterId);
            $shipmentObject->setShipmentStatus($shipmentObjectData->shipmentStatus);
            $shipmentObject->setLabelPrepType($shipmentObjectData->labelPrepType);
            $shipmentObject->setAreCasesRequired($shipmentObjectData->areCasesRequired);
    
            $this->entityManager->persist($shipmentObject);
            $this->entityManager->flush();
        }


        $query = "select * from shipments";
        $shipments = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();
        
        foreach ($shipments as $shipment) {
            $this->getItemsInShipment($shipment['amazon_shipment_id']);
        }

    }
    
    function getAllShipments() {
      
        $shipmentStatusList = [
            'WORKING',
            'READY_TO_SHIP',
            'SHIPPED',
            'RECEIVING',       
            'IN_TRANSIT',
            'CANCELLED',
            'DELETED',
            'CLOSED',
            'ERROR',
            'IN_TRANSIT',
            'DELIVERED',
            'CHECKED_IN'
        ];
       
        $shipments = $this->apiConnector->fbaInboundV0()->getShipments('ShipmentStatusList', $this->marketplaceId, $shipmentStatusList);
    
        return $shipments->dto()->payload->shipmentData;
    }

    function getItemsInShipment($shipmentId) {

        $itemsInShipment = $this->apiConnector->fbaInboundV0()->getShipmentItemsByShipmentId($shipmentId, $this->marketplaceId);

        $this->shipmentIdtoShipmentData[$shipmentId] = $itemsInShipment->dto()->payload->itemData;

        foreach ( $itemsInShipment->dto()->payload->itemData as $shipmentData) {

            $shipment = $this->entityManager->getRepository(Shipments::class)->findOneBy(['amazonShipmentId' => $shipmentId]);

            $shipmentItem = $this->entityManager->getRepository(ShipmentItems::class)->findOneBy(['sellerSku' => $shipmentData->sellerSku, 
                                                                                                 'shipmentId' => $shipment->getId()]);
            if (empty($shipmentItem)) {
                $shipmentItem = new ShipmentItems();
            }

            $shipmentItem->setSellerSku($shipmentData->sellerSku);
            $shipmentItem->setQuantityShipped($shipmentData->quantityShipped);
            $shipmentItem->setQuantityReceived($shipmentData->quantityReceived);
            $shipmentItem->setQuantityInCase($shipmentData->quantityInCase);

            $shipment->addItem($shipmentItem);
           
        }

        $this->entityManager->persist($shipment);
        $this->entityManager->flush();
    }

}