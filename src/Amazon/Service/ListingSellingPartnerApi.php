<?php

namespace Amazon\Service;

use Amazon\Entity\Listing;
use Amazon\Entity\Report;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SellingPartnerApi\SellingPartnerApi;
use SellingPartnerApi\Enums\Endpoint;
use SellingPartnerApi\Seller\ReportsV20210630\Dto\CreateReportSpecification;
use Amazon\Service\Config;
use Amazon\Repository\ReportRepository;

class ListingSellingPartnerApi
{
    private $entityManager;

    private $apiConnector;

    private $marketplaceId;

    public function __construct(EntityManagerInterface $entityManager,Config $config)
    {
        $this->entityManager = $entityManager;

        $configOptions = $config->getConfig();

        $this->apiConnector = SellingPartnerApi::seller(
            clientId: $configOptions['clientId'],
            clientSecret:$configOptions['clientSecret'],
            refreshToken: $configOptions['refreshToken'],
            endpoint: Endpoint::EU
        );

        $this->marketplaceId = $configOptions['marketplaceId'];
    }

    function generateReport($reportType, $action)
    {
        $listingApi = $this->apiConnector->reportsV20210630();
        $reportSpecification = new CreateReportSpecification("GET_MERCHANT_LISTINGS_ALL_DATA", ['AE08WJ6YKNBMC']);
        $result = $listingApi->createReport($reportSpecification);
        
        $report = new Report();
        $report->setAmazonReportId($result->dto()->reportId);

        $this->entityManager->persist($report);
        $this->entityManager->flush();

        return $result->dto()->reportId;
    }

    public function getReports() {

        $reports = $this->entityManager->getRepository("Amazon\Entity\Report")->findByStatusNull(); 
        
        return $reports;
    }
    
    public function getReportByAmazonId($id) {

        $report = $this->entityManager->getRepository("Amazon\Entity\Report")->findByAmazonReportId($id);

        return $report;
    }
    
    public function getLast5CreatedReports() {

        return $this->entityManager->createQueryBuilder('r')
                    ->orderBy('r.createdTime', 'DESC')
                    ->setMaxResults(5)
                    ->getQuery()
                    ->getResult();

    }

    function fetchReport($reportType, $action, $amazonReportId)
    {
        $listingApi = $this->apiConnector->reportsV20210630();
        $response = $listingApi->getReport($amazonReportId);

        if (empty($response->dto()->processingEndTime)) {
            return FALSE;
        }

        $report = $this->entityManager->getRepository("Amazon\Entity\Report")->findByAmazonReportId($amazonReportId); 

        $report->setAmazonReportId($amazonReportId);
        $report->setCreatedTime($response->dto()->createdTime);
        $report->setDataEndTime($response->dto()->dataEndTime);
        $report->setDataStartTime($response->dto()->dataStartTime);
        $report->setMarketplaceId($response->dto()->marketplaceIds[0]);
        $report->setProcessingEndTime($response->dto()->processingEndTime);
        $report->setProcessingStartTime($response->dto()->processingStartTime);
        $report->setProcessingStatus($response->dto()->processingStatus);
        $report->setReportDocumentId($response->dto()->reportDocumentId);
        //$report->setReportScheduleId($response->dto()->reportScheduleId);

        $this->entityManager->persist($report);
        $this->entityManager->flush();

        return $response->dto()->reportDocumentId;
    }

    function downloadReport($reportId) {
        
        $listingApi = $this->apiConnector->reportsV20210630();

        $report = $this->entityManager->getRepository("Amazon\Entity\Report")->findByAmazonReportId($reportId);    

        $response = $listingApi->getReportDocument($report->getReportDocumentId(), 'GET_MERCHANT_LISTINGS_ALL_DATA');
        $reportDocument = $response->dto();
        $contents = $reportDocument->download('GET_MERCHANT_LISTINGS_ALL_DATA');
        
        foreach ($contents as $listing) {
            //print_r($listing)

            $listingObject = $this->entityManager->getRepository("Amazon\Entity\Listing")->findBySellerSku($listing['seller-sku']); 
            if ($listingObject == FALSE) {
                $listingObject = new Listing();
            }
            $listingObject->setItemName($listing['item-name']);
            $listingObject->setItemDescription($listing['item-description']);
            $listingObject->setListingId($listing['listing-id']);
            $listingObject->setSellerSku($listing['seller-sku']);
            $listingObject->setAsin1($listing['asin1']);
            $listingObject->setPrice((float)$listing['price']);
            $listingObject->setFulfilmentChannel($listing['fulfillment-channel']);
            $listingObject->setProductId($listing['product-id']);
            //$listingObject->setOpenDate($listing['open-date']);

            $this->entityManager->persist($listingObject);
        }

        $this->entityManager->flush();
    }
            
}