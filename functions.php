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



// function calculate_standard_tax($person, $tax_info, $currency="none"){ //if currency is not set, is set to none, this will just print the float value out when called.
//     $salary = (float) $person["salary"]; //this is used to deduct max salary of tax bands to work out each stage
//     $salary_total = (float) $salary; //this is used when a persons tax band is identified, to work out their salary after tax
//     $tax_band = $person["tax_band"]; //persons tax band
//     $tax_deductable =0.00; //total of how much tax to deduct from salary_total

//     if($tax_band == "tax_band_1"){
//         if(!$currency == "GBP"){
//             try{
//             return format_GBP($salary_total);
//             }
//             catch(Exception $e){
//             echo $e->getMessage();
//             }
//         }else{
//             return $salary_total;
//         }
//     }else{
//         if($tax_band=="tax_band_4"){ //reduces person tax free allowance by 50% if tax band is 4
//             $salary -= 5000;
//         }else{
//         $salary -= 10000;
//         }
//     }
//     if($tax_band=="tax_band_2"){ //these sections return to the call, identifies person tax band and calculates accordingly
//         $tax_deductable += $salary*($tax_info["tax_band_2"]["rate"]/100);
//         $salary_total -= $tax_deductable;
//         if($currency == "GBP"){
//             try{
//             return format_GBP($salary_total);
//             }
//             catch(Exception $e){
//             echo $e->getMessage();
//             }
//         }else{
//             return $salary_total;
//         }
        
//     }else{
//         $salary -= $tax_info["tax_band_2"]["maxsalary"];
//         $tax_deductable += $tax_info["tax_band_2"]["maxsalary"]*($tax_info["tax_band_2"]["rate"]/100);
//     }
//     if($tax_band=="tax_band_3"){
//         $tax_deductable += $salary*($tax_info["tax_band_3"]["rate"]/100);
//         $salary_total -= $tax_deductable;
//         if($currency == "GBP"){
//             try{
//                 return format_GBP($salary_total);
//                 }
//                 catch(Exception $e){
//                 echo $e->getMessage();
//                 }
//         }else{
//             return $salary_total;
//         }
//     }else{
//         $salary -= $tax_info["tax_band_3"]["maxsalary"];
//         $tax_deductable += $tax_info["tax_band_3"]["maxsalary"]*($tax_info["tax_band_3"]["rate"]/100);
//     }
//     if($tax_band=="tax_band_4"){
//         $tax_deductable += $salary*($tax_info["tax_band_4"]["rate"]/100);
//         $salary_total -= $tax_deductable;
//            if($currency == "GBP"){
//             try{
//                 return format_GBP($salary_total);
//                 }
//                 catch(Exception $e){
//                 echo $e->getMessage();
//                 }
//         }else{
//             return $salary_total;
//         }
//     //should be the end here //IF PERSON IS TAX BAND 4, £10K TAX FREE ALLOWANCE IS REDUCED TO £5K.
//     }
// }


function process_value($value,$currency){
    $format_currency_func = "format_".$currency;
    if(!function_exists($format_currency_func)){ //checks if function exists with passed currency (GBP etc)
        throw new Exception(available_functions(check_currency_functions(),"'".$currency."' is not supported. Supported functions are:"));
    }else{
    try{
        return $format_currency_func($value);
        }
        catch(Exception $e){
        echo $e->getMessage();
        }
    }    
}


function format_currency_GBP($value){
    $formattedvalue = "";
    if(!is_numeric($value)){ //checks to see if the input is not numeric
        throw new Exception("data supplied to '".__FUNCTION__."' was not numeric"); //throw exception stating supplied value is incorrect type also print function name
    }else{
        $formattedvalue = '£' . number_format( (float) $value, 2, '.', ',' ); //formats to pounds with two decimal places.
        return $formattedvalue;
    }   
    
}

function format_currency_EUR($value){
    $formattedvalue = "";
    if(!is_numeric($value)){ //checks to see if the input is not numeric
        throw new Exception("data supplied to '".__FUNCTION__."' was not numeric"); //throw exception stating supplied value is incorrect type also print function name
    }else{
        $formattedvalue = '€' . number_format( (float) $value, 2, '.', ',' ); //formats to euro with two decimal places.
        return $formattedvalue;
    }   
    
}

function check_currency_functions(){
    $user_defined_funcs = get_defined_functions(false);
    $user_defined_funcs = $user_defined_funcs["user"];
    $currency_functions = array();
    foreach($user_defined_funcs as $function){
        if(str_contains($function,"format_currency")){
        array_push($currency_functions, $function);
        }
    }
    return $currency_functions;
}


function available_functions($functions,$message=""){
        echo $message."<br>";
        echo "<ul>";
        foreach($functions as $function){
            echo "<li>".$function."</li>";
        }
        echo "</ul>";
}

?>