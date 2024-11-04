<?php

namespace Amazon\Service;

use Amazon\Entity\Listing;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Http\Message\RequestInterface;
use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;

class InventorySellingPartnerApi
{
    private $entityManager;

    private $apiConnector;

    private $marketplaceId;

    public function __construct(EntityManagerInterface $entityManager,Config $config)
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

    function fetchInventoryData()
    {
        // How does listing get populated. 
        // NOTE : Good for debugging different api
        //$listings[0]['seller_sku'] = 'BK-PT6K-RR12';
        //$listings[1]['seller_sku'] = '2S-R7YY-I5JK';

        $query = "select * from listing where last_updated_date < DATE_SUB(NOW(), INTERVAL 6 HOUR) or last_updated_date is null";
        $listings = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();
        
        // unset($listings);
        // $listings[0]['seller_sku'] = '9902066253660';

        foreach ($listings as $listing) {
            $summary = $this->getAllInventoryForSku($listing['seller_sku']);

            if ($summary === FALSE) {
                continue;
            }

            $listingObject = $this->entityManager->getRepository("Amazon\Entity\Listing")->findBySellerSku($listing['seller_sku']); 
            if (empty($listingObject)) {
                $listingObject = new Listing();
            }

            $listingObject->setQuantity($summary['quantity']);
            $listingObject->setLastUpdatedDate(new \DateTime());

            $this->entityManager->persist($listingObject);
            $this->entityManager->flush();

            sleep(1);
            echo $listing['seller_sku'] . PHP_EOL;
        }
    }
    
    function getAllInventoryForSku($sellerSku) {
        
        $inventoryForSku = []; 
        //This doesn't for whatever reasons give quantity on Amazon 
        try {
            $listingItem = $this->apiConnector->fbaInventoryV1()->getInventorySummaries("Marketplace", $this->marketplaceId, [$this->marketplaceId], null, null, null, $sellerSku);
        } catch (Exception $e) {
            echo $e->getMessage();
            return FALSE;
        }
       
        
        if (!empty($listingItem->dto()->payload->inventorySummaries)) {

            // debugging 
            // print_r($listingItem->dto()->payload->inventorySummaries[0]);
            // exit();

            $inventoryForSku['quantity'] = $listingItem->dto()->payload->inventorySummaries[0]->totalQuantity;

            return $inventoryForSku;
        }

        $listingItem = $this->apiConnector->listingsItemsV20210801();
        $listingItemResult = $listingItem->getListingsItem('A1OHGF3S9PRL28', $sellerSku, [$this->marketplaceId], 'en_US', ['fulfillmentAvailability']);
        $listingItemResultDto = $listingItemResult->dto();

        if (!empty( $listingItemResultDto->fulfillmentAvailability)) {
            $inventoryForSku['fulfillmentChannelCode'] = $listingItemResultDto->fulfillmentAvailability[0]->fulfillmentChannelCode;
            $inventoryForSku['quantity'] =  $listingItemResultDto->fulfillmentAvailability[0]->quantity;
    
            return $inventoryForSku;
        }

        return FALSE;
        
        // This below code leaving here as its hard to work out but for some reasons it only gave items with inventory
        // Tell you nothing about FBM items and stuff with no stock
        // $inventoryApi = 

        // $granularityType = 'Marketplace';

        // $allSummaries = [];
        // $nextToken = null;

        // $startDate = new DateTime();
        // $startDate->sub(new DateInterval('P1M'));    
    
        // do {
        //     try {
    
        //         $result = $inventoryApi->getInventorySummaries(
        //             "Marketplace", $this->marketplaceId, [$this->marketplaceId], null, $startDate, null, null, $nextToken
        //         );
          
        //         $summaries = $result->dto()->payload->inventorySummaries ?? [];
        //         $allSummaries = array_merge($allSummaries, $summaries);
    
        //         $nextToken = $result->dto()->pagination->nextToken ?? null;
        //     } catch (Exception $e) {
        //         echo 'Exception when calling FbaInventoryApi->getInventorySummaries: ', $e->getMessage(), PHP_EOL;
        //         break;
        //     }
        // } while ($nextToken);
    
        // return $allSummaries;
    }

}
