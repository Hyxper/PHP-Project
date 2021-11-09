<?php
require __DIR__ . '/functions.php';
session_start();
$GLOBALS["working_currency"]="EUR";//define set currency to use--------------------NEEDED--------------------     
// try{
$GLOBALS["currency_rate"] = currency_conversion($GLOBALS["working_currency"]); //calculate currency exchange rates (works out 1 > other currencies. In this case will call api to check what £1 is in USD, EUR)--------------------NEEDED--------------------
// }catch(Exception $e){
//     echo $e->getMessage();//if the currency isnt supported, this will catch an exception and report to the user what is available.
//     exit();
// }

// $json = file_get_contents("./JSON/conversion_rates.json");
// $conversion_backup = json_decode($json,true);


// echo "<pre>";
// print_r(API_Invoke("https://currency-exchange.p.rapidapi.com/exchange?from=GBP&to=USD","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8")); //define tax table to use --------------------NEEDED--------------------
// echo "</pre>";

// echo "<pre>";
// print_r(currency_conversion($GLOBALS["working_currency"])); //define tax table to use --------------------NEEDED--------------------
// echo "</pre>";



// echo API_Invoke("https://currency-exchange.p.rapidapi.com/listquotes","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8")."<br>";
// echo (API_Invoke("https://currency-exchange.p.rapidapi.com/exchange?from=GBP&to=USD","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8"));
// echo "<br>";
// echo 10+API_Invoke("https://currency-exchange.p.rapidapi.com/exchange?from=GBP&to=USD","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8");

// $conversion_backup["GBP_rates"]["USD"] = "gotem";
// $updater_string = json_encode($conversion_backup);
// file_put_contents("./JSON/conversion_rates.json",$updater_string);

set_timezone("GMT");
echo date('D M j G:i:s a');





?>