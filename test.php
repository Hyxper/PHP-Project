<?php
require __DIR__ . '/functions.php';
$GLOBALS["working_currency"]="GBP";//define set currency to use--------------------NEEDED--------------------     
$GLOBALS["currency_rate"] = currency_conversion($GLOBALS["working_currency"]); //calculate currency exchange rates (works out 1 > other currencies. In this case will call api to check what £1 is in USD, EUR)--------------------NEEDED--------------------


$json = file_get_contents("./JSON/conversion_rates.json");
$conversion_backup = json_decode($json,true);


echo "<pre>";
print_r($conversion_backup); //define tax table to use --------------------NEEDED--------------------
echo "</pre>";


// echo API_Invoke("https://currency-exchange.p.rapidapi.com/listquotes","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8")."<br>";
echo var_dump(API_Invoke("https://currency-exchange.p.rapidapi.com/exchange?from=GBP&to=USD","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8"));
// echo "<br>";
// echo 10+API_Invoke("https://currency-exchange.p.rapidapi.com/exchange?from=GBP&to=USD","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8");



?>