<?php
require __DIR__ . '/functions.php';

function is_json($file){
    $json = file_get_contents($file);
    json_decode($json);
    if(!json_last_error() === JSON_ERROR_NONE){
     throw new Exception("File located at ".$file." is not JSON.");
    }
    if(json_last_error() !==0){
        throw new Exception("Error has been found when passing JSON file:".json_last_error_msg());
    }
    return true;
 }

// echo"<pre>";print_r($testarr);echo"</pre>";




function load_tax_file($file){
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
                echo "not numeric";
            }
            echo "present";
        }else{
        echo "not present";
        }
        if(array_key_exists("maxsalary",$criteria)){
            if(!is_numeric($criteria["maxsalary"])){
                echo "not numeric";
            }
            echo "present";
        }else{
        echo "not present";
        }
        if(array_key_exists("rate",$criteria)){
            if(!is_numeric($criteria["rate"])){
                echo "not numeric";
            }
            echo "present";
        }else{
        echo "not present";
        }
     
    
       
        echo"<pre>";
        print_r($criteria);
        echo"</pre>";
        }
    
    }


try{
    echo load_tax_file("./tax-tables.json");
}catch(Exception $e){
    echo $e->getMessage();
    exit();
    }






?>