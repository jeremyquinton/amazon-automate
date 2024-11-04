Using this software
===================
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