<?php
require __DIR__ . '/functions.php';

$GLOBALS["currency_rate"] = currency_conversion("gbp");
$GLOBALS["tax_data"] = create_tax_data("./tax-tables.json");


$salary = (float) $person["salary"]; //this is used to deduct max salary of tax bands to work out each stage
$net_salary = (float) $salary; //this is used when a persons tax band is identified, to work out their salary after tax
$person_tax_band = $person["tax_band"]; //persons tax band
$current_working_currency = $person["currency"];
$tax_deductable =0.00; //total of how much tax to deduct from net_salary
$calculated_values = array();
$tax_information = $GLOBALS["tax_data"];

if($currency!==$current_working_currency){
   try{
   $salary = exchange_currenncy($salary,$current_working_currency); //salary and salary total are converted to whatever currency (GBP etc) was supplied when calling function
   $net_salary = $salary;
   }catch(Exception $e){
    echo $e->getMessage();//if the currency isnt supported, this will catch an exception and report to the user what is available.
    exit();
   }

foreach($tax_information as $tax_band)
    if($person_tax_band == $tax_band){
        if($tax_band !== "tax_band_1"){
            $tax_deductable += $salary*($tax_band["rate"]/100); // if applicable, the persons salary should sit on this band, meaning it is a percentage (in this case 20%) of what is left
            $net_salary -= $tax_deductable; //removes this tax from total salary
        }
            try{
                if($currency!==$current_working_currency){ //if currency was converted, convert back  
                    $tax_deductable=exchange_currenncy($tax_deductable,$current_working_currency,true);
                    $net_salary=exchange_currenncy($net_salary,$current_working_currency,true); //true indicates to revert the calculation
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
        if($tax_band == "tax_band_1"){
            if($person_tax_band == "tax_band_4" && $person["companycar"]=="y"){
                $salary -= $tax_band["maxsalary"];
            }elseif($person_tax_band == "tax_band_4"){
                $salary -= 0.5*$tax_band["maxsalary"];
            }elseif($person["companycar"]=="y"){
                $salary -= $tax_band["maxsalary"];
            }else{
                continue;
            }
        }
        $salary -= $tax_band["maxsalary"]; //takes away max band to work out what is left
        $tax_deductable += $tax_band["maxsalary"]*($tax_band["rate"]/100);
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


?>