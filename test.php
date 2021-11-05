<?php
require __DIR__ . '/functions.php';

$GLOBALS["currency_rate"] = currency_conversion("gbp");
$GLOBALS["tax_data"] = create_tax_data("./tax-tables.json");
$personel = create_personel_data("GBP");
$testarr = array();

foreach($personel as $person){
    $returned_values=calculate_standard_tax($person,"GBP");
    echo"<pre>";print_r($returned_values);echo"</pre>";
    // array_push($testarr,$returned_values);
}


// echo"<pre>";print_r($testarr);echo"</pre>";




?>