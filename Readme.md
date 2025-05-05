Using this software
===================

App Credentials -> https://sellercentral.amazon.co.za/sellingpartner/developerconsole/ref=xx_DevCon_dnav_xx#



Deployment of Software
======================
This file right at the top I have added two lines
vendor/jlevers/selling-partner-api/src/Traits/Deserializes.php
 'Y-m-d\TH:i:s.u\Z',
 ''
 
Need to get the library patched. For now we use the deployment tool to exclude this file.
Should write a bug report for this at some point

This software does the following

It gets all our listings from Amazon using reports that amazon give us. We need to get all our listings from Amazon first and foremost. 
amazon:fetchreport Fetches a Report from Amazon. The report at the moment is just all our listings from Amazon
php bin/console amazon:fetchreport listings --action=generate
php bin/console amazon:fetchreport listings --action fetch --reportId 50078019997

These commands will most like be run manually 

Command below fetches all our inventory at Amazon aka how many items we have there for each product. 
amazon:fetchinventory                      Fetches Our Inventory or Stock Level Info From Amazon.
php bin/console amazon:fetchinventory


Command below fetches sales at Amazon aka how many orders we have in the last x days. 
amazon:fetchorders                         Fetches Our Order data from Amazon.
php bin/console amazon:fetchorders --period 30


amazon:fetchshipments                     Fetches Our Order data from Amazon.
php bin/console amazon:fetchorders 

amazon:restock                            Creates a restock based on sales.
php bin/console 

19 February 2025
//we are removing all items with barcodes like ZP-SVF0-395M from amazon.
//this means we need to remove that item from the listing table. 
//this means we need to create the correc item in the listing table. 
//items that we are fixing

33-AMWN-QJ58_MF
7Y-2MVT-F1WD_FBM
9H-9FI9-1CQI_FBM
BC-A1Y5-XF2H_FBM
BO-56VC-1L6V_FBM
OQ-S286-Y0CD_FBM
Q8-RAFZ-R0W9_FBM
S1-4PVD-2LOK_FBM
S8-PNIN-HAXN_FBM
TM-IYOM-GEDL_FBM
UC-NCYR-WZ6A_FBM
UV-A1R7-Y48I_FBM
V7-Q5D8-E1D0_FBM
VM-6LU7-J8N9_FBM
XU-OT8W-M8WJ_FBM
Y1-2KH8-ERGT_FBM
YH-8HXY-2R31_FBM
ZP-SVF0-395M_FBM
0B-U0VE-WTN6_FBM
2Y-ZS5R-7551_FBM
4S-F81Y-JBH7_FBM
6009714350580_FBM
AO-2JBR-84XW_FBM
B0CYLQSX5Z
B7-7KZ6-CJFQ_FBM
CW-YTKK-6DZ1_FBM
DW-L4G6-GKR0_MF
HR-7H66-FA6Q_FBM
JA-SE4O-0THT_FBM
JQ-Z5MS-GK4N_FBM
MF-NP2Q-1SNS_FBM
NQ-L00C-1W8O_FBM
NR-C5DS-QUV8_FBM
O8-UAU6-6KIW_FBM
OP-FR8M-UTQK_FBM
PZ-N1TP-OI27_FBM
UJ-7O2I-ZERL_FBM
BC-A1Y5-XF2H_FBM


delete from listing where seller_sku='33-AMWN-QJ58_MF';
delete from listing where seller_sku='33-AMWN-QJ58';
delete from amazon_takealot_barcode_lookup where amazon_barcode='33-AMWN-QJ58';
delete from listing where seller_sku='7Y-2MVT-F1WD_FBM';
delete from listing where seller_sku='7Y-2MVT-F1WD';
delete from amazon_takealot_barcode_lookup where amazon_barcode='7Y-2MVT-F1WD';
delete from listing where seller_sku='9H-9FI9-1CQI_FBM';
delete from listing where seller_sku='9H-9FI9-1CQI';
delete from amazon_takealot_barcode_lookup where amazon_barcode='9H-9FI9-1CQI';
delete from listing where seller_sku='BC-A1Y5-XF2H_FBM';
delete from listing where seller_sku='BC-A1Y5-XF2H';
delete from amazon_takealot_barcode_lookup where amazon_barcode='BC-A1Y5-XF2H';
delete from listing where seller_sku='BO-56VC-1L6V_FBM';
delete from listing where seller_sku='BO-56VC-1L6V';
delete from amazon_takealot_barcode_lookup where amazon_barcode='BO-56VC-1L6V';
delete from listing where seller_sku='OQ-S286-Y0CD_FBM';
delete from listing where seller_sku='OQ-S286-Y0CD';
delete from amazon_takealot_barcode_lookup where amazon_barcode='OQ-S286-Y0CD';
delete from listing where seller_sku='Q8-RAFZ-R0W9_FBM';
delete from listing where seller_sku='Q8-RAFZ-R0W9';
delete from amazon_takealot_barcode_lookup where amazon_barcode='Q8-RAFZ-R0W9';
delete from listing where seller_sku='S1-4PVD-2LOK_FBM';
delete from listing where seller_sku='S1-4PVD-2LOK';
delete from amazon_takealot_barcode_lookup where amazon_barcode='S1-4PVD-2LOK';
delete from listing where seller_sku='S8-PNIN-HAXN_FBM';
delete from listing where seller_sku='S8-PNIN-HAXN';
delete from amazon_takealot_barcode_lookup where amazon_barcode='S8-PNIN-HAXN';
delete from listing where seller_sku='TM-IYOM-GEDL_FBM';
delete from listing where seller_sku='TM-IYOM-GEDL';
delete from amazon_takealot_barcode_lookup where amazon_barcode='TM-IYOM-GEDL';
delete from listing where seller_sku='UC-NCYR-WZ6A_FBM';
delete from listing where seller_sku='UC-NCYR-WZ6A';
delete from amazon_takealot_barcode_lookup where amazon_barcode='UC-NCYR-WZ6A';
delete from listing where seller_sku='UV-A1R7-Y48I_FBM';
delete from listing where seller_sku='UV-A1R7-Y48I';
delete from amazon_takealot_barcode_lookup where amazon_barcode='UV-A1R7-Y48I';
delete from listing where seller_sku='V7-Q5D8-E1D0_FBM';
delete from listing where seller_sku='V7-Q5D8-E1D0';
delete from amazon_takealot_barcode_lookup where amazon_barcode='V7-Q5D8-E1D0';
delete from listing where seller_sku='VM-6LU7-J8N9_FBM';
delete from listing where seller_sku='VM-6LU7-J8N9';
delete from amazon_takealot_barcode_lookup where amazon_barcode='VM-6LU7-J8N9';
delete from listing where seller_sku='XU-OT8W-M8WJ_FBM';
delete from listing where seller_sku='XU-OT8W-M8WJ';
delete from amazon_takealot_barcode_lookup where amazon_barcode='XU-OT8W-M8WJ';
delete from listing where seller_sku='Y1-2KH8-ERGT_FBM';
delete from listing where seller_sku='Y1-2KH8-ERGT';
delete from amazon_takealot_barcode_lookup where amazon_barcode='Y1-2KH8-ERGT';
delete from listing where seller_sku='YH-8HXY-2R31_FBM';
delete from listing where seller_sku='YH-8HXY-2R31';
delete from amazon_takealot_barcode_lookup where amazon_barcode='YH-8HXY-2R31';
delete from listing where seller_sku='ZP-SVF0-395M_FBM';
delete from listing where seller_sku='ZP-SVF0-395M';
delete from amazon_takealot_barcode_lookup where amazon_barcode='ZP-SVF0-395M';
delete from listing where seller_sku='0B-U0VE-WTN6_FBM';
delete from listing where seller_sku='0B-U0VE-WTN6';
delete from amazon_takealot_barcode_lookup where amazon_barcode='0B-U0VE-WTN6';
delete from listing where seller_sku='2Y-ZS5R-7551_FBM';
delete from listing where seller_sku='2Y-ZS5R-7551';
delete from amazon_takealot_barcode_lookup where amazon_barcode='2Y-ZS5R-7551';
delete from listing where seller_sku='4S-F81Y-JBH7_FBM';
delete from listing where seller_sku='4S-F81Y-JBH7';
delete from amazon_takealot_barcode_lookup where amazon_barcode='4S-F81Y-JBH7';
delete from listing where seller_sku='6009714350580_FBM';
delete from listing where seller_sku='6009714350580';
delete from amazon_takealot_barcode_lookup where amazon_barcode='6009714350580';
delete from listing where seller_sku='AO-2JBR-84XW_FBM';
delete from listing where seller_sku='AO-2JBR-84XW';
delete from amazon_takealot_barcode_lookup where amazon_barcode='AO-2JBR-84XW';
delete from listing where seller_sku='B7-7KZ6-CJFQ_FBM';
delete from listing where seller_sku='B7-7KZ6-CJFQ';
delete from amazon_takealot_barcode_lookup where amazon_barcode='B7-7KZ6-CJFQ';
delete from listing where seller_sku='CW-YTKK-6DZ1_FBM';
delete from listing where seller_sku='CW-YTKK-6DZ1';
delete from amazon_takealot_barcode_lookup where amazon_barcode='CW-YTKK-6DZ1';
delete from listing where seller_sku='DW-L4G6-GKR0_MF';
delete from listing where seller_sku='DW-L4G6-GKR0';
delete from amazon_takealot_barcode_lookup where amazon_barcode='DW-L4G6-GKR0';
delete from listing where seller_sku='HR-7H66-FA6Q_FBM';
delete from listing where seller_sku='HR-7H66-FA6Q';
delete from amazon_takealot_barcode_lookup where amazon_barcode='HR-7H66-FA6Q';
delete from listing where seller_sku='JA-SE4O-0THT_FBM';
delete from listing where seller_sku='JA-SE4O-0THT';
delete from amazon_takealot_barcode_lookup where amazon_barcode='JA-SE4O-0THT';
delete from listing where seller_sku='JQ-Z5MS-GK4N_FBM';
delete from listing where seller_sku='JQ-Z5MS-GK4N';
delete from amazon_takealot_barcode_lookup where amazon_barcode='JQ-Z5MS-GK4N';
delete from listing where seller_sku='MF-NP2Q-1SNS_FBM';
delete from listing where seller_sku='MF-NP2Q-1SNS';
delete from amazon_takealot_barcode_lookup where amazon_barcode='MF-NP2Q-1SNS';
delete from listing where seller_sku='NQ-L00C-1W8O_FBM';
delete from listing where seller_sku='NQ-L00C-1W8O';
delete from amazon_takealot_barcode_lookup where amazon_barcode='NQ-L00C-1W8O';
delete from listing where seller_sku='NR-C5DS-QUV8_FBM';
delete from listing where seller_sku='NR-C5DS-QUV8';
delete from amazon_takealot_barcode_lookup where amazon_barcode='NR-C5DS-QUV8';
delete from listing where seller_sku='O8-UAU6-6KIW_FBM';
delete from listing where seller_sku='O8-UAU6-6KIW';
delete from amazon_takealot_barcode_lookup where amazon_barcode='O8-UAU6-6KIW';
delete from listing where seller_sku='OP-FR8M-UTQK_FBM';
delete from listing where seller_sku='OP-FR8M-UTQK';
delete from amazon_takealot_barcode_lookup where amazon_barcode='OP-FR8M-UTQK';
delete from listing where seller_sku='PZ-N1TP-OI27_FBM';
delete from listing where seller_sku='PZ-N1TP-OI27';
delete from amazon_takealot_barcode_lookup where amazon_barcode='PZ-N1TP-OI27';
delete from listing where seller_sku='UJ-7O2I-ZERL_FBM';
delete from listing where seller_sku='UJ-7O2I-ZERL';
delete from amazon_takealot_barcode_lookup where amazon_barcode='UJ-7O2I-ZERL';
delete from listing where seller_sku='O4-BV6P-9HAM_FBM';
delete from listing where seller_sku='O4-BV6P-9HAM';

delete from amazon_takealot_barcode_lookup where amazon_barcode='O4-BV6P-9HAM';
delete from amazon_takealot_barcode_lookup where amazon_barcode='BC-A1Y5-XF2H';

//need to also build the array in the amazonOrderItems in the old to new sku

//https://www.amazon.co.za/dp/B0DK5T4F4Q?th=1 no takealot list ? - correct the listing
