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
                  $query = "update amazonOrders set orderStatus = ? where amazonOrderId = ?";
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