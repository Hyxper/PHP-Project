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
    $salary = (float) $person["salary"]; //this is used to deduct max salary of tax bands to work out each stage
    $salary_total = (float) $salary; //this is used when a persons tax band is identified, to work out their salary after tax
    $tax_band = $person["tax_band"]; //persons tax band
    $tax_deductable =0.00; //total of how much tax to deduct from salary_total

    if($tax_band == "tax_band_1"){
        return $salary_total;
    }else{
        if($tax_band=="tax_band_4"){ //reduces person tax free allowance by 50% if tax band is 4
            $salary -= 5000;
        }else{
        $salary -= 10000;
        }
    }
    if($tax_band=="tax_band_2"){ //these sections return to the call, identifies person tax band and calculates accordingly
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
    //should be the end here //IF PERSON IS TAX BAND 4, £10K TAX FREE ALLOWANCE IS REDUCED TO £5K.
    }
}


//$formattedcost = '£' . number_format( (float) $costcounter, 2, '.', ',' ); //chose this because I dont like number format

?>