<?php

//JACK ADD ARRAY THAT RETURNS MONTHLY PAY/ GROSS PAY/ TAX DEDUCTED/ YEARLY PAY


//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------CREATE DATA SETS----------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function create_personel_data($currency_to_work_in,$currency_rates){ // creates data for each person on list. Supplied with desired currency to exchange and base tax bands accordingly.
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


function create_tax_data(){ //can crate array of tax info. very reusalble when this information is required.
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

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------CACLULATE TAX-------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


function calculate_standard_tax($person, $tax_info, $currency, $currency_rates){ //currency_rates must be generated before, and must best what currency you want to work in. ($currency = GBP, $currency_rates must be GBP)
    $salary = (float) $person["salary"]; //this is used to deduct max salary of tax bands to work out each stage
    $net_salary = (float) $salary; //this is used when a persons tax band is identified, to work out their salary after tax
    $tax_band = $person["tax_band"]; //persons tax band
    $current_working_currency = $person["currency"];
    $tax_deductable =0.00; //total of how much tax to deduct from net_salary
    $calculated_values = array();

    if($currency!==$current_working_currency){
       $salary = exchange_currenncy($currency_rates,$salary,$current_working_currency); //salary and salary total are converted to whatever currency (GBP etc) was supplied when calling function
       $net_salary = $salary;
    }

    if($tax_band == "tax_band_1"){ //all works around what the persons tax band was set to
            try{
                if($currency!==$current_working_currency){ //if currency was converted, convert back  
                    $tax_deductable=exchange_currenncy($currency_rates,$tax_deductable,$current_working_currency,true);
                    $net_salary=exchange_currenncy($currency_rates,$net_salary,$current_working_currency,true); //true indicates to revert the calculation
                }
                 $calculated_values["salary_year"] = $person["salary"];
                 $calculated_values["tax_year"] = $tax_deductable;
                 $calculated_values["net_salary_year"] = $net_salary;
                 $calculated_values["salary_month"] = $person["salary"]/12;
                 $calculated_values["tax_month"] = $tax_deductable/12;
                 $calculated_values["net_salary_month"] = $net_salary/12;
                 foreach($calculated_values as $key=>$calc_value){
                  $calculated_values[$key]=process_value($calc_value,$current_working_currency);
                 }
                 return $calculated_values;
            //process value is supplied with the total salary (after tax), and supplied currency if there was one, this will format the salary based on users currency (Currently)
            }
            catch(Exception $e){
            echo $e->getMessage();//if the currency isnt supported, this will catch an exception and report to the user what is available.
            exit();
            }
    }else{
        if($tax_band=="tax_band_4"){ //reduces person tax free allowance by 50% if tax band is 4
            $salary -= 5000;
        }elseif($person["companycar"]=="y"){ //has company car means dont take anything of total to tax
          //dont deduct anything  
        }
        else{
            $salary -= 10000;
        }
    // this is a unqiue case as tax band 1 persons dont pay tax, as their taxband is below the first thresh hold. it is also noted that tax band 4 employees pay 50% less (5k), also no math is performed here apart from
    // reducing the person salary by the higher threshold amount, to work out how much left is taxable.
    }
    if($tax_band=="tax_band_2"){ //these sections return to the call, identifies person tax band and calculates accordingly
        $tax_deductable += $salary*($tax_info["tax_band_2"]["rate"]/100); // if applicable, the persons salary should sit on this band, meaning it is a percentage (in this case 20%) of what is left
        $net_salary -= $tax_deductable; //removes this tax from total salary
        try{
            if($currency!==$current_working_currency){ //if currency was converted, convert back  
                $tax_deductable=exchange_currenncy($currency_rates,$tax_deductable,$current_working_currency,true);
                $net_salary=exchange_currenncy($currency_rates,$net_salary,$current_working_currency,true); //true indicates to revert the calculation
            }
             $calculated_values["salary_year"] = $person["salary"];
             $calculated_values["tax_year"] = $tax_deductable;
             $calculated_values["net_salary_year"] = $net_salary;
             $calculated_values["salary_month"] = $person["salary"]/12;
             $calculated_values["tax_month"] = $tax_deductable/12;
             $calculated_values["net_salary_month"] = $net_salary/12;
             foreach($calculated_values as $key=>$calc_value){
                $calculated_values[$key]=process_value($calc_value,$current_working_currency);
             }
             return $calculated_values;
        //process value is supplied with the total salary (after tax), and supplied currency if there was one, this will format the salary based on users currency (Currently)
        }
        catch(Exception $e){
            echo $e->getMessage();
            exit();
            }
    }else{//else would mean that the person is not in that tax band, but they have to pay the maximum tax in this bracket (20% of 40k in this case)
        $salary -= $tax_info["tax_band_2"]["maxsalary"]; //takes away max band to work out what is left
        $tax_deductable += $tax_info["tax_band_2"]["maxsalary"]*($tax_info["tax_band_2"]["rate"]/100); //adds the tax in this band to use when required (when working out total taxable)
    //the other sections are structed the same way. The person is either in a tax band, and works out the remainder, OR the taxable amount is deducted from the counter, and the tax for that band is worked out, then is rolled
    //onto the next band in order to keep track of total tax to deduct.
    }
    if($tax_band=="tax_band_3"){
        $tax_deductable += $salary*($tax_info["tax_band_3"]["rate"]/100);
        $net_salary -= $tax_deductable;
        try{
            if($currency!==$current_working_currency){ //if currency was converted, convert back  
                $tax_deductable=exchange_currenncy($currency_rates,$tax_deductable,$current_working_currency,true);
                $net_salary=exchange_currenncy($currency_rates,$net_salary,$current_working_currency,true); //true indicates to revert the calculation
            }
             $calculated_values["salary_year"] = $person["salary"];
             $calculated_values["tax_year"] = $tax_deductable;
             $calculated_values["net_salary_year"] = $net_salary;
             $calculated_values["salary_month"] = $person["salary"]/12;
             $calculated_values["tax_month"] = $tax_deductable/12;
             $calculated_values["net_salary_month"] = $net_salary/12;
             foreach($calculated_values as $key=>$calc_value){
              $calculated_values[$key]=process_value($calc_value,$current_working_currency);
             }
             return $calculated_values;
        //process value is supplied with the total salary (after tax), and supplied currency if there was one, this will format the salary based on users currency (Currently)
        }
        catch(Exception $e){
            echo $e->getMessage();
            exit();
            }
    }else{
        $salary -= $tax_info["tax_band_3"]["maxsalary"];
        $tax_deductable += $tax_info["tax_band_3"]["maxsalary"]*($tax_info["tax_band_3"]["rate"]/100);
    }
    if($tax_band=="tax_band_4"){
        $tax_deductable += $salary*($tax_info["tax_band_4"]["rate"]/100);
        $net_salary -= $tax_deductable;
       
            try{
                if($currency!==$current_working_currency){ //if currency was converted, convert back  
                    $tax_deductable=exchange_currenncy($currency_rates,$tax_deductable,$current_working_currency,true);
                    $net_salary=exchange_currenncy($currency_rates,$net_salary,$current_working_currency,true); //true indicates to revert the calculation
                }
                 $calculated_values["salary_year"] = $person["salary"];
                 $calculated_values["tax_year"] = $tax_deductable;
                 $calculated_values["net_salary_year"] = $net_salary;
                 $calculated_values["salary_month"] = $person["salary"]/12;
                 $calculated_values["tax_month"] = $tax_deductable/12;
                 $calculated_values["net_salary_month"] = $net_salary/12;
                 foreach($calculated_values as $key=>$calc_value){
                  $calculated_values[$key]=process_value($calc_value,$current_working_currency);
                 }
                 return $calculated_values;
            //process value is supplied with the total salary (after tax), and supplied currency if there was one, this will format the salary based on users currency (Currently)
            }
            catch(Exception $e){
                echo $e->getMessage();
                exit();
                }
    //should be the end here //IF PERSON IS TAX BAND 4, £10K TAX FREE ALLOWANCE IS REDUCED TO £5K.
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
function exchange_currenncy($rates,$amount,$exchange_currency,$revert = false){ //this takes a generated array of exchange rates (see currency conversion), amount to exchange, what to exchange to/from
    $amount = (float) $amount;
    $check_if_exists = 0;
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
//-----------------------------------------------------------------------------------------------API FUNCTIONS-------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
function currency_conversion($currency_convert_from){ //creates an array of currency values from an API. will take any input that the API supports (USD, JPY, EUR etc) and return the conversion rates for the availble "format_currency_" functions.
    $currency_convert_from = strtoupper($currency_convert_from); //makes it so can be entered lower case.
    $arrayofconversion = array(); //array that will contain all of the conversion rates
    $api_key = "04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8"; //key needed to interface with API

    $curl = curl_init(); //start process
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://currency-exchange.p.rapidapi.com/listquotes",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
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
       throw new Exception("cURL Error when checking available currencies on API #:" . $err); //reports if there was an error getting data
    } else {
        $available_currencies=array();
        foreach(explode('"',$response) as $str){ //filter out response from API, contained commas and brackets
            if(!str_contains($str,",")){
                if(!str_contains($str,"[")){
                    if(!str_contains($str,"]")){
                        array_push($available_currencies,$str); //this will only push elements from the string that are valid (Currencies)
                    } 
                 }  
             }
         }
            $check_if_exists = 0;
            foreach($available_currencies as $currency){ //this section will go through each element and check for a match, if the counter is not equal to the array length, means the input from user was invalid
                if(strcmp($currency_convert_from, $currency)!==0){
                    $check_if_exists += 1;  
                }
            }
            if($check_if_exists == count($available_currencies)){
                throw new Exception(available_functions($available_currencies,"Currency format is not available from API, availble currencies are:"));
            }
    }
    foreach(check_currency_functions() as $function){ //this ensures that conversion rates are only added that have a existing formatting function. More functions means more support.
    $currency_convert_to = strtoupper(substr($function,-3));   //will always be denoted by last 3 letters (GBP, EUR, USD, JPY)
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://currency-exchange.p.rapidapi.com/exchange?from=".$currency_convert_from."&to=".$currency_convert_to, //concat what has been entered, with what is available.
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
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
        throw new exception("cURL Error when gathering currency exchange rates #:" . $err);
    } else {
        $arrayofconversion[$currency_convert_to] = $response; //appends to array.
    }
  }
    return $arrayofconversion; 
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
        if(str_contains($function,"format_currency")){
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

?>