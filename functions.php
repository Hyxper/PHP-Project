<?php

//JACK ADD ARRAY THAT RETURNS MONTHLY PAY/ GROSS PAY/ TAX DEDUCTED/ YEARLY PAY


//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------CREATE DATA SETS----------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function create_personel_data($currency_to_work_in){ // creates data for each person on list. Supplied with desired currency to exchange and base tax bands accordingly.
    $personeldatapre = array();
    $personeldata = array();
    $employeefile = "./JSON/employees-final.json";

    if(is_json($employeefile) == true){
        $json = file_get_contents($employeefile);
        $personeldatapre = json_decode($json,true);
    }
    
    foreach($personeldatapre as $person){
        $elementrename =  $person["id"]."_".$person["firstname"]."_".$person["lastname"];
        $personeldata[$elementrename] = $person;
        $elementrename=""; 
    } //this adds the ID, firstname and last name to the parent array element, rather than normal.
 
    if(isset($GLOBALS["tax_data"])==false){
        throw new Exception("tax data is not set!");
    }else{
        $taxdata = $GLOBALS["tax_data"];
    }
    
    
    foreach($personeldata as $key=>$person){
        if($person["currency"]!==$currency_to_work_in){
            $salarytocheck = exchange_currency($person["salary"],$person["currency"]);
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
    //appends tax band to each employee, for tax calculation function.
    return $personeldata;
}

                                        
function create_tax_data($tax_data_file,$currency_of_file){ //can crate array of tax info. very reusalble when this information is required. Requires a tax data file (JSON) that matches the format of the original.
    $taxdatapre = array();
    $taxdata = array();
    if(is_json($tax_data_file) == true){
        if(verify_tax_file($tax_data_file)==true){
            $json = file_get_contents($tax_data_file);
            $taxdatapre = json_decode($json,true);
        }
    }  
    foreach($taxdatapre as $tax){
        $elementrename =  "tax_band_".$tax["id"];
        $taxdata[$elementrename] = $tax;
        $elementrename=""; 
    } //this creates an array from tax rate JSON, renaming the parent element to the tax band, and removing the name elemen

    if ($currency_of_file !==  $GLOBALS["working_currency"]){
        foreach($taxdata as $key=>&$tax_band){
    
            if($key == array_key_first($taxdata)){
                $tax_band["maxsalary"] = round(exchange_currency($tax_band["maxsalary"],$currency_of_file),2);
                continue;
            }
            if($key == array_key_last($taxdata)){
                $tax_band["minsalary"] = round(exchange_currency($tax_band["minsalary"],$currency_of_file),2);
                continue;
                }   
           $tax_band["minsalary"] = round(exchange_currency($tax_band["minsalary"],$currency_of_file),2);
           $tax_band["maxsalary"] = round(exchange_currency($tax_band["maxsalary"],$currency_of_file),2);
           
        }
      
    }
    return $taxdata;
}

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------CACLULATE TAX-------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


function calculate_standard_tax($person, $currency){ //passes in person (array) and currency that is going to be used for calculating
    $salary = (float) $person["salary"]; //this is used to keep track of how much salary is left to calculate
    $net_salary = (float) $salary; //this is used when a persons tax band is identified, to work out their salary after tax
    $person_tax_band = $person["tax_band"]; //persons tax band
    $current_working_currency = $person["currency"]; //currency someone is paid in (to check and exchange if neccesary)
    $tax_deductable =0.00; //total of how much tax to deduct from net_salary
    $calculated_values = array(); //values to return to the user after calculating


    if(isset($GLOBALS["tax_data"])==false){
        throw new Exception("tax data is not set!");
    }else{
        $tax_information = $GLOBALS["tax_data"];//assigns all relevant tax data for this function
    }
    
    if($currency!==$current_working_currency){
        try{
        $salary = exchange_currency($salary,$current_working_currency); //exchange currency if persons currency does not match working.
        $net_salary = $salary;
        }catch(Exception $e){
            echo $e->getMessage();//if the currency isnt supported, this will catch an exception and report to the user what is available.
            exit();
        }
    }
        
        foreach($tax_information as $tax_band=>$tax_data){ //loop through each tax band
            if($person_tax_band == $tax_band){
                if($tax_data["rate"] !== 0){ //if person is tax_band_1, will only calc if there is
                    $tax_deductable += $salary*($tax_data["rate"]/100); // if applicable, the persons salary should sit on this band, meaning it is a percentage of what is left
                    $net_salary -= $tax_deductable; //removes the tax from total salary to work out net.
                }
                    try{
                        if($currency=="GBP"){ //if currency was converted, convert back  (only if default GBP is set as currency)
                            $tax_deductable=exchange_currency($tax_deductable,$current_working_currency,true);//exchange back to working currency
                            $net_salary=exchange_currency($net_salary,$current_working_currency,true); //true indicates to revert the calculation
                        }

                        //asigns array to be returned, contains all info needed to inform whomever on tax information.
                        if($currency == "GBP"){
                            $calculated_values["salary_year"] = $person["salary"];
                            $calculated_values["salary_month"] = $calculated_values["salary_year"]/12;
                        }else{
                            $calculated_values["salary_year"] = exchange_currency($person["salary"],$current_working_currency);
                            $calculated_values["salary_month"] = exchange_currency($calculated_values["salary_year"]/12,$current_working_currency);
                        }
                        $calculated_values["tax_year"] = $tax_deductable;
                        $calculated_values["tax_month"] = $calculated_values["tax_year"]/12;
                        $calculated_values["net_salary_year"] = $net_salary;
                        $calculated_values["net_salary_month"] = $calculated_values["net_salary_year"]/12;
                        foreach($calculated_values as $key=>$calc_value){ //for each element in array, process the value to be applicable currency
                            if($currency=="GBP"){
                                $calculated_values[$key]=process_value($calc_value,$current_working_currency); //so person in dollars salary is displayed correctly (DEFUALT VALUE)
                            }else{
                                $calculated_values[$key]=process_value($calc_value,$currency); //OTHERWISE DISPLAY ALL AS SELECTED CURRENCY
                            }
                        
                        }
                        return $calculated_values; //returns to user.           
                    }
                    catch(Exception $e){
                        echo $e->getMessage();//if the currency isnt supported, this will catch an exception and report to the user what is available.
                    exit();
                    }
        }else{
            //exceptions center. in this case for the project, people in tax band 4 earn 50% personal allowance, and dont get any if they have a car. added check for currency incase I want to add other exceptions.
            if($tax_band == "tax_band_1" && $currency == "GBP"){
                if($person_tax_band == "tax_band_4" && $person["companycar"]=="y"){
                    continue;
                }elseif($person["companycar"]=="y"){
                    continue;
                }elseif($person_tax_band == "tax_band_4"){
                    $salary -= 0.5*$tax_data["maxsalary"];
                }else{
                    $salary -= $tax_data["maxsalary"]; //remove the tax free allowance from the salary.
                }
                continue;
             }
             //at this point, we can assume person is not in the matching tax band, and the tax band is not tax band 1.
             $tax_band_difference = $tax_data["maxsalary"]-$tax_data["minsalary"];
             $salary -= $tax_band_difference;//remove from salary based on the difference between tax bands minimum and maximum.
             $tax_deductable += $tax_band_difference*($tax_data["rate"]/100); //work out tax to pay on the difference.
        }
      }
}
  


//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------FORMAT CURRENCY-------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function process_value($value,$currency){ //this will attempt to process a value, it will check what functions exist, and if it does call it. the returned amount will be formatted as a specific currency.
    $format_currency_func = "format_currency_".$currency;
    if(!function_exists($format_currency_func)){ //checks if function exists with passed currency (GBP etc)
        throw new Exception(available_functions(check_currency_functions(),"'".$currency."' is not supported. Supported functions are:")); //will complain if supplied currency is not supported
    }else{
    try{ //will complain and fall over if the supplied value is not numeric. string of numbers, int and float are all compatible.
        return $format_currency_func($value);
        }
        catch(Exception $e){
        echo $e->getMessage();
        exit();
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

function format_currency_USD($value){
    $formattedvalue = "";
    if(!is_numeric($value)){ //checks to see if the input is not numeric
        throw new Exception("data supplied to '".__FUNCTION__."' was not numeric"); //throw exception stating supplied value is incorrect type also print function name
    }else{

        $formattedvalue = '$' . number_format( (float) $value, 2, '.', ',' ); //formats to dollars with two decimal places.
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
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------EXCHANGE CURRENCY---------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function exchange_currency($amount,$exchange_currency,$revert = false){ //this takes a generated array of exchange rates (see currency conversion), amount to exchange, what to exchange to/from 
    $amount = (float) $amount;
    $check_if_exists = 0;

    if(isset($GLOBALS["currency_rate"])==false){
        throw new Exception("Currency conversion rates not set!");
    }else{
        $rates = $GLOBALS["currency_rate"];
    }
    foreach($rates as $currency=>$value){
        if(strcmp($currency,$exchange_currency)!==0){
            $check_if_exists += 1;  
        }
    }
    if($check_if_exists == count($rates)){
        throw new Exception(available_functions($rates,"'".$exchange_currency."' is not supported for coversion. Supported currencies are:")); //will complain if supplied currency is not supported
    }else{      
        if($revert){
        return $amount*$rates[$exchange_currency]; //convert from
        }else{        
         return $amount/$rates[$exchange_currency]; //convert to
        }
    }
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------api FUNCTIONS-------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function currency_conversion($currency_convert_from){ //creates an array of currency values from an api. will take any input that the api supports (USD, JPY, EUR etc) and return the conversion rates for the availble "format_currency_" functions.
    $currency_convert_from = strtoupper($currency_convert_from); //makes it so can be entered lower case.
    $converted_currencies = array(); //array that will contain all of the conversion rates
    $key = "04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8"; //key needed to interface with api
    $rate_file = "./JSON/conversion_rates.json"; //file needed to replace redudant values if api is returning zero, updates when all are read back.

        if(is_json($rate_file)==true){  
            $rate_file_data = array();
            $json = file_get_contents($rate_file);
            $rate_file_data = json_decode($json,true);
        }


        $available_currencies=array(); //check api to see what currencies it supports
        foreach(explode('"',api_invoke("https://currency-exchange.p.rapidapi.com/listquotes",$key, "error when querying available currencies:")) as $str){ //filter out response from api, contained commas and brackets
            if(preg_match("/[\[,\]]/",$str) == 0){ //filters string with regeular expression
                        array_push($available_currencies,$str); //this will only push elements from the string that are valid (Currencies)
            }
        }
            $check_if_exists = 0;
            foreach($available_currencies as $currency){ //this section will go through each element and check for a match, if the counter is not equal to the array length, means the input from user was invalid (isnt supported)
                if(strcmp($currency_convert_from, $currency)!==0){
                    $check_if_exists += 1;  
                }
            }
            if($check_if_exists == count($available_currencies)){
                throw new Exception(available_functions($available_currencies,"Currency format is not available from api, availble currencies are:"));
            }

        foreach(check_currency_functions() as $function){ //this ensures that conversion rates are only added that have a existing formatting function. More functions means more support.

                $currency_convert_to = strtoupper(substr($function,-3));   //will always be denoted by last 3 letters (GBP, EUR, USD, JPY)
                $returned_val = api_invoke("https://currency-exchange.p.rapidapi.com/exchange?from=".$currency_convert_from."&to=".$currency_convert_to,$key,"cURL Error when gathering currency exchange rates:");

                if($returned_val==0){//API is prone to sending zero as a response in cases where data cannoth be gathered. Stops divide by zero errors and continuity.
                    $converted_currencies[$currency_convert_to] = $rate_file_data[$currency_convert_from."_rates"][$currency_convert_to]["rate"]; //makes array im returning a hisotical returned value gathered from successful interfacing
                }else{//else use the value API has returned, and update the JSON file with the newest rate in case API falls over.
                    $converted_currencies[$currency_convert_to] = $returned_val; //appends to array.
                    $rate_file_data[$currency_convert_from."_rates"][$currency_convert_to]["rate"] = $returned_val; //update file
                    $rate_file_data[$currency_convert_from."_rates"][$currency_convert_to]["last_updated"]=date('D M j G:i:s a'); //set date last updated on value
                    $updater = json_encode($rate_file_data);//encode file as json
                    file_put_contents($rate_file,$updater);//place encoded data back into file
                }
            }
            return $converted_currencies; 
        }



 function api_invoke($URL, $api_key,$err_msg="error"){

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: currency-exchange.p.rapidapi.com",
                "x-rapidapi-key: ".$api_key
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);    
        if ($err) {
            throw new exception($err_msg." ".$err);
        }else{
            return $response;
        }
}
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------AUXILLARY FUNCTIONS-------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function check_currency_functions(){ //creates an array of available functions, in which have been created to format currency. will return what is available. uses the function below to return a formatted list to user
    $user_defined_funcs = get_defined_functions(false);
    $user_defined_funcs = $user_defined_funcs["user"];
    $currency_functions = array();
    foreach($user_defined_funcs as $function){
        if(preg_match("/format_currency/",$function)){
            array_push($currency_functions, $function);
        }
    }
    return $currency_functions;
}

function available_functions($functions,$message=""){ //will just tell you what functions are available based on what is passed in. formatted as a list. can supply optional message.
        echo $message."<br>";
        echo "<ul>";
        foreach($functions as $function){
            echo "<li>".$function."</li>";
        }
        echo "</ul>";
}

function is_json($file){
    $json = file_get_contents($file);
    json_decode($json);
    if(json_last_error() !== JSON_ERROR_NONE){
     throw new Exception("File located at ".$file." is not JSON.");
    }
    if(json_last_error() !==0){
        throw new Exception("Error has been found when passing JSON file:".json_last_error_msg());
    }
    return true;
 }

function verify_tax_file($file){
    $json = file_get_contents($file);
    $contents = json_decode($json,true);

    foreach($contents as $key=>$criteria){
        if(array_key_exists("id",$criteria)){
            if(!is_numeric($criteria["id"])){
                throw new Exception("value in element ".$key." for ID is invalid");
            }
        }else{
            throw new Exception("element ".$key." requires ID field");
        }
        if(array_key_exists("minsalary",$criteria)){
            if(!is_numeric($criteria["minsalary"])){
                throw new Exception("value in element ".$key." for minsalary is invalid");
            }
        }else{
            throw new Exception("element ".$key." requires minsalary field");
        }
        if(array_key_exists("maxsalary",$criteria)){
            if(!is_numeric($criteria["maxsalary"])){
                throw new Exception("value in element ".$key." for maxsalary is invalid");
            }
        }else{
            throw new Exception("element ".$key." requires maxsalary field");
        }
        if(array_key_exists("rate",$criteria)){
            if(!is_numeric($criteria["rate"])){
                throw new Exception("value in element ".$key." for rate is invalid");
            }
        }else{
            throw new Exception("element ".$key." requires rate field");
        }
      }  
      return true;
    }

function set_timezone($timezone){ //if timezone has not been set in session, set timezone.
    if(isset($_SESSION["timezone"])==false){
        date_default_timezone_set($timezone);
    }
}


function check_tax_files($dir,$for_form = false){
    $returned_files = array();
    $temp = array();

    if($for_form == true){
        foreach(scandir($dir) as $file){
            if(preg_match("/tax-tables/",$file) !== 0){

                if(preg_match("/tax-tables\.json/",$file) !== 0){
                    $temp = array("file"=>$file, "reigon"=>"British", "code" => "GBP");
                    array_push($returned_files,$temp);
                }

                if(preg_match("/tax-tables_USD/",$file) !== 0){
                    if(preg_match("/(^tax-tables)/",$file) !== 0)
                        $temp = array("file"=>$file, "reigon"=>"American", "code" => "USD");
                        array_push($returned_files,$temp);
                }

                if(preg_match("/tax-tables_EUR/",$file) !== 0){
                    if(preg_match("/(^tax-tables)/",$file) !== 0)
                        $temp = array("file"=>$file, "reigon"=>"French", "code" => "EUR");
                        array_push($returned_files,$temp);
                }
            }
        
        }
        return $returned_files;
    }else{
        foreach(scandir($dir) as $file){
            if(preg_match("/tax-tables/",$file) !== 0){
                array_push($returned_files,$file);
             }
        }
    return $returned_files;
    }  
}


function create_check_project_file($filename){
    $filepath = __DIR__."/JSON/".$filename;
    if (file_exists($filepath)) {
        return;
    } else {
        copy(__DIR__."/JSON/tax-tables.json",$filepath);
    }
}
?>