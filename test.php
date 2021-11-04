<?php
require __DIR__ . '/functions.php';

function create_tax_datatwo(){ //can crate array of tax info. very reusalble when this information is required.
    $taxdatapre = array();
    $taxdata = array();
    $json = file_get_contents("./tax-tablestwo.json");
    $taxdatapre = json_decode($json,true);
    foreach($taxdatapre as $tax){
        $elementrename =  "tax_band_".$tax["id"];
        $taxdata[$elementrename] = $tax;
        $elementrename=""; 
    } //this creates an array from tax rate JSON, renaming the parent element to the tax band, and removing the name elemen

   return $taxdata;
}








    $personeldatapre = array();
    $personeldata = array();

    $currency_to_work_in = "GBP";
    $currency_rates = currency_conversion("GBP");

    $json = file_get_contents("./employees-final.json");
    $personeldatapre = json_decode($json,true);
    foreach($personeldatapre as $person){
        $elementrename =  $person["id"]."_".$person["firstname"]."_".$person["lastname"];
        $personeldata[$elementrename] = $person;
        $elementrename=""; 
    } //this adds the ID, firstname and last name to the parent array element, rather than normal.
 
    $taxdata = create_tax_datatwo(); //calls below function to create tax band info for employees
    
    foreach($personeldata as $key=>$person){

        if($person["currency"]!==$currency_to_work_in){
        $salarytocheck = exchange_currenncy($currency_rates,$person["salary"],$person["currency"]);
        }else{
        $salarytocheck = $person["salary"];     
        }

        foreach($taxdata as $taxband => $taxinfo){
            if($salarytocheck >= $taxinfo["minsalary"]){
                if($salarytocheck <= $taxinfo["maxsalary"]){
                    $personeldata[$key]["tax_band"] = $taxband;
                }
            }
        }

    }

    echo "<pre>";print_r($personeldata);echo "</pre>"

?>