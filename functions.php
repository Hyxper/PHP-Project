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
 
    $taxdata = create_tax_data(); //calls below function to create tax band info for employees

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



function calculate_standard_tax($person, $tax_info, $currency){ //if currency is not set, is set to the persons default, this will just print the default currency value out when called.
    $salary = (float) $person["salary"]; //this is used to deduct max salary of tax bands to work out each stage
    $salary_total = (float) $salary; //this is used when a persons tax band is identified, to work out their salary after tax
    $tax_band = $person["tax_band"]; //persons tax band
    $tax_deductable =0.00; //total of how much tax to deduct from salary_total

    if(!$currency=$person["currency"]){
        // exchange currency
    }

    if($tax_band == "tax_band_1"){ //all works around what the persons tax band was set to
        if($currency){ //checks if a currency was applied, if so it will try to run process_value with it
            try{
            return process_value($salary_total,$currency);//process value is supplied with the total salary (after tax), and supplied currency if there was one
            }
            catch(Exception $e){
            echo $e->getMessage();//if the currency isnt supported, this will catch an exception and report to the user what is available.
            exit();
            }
        }else{
            return $salary_total;//if no currency has been entered, it will just return the default value (Either GPB or USD).
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
    // reducing the person salary by the higher threshold ammount, to work out how much left is taxable.
    }
    if($tax_band=="tax_band_2"){ //these sections return to the call, identifies person tax band and calculates accordingly
        $tax_deductable += $salary*($tax_info["tax_band_2"]["rate"]/100); // if applicable, the persons salary should sit on this band, meaning it is a percentage (in this case 20%) of what is left
        $salary_total -= $tax_deductable; //removes this tax from total salary
            try{
            return process_value($salary_total,$currency);
            }
            catch(Exception $e){
            echo $e->getMessage();
            exit();
            }
    }else{//else would mean that the person is not in that tax band, but they have to pay the maximum tax in this bracket (20% of 40k in this case)
        $salary -= $tax_info["tax_band_2"]["maxsalary"]; //takes away max band to work out what is left
        $tax_deductable += $tax_info["tax_band_2"]["maxsalary"]*($tax_info["tax_band_2"]["rate"]/100); //adds the tax in this band to use when required (when working out total taxable)
    //the other sections are structed the same way. The person is either in a tax band, and works out the remainder, OR the taxable ammount is deducted from the counter, and the tax for that band is worked out, then is rolled
    //onto the next band in order to keep track of total tax to deduct.
    }
    if($tax_band=="tax_band_3"){
        $tax_deductable += $salary*($tax_info["tax_band_3"]["rate"]/100);
        $salary_total -= $tax_deductable;
            try{
            return process_value($salary_total,$currency);
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
        $salary_total -= $tax_deductable;
        if($currency){
            try{
            return process_value($salary_total,$currency);
            }
            catch(Exception $e){
            echo $e->getMessage();
            exit();
            }
    //should be the end here //IF PERSON IS TAX BAND 4, £10K TAX FREE ALLOWANCE IS REDUCED TO £5K.
       }
    }
}




function process_value($value,$currency){ //this will attempt to process a value, it will check what functions exist, and if it does call it. the returned ammount will be formatted as a specific currency.
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

        $formattedvalue = '$' . number_format( (float) $value, 2, '.', ',' ); //formats to euro with two decimal places.
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













function currency_conversion($currency_convert_from){ //creates an array of currency values from an API. will take any input that the API supports (USD, JPY, EUR etc) and return the conversion rates for the availble "format_currency_" functions.
    $currency_convert_from = strtoupper($currency_convert_from);
    $arrayofconversion = array();
    $api_key = "04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8";
    $curl = curl_init();

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
       throw new Exception("cURL Error when checking available currencies on API #:" . $err);
    } else {
        $available_currencies=array();
        foreach(explode('"',$response) as $str){
            if(!str_contains($str,",")){
                if(!str_contains($str,"[")){
                    if(!str_contains($str,"]")){
                        array_push($available_currencies,$str);
                    } 
                 }  
             }
         }
            $check_if_exists = 0;
            foreach($available_currencies as $currency){
                if(strcmp($currency_convert_from, $currency)!==0){
                    $check_if_exists += 1;  
                }
            }
            if($check_if_exists == count($available_currencies)){
                throw new Exception(available_functions($available_currencies,"Currency format is not available from API, availble currencies are:"));
            }
    }
    foreach(check_currency_functions() as $function){
    $currency_convert_to = strtoupper(substr($function,-3));   
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://currency-exchange.p.rapidapi.com/exchange?from=".$currency_convert_from."&to=".$currency_convert_to,
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
        $arrayofconversion[$currency_convert_to] = $response;
    }
  }
    return $arrayofconversion; 
}   

?>