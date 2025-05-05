<?php

namespace Amazon\Service;

use Amazon\Entity\AmazonItemsOutOfStock;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Restock
{

    private $allListings;

    private $entityManager;

    private $skuToStockLevel;

    public $oldToNewSku = ['33-AMWN-QJ58' => '9902059200015',
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
                            'UJ-7O2I-ZERL' => '9902184269741',
                            'O4-BV6P-9HAM' => '6009714352751',
                            'X1-0699-UCAI' => '9902191827781',
                            '7I-TH2H-Y5KU' => '9901057582284',
                            '52-E1DW-GS65' => '9902191777581',
                            'AQ-4REO-KFVT' => '9902080318291',
                            'GC-XYMZ-5S0R' => '9902000530543',
                            'SA-7NPH-GSL7' => '9902031785844',
                            'TI-03E1-ZYV1' => '9901058590691',
                            'YO-6ZMU-GT41' => '9902191777574',
                            '1Y-U1DS-21UA' => '9902096421114',
                            '2M-U4PM-DSUP' => '9902184271850',
                            'MM-V8W9-X6WW' => '9902041402359',
                            'BO-8OQZ-7B0Q' => '9902029501470',
                            'F9-STTC-0Z67' => '9900572245629',
                            'GL-D720-M9OP' => '9902160328851',
                            'D8-UYO5-MKU4' => '9900572245636',
                            'X3-AMMN-ZVYN' => '9901058683461',
                            'JG-45M9-PIVN' => '9902043374586',
                            'F5-Z1YH-Y9VB' => '9902191777543',
                            'IE-3VVK-3SKU' => '9902041193899',
                            'WI-5LEM-581' => '9901059104873',
                            '6T-OM6X-FH4U' => '9902079153483',
                            'XC-RNEN-83MP' => '9902191827804',
                            '5E-BFR3-LXI8' => '9902191827798',
                            'X3-31GF-NTZD' => '9902191827767',
                            '35-Q33W-TEYN' => '9902191827774',
                            'WC-XKO8-G2QZ' => '9902018751275',
                            'T4-SAT5-FV4Q' => '9902080318499',
                            'BO-8OQZ-7B0Q' => '9902029501470',
                            '3D-4IKD-R3E7' => '9902017708256',
                            'JG-45M9-PIVN' => '9902043374586',
                            'VN-NYXX-CH92' => '9902176777179',
                            '3G-9ND1-1AFV' => '9902191777543',
                            'UM-J6B2-VLGM' => '9902191777598',
                            'Y1-2KH8-ERGT' => '9902191777567',
                            'F5-Z1YH-Y9VB' => '9902191777543',
                            '1I-YW9I-69GZ' => '9902191777574',
                            'YH-8HXY-2R31' => '9900959435902',
                            'TK-GPOI-T7QQ' => '9901059104873',
                            'WI-5LEM-581W' => '9901058683461',
                            'XF-NAZ4-Q6XE' => '9902191777598',
                            '8J-Y2OJ-5X9E' => '9902033213901',
                            'LS-F0MT-Z8EU' => '9902079577227',
                            'ZX-ADX2-0O4S' => '9902071695790',
                            'VT-Q5UF-8KOX' => ''
                            ];

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

        $query = "truncate amazonItemsOutOfStock";
        $this->entityManager->getConnection()->prepare($query)->executeQuery();
        $actualItemsToRestock = [];
        foreach ($skusToRestock as $sku => $item) {

            //great for debugging purposes
            if ($sku != '9902031717920') {
               // continue;
            }

            //echo "testing " . PHP_EOL;

            if ($this->stockOfItem($sku) == 0) {
                $amazonItemsOutOfStock = new AmazonItemsOutOfStock();
                $amazonItemsOutOfStock->setProductName($item);
                $amazonItemsOutOfStock->setSellerSku($sku);

                $this->entityManager->persist($amazonItemsOutOfStock);
                $this->entityManager->flush();

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
                    //unset($this->skuToStockLevel[$takealotBarcode]);
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

        $query = "select item_name,seller_sku from listing where quantity = 0 and fulfilment_channel = 'AMAZON_EU'";

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
