<?php

namespace Amazon\Service;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Http\Message\RequestInterface;
use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;

class OrdersSellingPartnerApi
{
    private $entityManager;

    private $apiConnector;

    private $orders;

    private $ordersToOrderItems;

    //6009714350580
    private $oldToNewSku = ['33-AMWN-QJ58' => '9902059200015',
                            '7Y-2MVT-F1WD' => '9902184170788',
                            '9H-9FI9-1CQI' => '9902164116805',
                            'BC-A1Y5-XF2H' => '9900964646911',
                            'BO-56VC-1L6V' => '9902182412477',
                            'OQ-S286-Y0CD' => '9902184919844',
                            'Q8-RAFZ-R0W9' => '9901038524234',
                            'S1-4PVD-2LOK' => '9902185223315',
                            'S8-PNIN-HAXN' => '9902031717920',
                            'TM-IYOM-GEDL' => '9902183941686',
                            'UC-NCYR-WZ6A' => '9902041198764',
                            'UV-A1R7-Y48I' => '9902141892708',
                            'V7-Q5D8-E1D0' => '9902134249472',
                            'VM-6LU7-J8N9' => '9902186424292',
                            'XU-OT8W-M8WJ' => '9902175696570',
                            'Y1-2KH8-ERGT' => '9902191777567',
                            'YH-8HXY-2R31' => '9900959435902',
                            'ZP-SVF0-395M' => '9901080708408',
                            '0B-U0VE-WTN6' => '9901058590721',
                            '2Y-ZS5R-7551' => '9901038117924',
                            '4S-F81Y-JBH7' => '9902080318437',
                            'AO-2JBR-84XW' => '9902134557997',
                            'B7-7KZ6-CJFQ' => '9901058590929',
                            'CW-YTKK-6DZ1' => '9900962027958',
                            'DW-L4G6-GKR0' => '9902018451434',
                            'HR-7H66-FA6Q' => '9901038814403',
                            'JA-SE4O-0THT' => '9902134559809',
                            'JQ-Z5MS-GK4N' => '9902041198955',
                            'MF-NP2Q-1SNS' => '9901013173143',
                            'NQ-L00C-1W8O' => '9902044242365',
                            'NR-C5DS-QUV8' => '9902080318277',
                            'O8-UAU6-6KIW' => '9902184271850',
                            'OP-FR8M-UTQK' => '9902062631042',
                            'PZ-N1TP-OI27' => '9902027182909',
                            'UJ-7O2I-ZERL' => '9902184269741']; 

    public function __construct(EntityManagerInterface $entityManager,Config $config)
    {

        $configOptions = $config->getConfig();

        $this->entityManager = $entityManager;
        $this->apiConnector = SellingPartnerApi::seller(
            clientId: $configOptions['clientId'],
            clientSecret: $configOptions['clientSecret'],
            refreshToken: $configOptions['refreshToken'],
            endpoint: Endpoint::EU
        );
    }

    private function getPeriodAsDate($period = false)
    {

        if (stristr($period, ",") !== FALSE) {
            $parts = explode(",", $period);
            $daysAgo = strtotime($parts[0] . " days ago");
            $startDate = (new \DateTime())->setTimestamp($daysAgo);
            
            $endInDays = ($parts[0]-$parts[1]);
            $daysAgo = strtotime($endInDays . " days ago");
            $endDate = (new \DateTime())->setTimestamp($daysAgo);
            return [$startDate, $endDate];
        }

        if ($period == 'today') {
            $midnight = strtotime('Yesterday');
            $startDate = (new \DateTime())->setTimestamp($midnight);
     
            return [$startDate, null];
        }

        $integerPeriod = (int)$period;
        if ($integerPeriod == 0) {
            //amazon SA launched 01/05/2024 so get sales from then
            $startDate = (new DateTime())->setTimestamp(1714514400);

            $midnightTomorrow = strtotime('Midnight Tomorrow - 1 second');
            $endDate = (new \DateTime())->setTimestamp($midnightTomorrow);

            return [$startDate, $endDate];
        }

        //any integer assume days
        if ($integerPeriod > 0) {
            $daysAgo = strtotime($integerPeriod . " days ago");
            $startDate = (new \DateTime())->setTimestamp($daysAgo);

            return [$startDate, null];
        }
       
        
    }

    public function fetchOrderData($period) 
    {
        $dates = $this->getPeriodAsDate($period);

        $startDate = $dates[0];
        $endDate = $dates[1];

        $this->fetchOrders($startDate, $endDate);
        $this->fetchOrderItems();
        $this->processOrdersToDb();
    }

    private function checkOrderExists($amazonOrderId) {
        try {
            // Prepare the query
            $query = "SELECT * FROM amazonOrders WHERE orderId = '" . $amazonOrderId . "'";
            $rows = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();
    
        } catch (\Doctrine\DBAL\Exception $e) {
            // Handle the exception
            echo 'Database error: ' . $e->getMessage();
        }
    
        if (count($rows) > 0) {
            return ['orderExists' => TRUE, 'order' => $rows[0]];
        }
    
        return ['orderExists' => FALSE, 'order' => 'none'];
    }

    public function fetchOrders($startDate) {

        $this->orders = [];
        $nextToken = null;

        $sd = $startDate->format('Y-m-d');

        $ed = null;

        do { 
            try {

                $ordersApi = $this->apiConnector->ordersV0();
                $response = $ordersApi->getOrders(
                    createdAfter: $sd,
                    marketplaceIds: ['AE08WJ6YKNBMC'],
                    nextToken: $nextToken
                );

                $orders = $response->dto()->payload->orders ?? [];
                $this->orders = array_merge($this->orders, $orders);

                $nextToken = $response->dto()->pagination->nextToken ?? null;

               
            } catch (Exception $e) {
                echo 'Exception when calling FbaInventoryApi->getInventorySummaries: ', $e->getMessage() .  PHP_EOL;
                break;
            }
        } while ($nextToken);
        
        $this->orders;
    }

    public function fetchOrderItems() {

        $orders = [];

        foreach ($this->orders as $order) {

            $ordersApi = $this->apiConnector->ordersV0();
            $response  = $ordersApi->getOrderItems($order->amazonOrderId);
            $dto = $response->dto();

            foreach ($dto->payload->orderItems as $orderItems) {
                $this->ordersToOrderItems[$order->amazonOrderId][] = $orderItems;
            }

            sleep(2);
        } 
        //object is immutable so have to just fix database with series of sql queries
        foreach ($this->oldToNewSku as $oldSku => $newSku) {
            $query = "update amazonOrderItems set sellerSku='" . $newSku ."' where sellerSku='" . $oldSku . "'";
            $this->entityManager->getConnection()->prepare($query)->executeQuery();
        }
    }

    public function processOrdersToDb() {

        foreach ($this->orders as $order) { 

            $amazonOrderId = $order->amazonOrderId;
            $fufilmentChannel = $order->fulfillmentChannel;
            $orderDate = $order->purchaseDate;
            $apiOrderStatus = $order->orderStatus;

            if ($apiOrderStatus == 'Canceled' || $apiOrderStatus == 'Pending') {
                $orderAmount = 0;
            } else {
                $orderAmount = $order->orderTotal->amount;
            }
            
            $timestamp = strtotime($orderDate);
            $dateTime = new DateTime();
            $dateTimeString = $dateTime->setTimestamp($timestamp)->getTimestamp();
        
            $dbOrderDetails = $this->checkOrderExists($amazonOrderId);
         
            if ($dbOrderDetails['orderExists']) {
                if ($dbOrderDetails['order']['orderStatus'] != $apiOrderStatus) {
                  $query = "update amazonOrders set orderStatus = ? where orderId = ?";
                  $this->entityManager->getConnection()->prepare($query)->executeQuery([$apiOrderStatus, $amazonOrderId]);
               
                }
                continue;
            }

            try {
                $query = "insert into amazonOrders(orderId, fulfillmentChannel, purchaseDate, orderStatus, totalAmount) values (?,?,?,?,?) ";
                $stmt = $this->entityManager->getConnection()->prepare($query);
                $stmt->bindValue(1, $amazonOrderId);
                $stmt->bindValue(2, $fufilmentChannel);
                $stmt->bindValue(3, $dateTimeString);
                $stmt->bindValue(4, $apiOrderStatus);
                $stmt->bindValue(5, $orderAmount);
                $stmt->executeQuery();
                $orderItemId = $this->entityManager->getConnection()->lastInsertId();

            } catch (\Doctrine\DBAL\Exception $e) {
                echo "An error occurred: " . $e->getMessage();
            }
         
         
            foreach ($this->ordersToOrderItems[$order->amazonOrderId] as $orderItems) {
        
                $amount = (empty($orderItems->itemPrice->amount)) ? 0 : $orderItems->itemPrice->amount;

                $query = "insert into amazonOrderItems(asin, sellerSku, orderId, productName, quantity, price) values  (?,?,?,?,?,?) ";
                $stmt = $this->entityManager->getConnection()->prepare($query);
                $stmt->bindValue(1, $orderItems->asin);
                $stmt->bindValue(2, $orderItems->sellerSku);
                $stmt->bindValue(3, $orderItemId);
                $stmt->bindValue(4, $orderItems->title);
                $stmt->bindValue(5, $orderItems->quantityOrdered);
                $stmt->bindValue(6, $amount);
              
                $stmt->executeQuery();
            }
        }
    }

}