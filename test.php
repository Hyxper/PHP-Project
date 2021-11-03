<?php
require __DIR__ . '/functions.php';

$currency_to_work_in= "GBP";
$currency_rates=currency_conversion("GBP");



$personeldatapre = array();
$personeldata = array();

$json = file_get_contents("./employees-final.json");
$personeldatapre = json_decode($json,true);

foreach($personeldatapre as $person){
    $elementrename =  $person["id"]."_".$person["firstname"]."_".$person["lastname"];
    $personeldata[$elementrename] = $person;
    $elementrename=""; 
} //this adds the ID, firstname and last name to the parent array element, rather than normal.

$taxdata = create_tax_data(); //calls below function to create tax band info for employees

foreach($personeldata as $key=>$person){

    if($person["currency"]!==$currency_to_work_in){
    $salarytocheck = exchange_currenncy($currency_rates,$person["salary"],$person["currency"]);
    }else{
    $salarytocheck = $person["salary"];     
    }
    
    // foreach($taxdata as $taxband => $taxinfo){
    //     if($salarytocheck >= $taxinfo["minsalary"]){
    //         if($salarytocheck <= $taxinfo["maxsalary"]){
    //             $personeldata[$key]["tax_band"] = $taxband;
    //         }
    //     }
    // }


     if($salarytocheck <= $taxdata["tax_band_1"]["maxsalary"]){ // 10k
         $personeldata[$key]["tax_band"] = "tax_band_1";           
     }elseif($salarytocheck <= $taxdata["tax_band_2"]["maxsalary"]){//40k
         $personeldata[$key]["tax_band"] = "tax_band_2";            
     }elseif($salarytocheck <= $taxdata["tax_band_3"]["maxsalary"]){//150k
         $personeldata[$key]["tax_band"] = "tax_band_3";          
     }elseif($salarytocheck > $taxdata["tax_band_4"]["minsalary"]){//150k+
         $personeldata[$key]["tax_band"] = "tax_band_4";        
     }

}
//appends tax band to each employee, for tax calculation function.

echo "<pre>";
echo $personeldata["8861_Faith_Watson"]["tax_band"];
echo "</pre>";





?>