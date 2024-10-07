<?php

namespace Amazon\Service;

use Doctrine\ORM\EntityManagerInterface;


class Restock
{

    private $allListings;

    private $entityManager;



    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function play()
    {

        //get all products with sales
        $query = "select seller_sku,item_name,quantity,fulfilment_channel from listing";
        $rows = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();

        $this->allListings = [];

        foreach ($rows as $row) {
            $this->allListings[$row['seller_sku']] = $row;
        }

        echo "Seller SKU, Item, Stock ". PHP_EOL;

        foreach ($this->allListings as $sellerSKU => $listing) {
            
            if (!$this->FBMListing($sellerSKU)) {
                continue;
            }

            $nonFBMSKU = $this->getAssumedNoFBABarcode($sellerSKU);
        
            if (!$this->listingExistsAsNonFBM($nonFBMSKU)) {
                continue;
            }

            if ($this->listingHasStock($this->allListings[$nonFBMSKU])) {
                continue;
            }

            
            //items to stock at Amazon
            echo $this->allListings[$nonFBMSKU]['seller_sku'] . "," . $this->allListings[$nonFBMSKU]['item_name'] . "," . $this->allListings[$nonFBMSKU]['quantity'] . PHP_EOL;

        }
    }

    public function run() {

        //get all the items that are on there way to Amazon.
        $skusOnTheWayToAmazon = $this->getSkusOnTheWayToAmazon();
        
        print_r($skusOnTheWayToAmazon);
        
        $skusToRestock = $this->getFBAItemsWithNoStockAndHaveSalesHistory();
        
        foreach ($skusToRestock as $item) {

            if (array_search($item, $skusOnTheWayToAmazon) === FALSE) {
                echo $item . PHP_EOL;
            }

        }
        
        

    }

    private function getFBAItemsWithNoStockAndHaveSalesHistory() {

        $query = "select seller_sku from listing where quantity = 0 and fulfilment_channel = 'AMAZON_EU' " . 
                 "and seller_sku in (select sellerSku from amazonOrderItems); ";
        $rows = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();

        foreach ($rows as $row) {
            $skusToRestock[] = $row['seller_sku'];
        }

        return $skusToRestock;

    }

    private function getSkusOnTheWayToAmazon() {
        $query = "select si.seller_sku from shipments s " . 
                 "inner join shipment_items si on s.id = si.shipment_id where s.shipment_status in ('RECEIVING','SHIPPED');";
        $rows = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();

        foreach ($rows as $row) {
            $skusOnTheyWayToAmazon[] = $row['seller_sku'];
        }

        return $skusOnTheyWayToAmazon;

    }

    private function getAssumedNoFBABarcode($sellerSKU) {
        $parts = explode("_FBM", $sellerSKU);
        $nonFBMSKU = $parts[0];

        return $nonFBMSKU;
    }

    private function FBMListing($key) {

        if (stristr($key, "FBM") !== FALSE) {
            return TRUE;
        }

        return FALSE;
    }

    private function listingExistsAsNonFBM($nonFBMSKU) {
        
        if (!empty($this->allListings[$nonFBMSKU])) {
            return TRUE; 
        }

        return FALSE;
    }

    private function listingHasStock($listing) {
        
        if ($listing['quantity'] > 0) {
            return TRUE;
        }
        
        return FALSE;
    }
}
