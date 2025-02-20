<?php

namespace Amazon\Service;

use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Restock
{

    private $allListings;

    private $entityManager;

    private $skuToStockLevel;

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

        $skusToRestock = $this->getFBAItemsWithNoStock();

        foreach ($skusToRestock as $sku => $item) {

            //great for debugging purposes
            if ($sku != '9900964646911') {
            //    continue;
            }

            if ($this->stockOfItem($sku) == 0) {
            //    echo "no stock : " . $sku . " " . $item . PHP_EOL;
                continue;
            }    
            $actualItemsToRestock[] = $sku;
        }

        $barcodeList = "('" . implode("','", $actualItemsToRestock) . "')";
       
        $query = "select sellerSku,count(*) as total from amazonOrderItems where sellerSku in " . $barcodeList . " group by sellerSku";
        $skuToCount = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();

        $skuToCount = array_combine(array_column($skuToCount, 'sellerSku'), array_column($skuToCount, 'total'));
    
        foreach ($actualItemsToRestock as $itemToRestock) {
             
            if (array_key_exists($itemToRestock, $skuToCount) !== FALSE) {
                if ($skuToCount[$itemToRestock] > 1) {
                    echo $itemToRestock .",2" . PHP_EOL; 
                }    
            } else {
               echo $itemToRestock .",1" . PHP_EOL; 
            }

        }


    }

    /**
     * Have an issue when trying to manipulate an Amazon excel template with php spreadsheet so for now will make a cut and paste something
     * 
     * 
     * 
     * @return void 
     * @throws \Doctrine\DBAL\Exception 
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception 
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception 
     * @throws \Psr\SimpleCache\InvalidArgumentException 
     * @throws \PhpOffice\PhpSpreadsheet\Exception 
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception 
     * @throws \DivisionByZeroError 
     * @throws \ArithmeticError 
     */
    public function run_backup() {

        //get all the items that are on there way to Amazon.
        $skusOnTheWayToAmazon = $this->getSkusOnTheWayToAmazon();
        //$skusOnTheWayToAmazon = $this->getSkusOnTheWayToAmazon();
        //$skusOnTheWayToAmazon = [];

        //This here need to check 
        $skusToRestock = $this->getFBAItemsWithNoStock();
        
        $bulkFile = getcwd() . "/ManifestFileUpload_Template_MPL.xlsx";
        
        $spreadsheet = IOFactory::load($bulkFile);
        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");

        $sheet = $spreadsheet->getSheetByName("Create workflow – template");

        $sheetData = $sheet->toArray(null, true, true, true);

        $counter = 9;
        $itemRestockCounter = 0;
        foreach ($skusToRestock as $sku => $itemName) {

            if ($this->stockOfItem($skusToRestock) == 0) {
                echo "no stock : " . $skusToRestock . PHP_EOL;
                continue;
            }

            // This is almost a double check so ignoring for now
            // if (array_search($item, $skusOnTheWayToAmazon) !== FALSE) {
            //     continue;           
            // }
            echo $skusToRestock . PHP_EOL;
            $sheet->setCellValue('A' . $counter, $skusToRestock);
            $sheet->setCellValue('B' . $counter, 1);

            $itemRestockCounter++;
            $counter++;
        }

        $filename = "ManifestFileUpload_Template_MPL_" . date("Y_m_d") .".xlsx";


        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $filepath = "/var/www/takealot-sales/restock_files/" . $filename;
        
        $dateTime = new \DateTime();
        $date = $dateTime->format('Y-m-d');

        $this->addFileInfo('amazon', $filepath, $filename, $itemRestockCounter, $date);

        $writer->save($filepath);
    
    }

    private function addFileInfo($warehouse, $filepath, $filename, $counter, $date) {
        $query = "select * from bulkRestockFiles where warehouse = ? and filename = ? and filedate = ?";
        $rows = $this->entityManager->getConnection()->prepare($query)->executeQuery([$warehouse, $filename, $date])->fetchAllAssociative();
        if (empty($rows)) {
            $query = "insert into bulkRestockFiles(warehouse, filepath, filename, counter, filedate) " .
                     "values (?, ?, ?, ?, ?) ";
            $this->entityManager->getConnection()->prepare($query)->executeQuery([$warehouse, $filepath, $filename, $counter, $date]);          
        } 
    }

    private function stockOfItem($sku) {

        if (empty($this->skuToStockLevel)) {
            
            $query = "select sku, my_soh from bulk_replenishment_table";
            $rows = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();
            foreach ($rows as $row) {
                $this->skuToStockLevel[$row['sku']] = $row['my_soh'];
            }

            $query = "select amazon_barcode, takealot_barcode from amazon_takealot_barcode_lookup";
            $rows = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();
            foreach ($rows as $row) {
                $skuTakealotToAmazon[$row['takealot_barcode']] = $row['amazon_barcode'];
            }

            foreach ($skuTakealotToAmazon as $takealotBarcode => $amazonBarcode) {
                if (array_key_exists($takealotBarcode,  $this->skuToStockLevel)) {
                    $this->skuToStockLevel[$amazonBarcode] = $this->skuToStockLevel[$takealotBarcode];
                    unset($this->skuToStockLevel[$takealotBarcode]);
                }
            }

        }

        if (array_key_exists($sku, $this->skuToStockLevel) !== FALSE) {
            return $this->skuToStockLevel[$sku];
        }

        return 0;
    }

    private function getFBAItemsWithNoStockAndHaveSalesHistory() {

        $query = "select seller_sku from listing where quantity = 0 and fulfilment_channel = 'AMAZON_EU' " . 
                 "and seller_sku in (select sellerSku from amazonOrderItems); ";

        //$query = "select seller_sku from listing";  
        $rows = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();

        foreach ($rows as $row) {
            $skusToRestock[] = $row['seller_sku'];
        }

        return $skusToRestock;

    }

    private function getFBAItemsWithNoStock() {

        $query = "select item_name,seller_sku from listing where quantity = 0 and fulfilment_channel = 'AMAZON_EU' ";

        //$query = "select seller_sku from listing";  
        $rows = $this->entityManager->getConnection()->prepare($query)->executeQuery()->fetchAllAssociative();

        foreach ($rows as $row) {
            $skusToRestock[$row['seller_sku']] = $row['item_name'];
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
