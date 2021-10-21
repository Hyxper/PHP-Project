<?php

function create_personel_data(){
    $personeldatapre = array();
    $personeldata = array();
    $json = file_get_contents("./employees-final.json");
    $personeldatapre = json_decode($json,true);
    foreach($personeldatapre as $person){
        $elementrename =  $person["id"]."_".$person["firstname"]."_".$person["lastname"];
        $personeldata[$elementrename] = $person;
        $elementrename=""; 
    } //this adds the ID, firstname and last name to the parent array element, rather than normal.
 
    $taxdata = create_tax_data();

    foreach($personeldata as $key=>$person){
        $salarytocheck = $person["salary"];     
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
    return $personeldata;
}


function create_tax_data(){
    $taxdatapre = array();
    $taxdata = array();
    $json = file_get_contents("./tax-tables.json");
    $taxdatapre = json_decode($json,true);
    foreach($taxdatapre as $tax){
         $elementrename =  "tax_band_".$tax["id"];
         $taxdata[$elementrename] = $tax;
         $elementrename=""; 
    } //this creates an array from tax rate JSON, renaming the parent element to the tax band, and removing the name elemen

    return $taxdata;
}



function calculate_standard_tax($person, $tax_info){
    $salary = $person["salary"];
    $salary_total = $salary;
    $tax_band = $person["tax_band"];
    $tax_deductable =0.0;

    if($tax_band == "tax_band_1"){
        return $salary_total;
    }else{
        $salary -= 10000;
    }
    
    if($tax_band=="tax_band_2"){
        $tax_deductable += $salary*($tax_info["tax_band_2"]["rate"]/100);
        $salary_total -= $tax_deductable;
        return $salary_total;
    }else{
        $salary -= $tax_info["tax_band_2"]["maxsalary"];
        $tax_deductable += $tax_info["tax_band_2"]["maxsalary"]*($tax_info["tax_band_2"]["rate"]/100);
    }

    if($tax_band=="tax_band_3"){
        $tax_deductable += $salary*($tax_info["tax_band_3"]["rate"]/100);
        $salary_total -= $tax_deductable;
        return $salary_total;
    }else{
        $salary -= $tax_info["tax_band_3"]["maxsalary"];
        $tax_deductable += $tax_info["tax_band_3"]["maxsalary"]*($tax_info["tax_band_3"]["rate"]/100);
    }

    if($tax_band=="tax_band_4"){
        $tax_deductable += $salary*($tax_info["tax_band_4"]["rate"]/100);
        $salary_total -= $tax_deductable;
        return $salary_total;
    //should be the end here
}





?>