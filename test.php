<?php
require __DIR__ . '/functions.php';
$GLOBALS["working_currency"]="USD";//define set currency to use--------------------NEEDED--------------------     
$GLOBALS["currency_rate"] = currency_conversion($GLOBALS["working_currency"]); //calculate currency exchange rates (works out 1 > other currencies. In this case will call api to check what £1 is in USD, EUR)--------------------NEEDED--------------------

echo "<pre>";
print_r(create_tax_data("./tax-tables.json","GBP")); //define tax table to use --------------------NEEDED--------------------
echo "</pre>";


?>