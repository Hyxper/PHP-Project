<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Main Page</title>
</head>
<body>

    <?php
        require __DIR__ . '/functions.php';

        $personel = create_personel_data();
        $tax_information = create_tax_data();

        // echo "<pre>";
     
        // echo "is not supported. Supported functions are:<br>";
        // echo "<ul>";
        // foreach(check_currency_functions() as $function){
        //     echo "<li>".$function."</li>";
        // }
        // echo "</ul>";
        // // print_r($user_defined_funcs[0]);
    //    $currency = "";

    //    if(!$currency){
    //        echo "blank";
    //    }

        // echo "</pre>";

        // foreach($personel as $person){
        // print_r(calculate_standard_tax($person,$tax_information));
        // echo "<br>";
        // }









//         function currency_conversion($currency_convert_from){
//         $arrayofconversion = array();
//         $availablecurrencies = array();
//         $api_key = "04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8";
        
//         $curl = curl_init();

//         curl_setopt_array($curl, [
//             CURLOPT_URL => "https://currency-exchange.p.rapidapi.com/listquotes",
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_FOLLOWLOCATION => true,
//             CURLOPT_ENCODING => "",
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 30,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => "GET",
//             CURLOPT_HTTPHEADER => [
//                 "x-rapidapi-host: currency-exchange.p.rapidapi.com",
//                 "x-rapidapi-key: ".$api_key
//             ],
//         ]);
//         $response = curl_exec($curl);
//         $err = curl_error($curl);
//         curl_close($curl);
//         if ($err) {
//             echo "cURL Error when checking available currencies on API #:" . $err;
//         } else {
            
//         }
        
//         foreach($availablecurrencies as $currency){
//             if($currency == $currency_convert_from){
//                 throw new Exception (available_functions($availablecurrencies,"'".$currency_convert_from."' is not supported by API. Supported currencies are:"));
//             }
//         }


//         foreach(check_currency_functions() as $function){
//         $currency_convert_to = strtoupper(substr($function,-3));   
//         $curl = curl_init();
//         curl_setopt_array($curl, [
//             CURLOPT_URL => "https://currency-exchange.p.rapidapi.com/exchange?from=".$currency_convert_from."&to=".$currency_convert_to,
//             // CURLOPT_URL => "https://currency-exchange.p.rapidapi.com/exchange?from=SGD&to=USD&q=1.0",
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_FOLLOWLOCATION => true,
//             CURLOPT_ENCODING => "",
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 30,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => "GET",
//             CURLOPT_HTTPHEADER => [
//                 "x-rapidapi-host: currency-exchange.p.rapidapi.com",
//                 "x-rapidapi-key: ".$api_key
//             ],
//         ]);
//         $response = curl_exec($curl);
//         $err = curl_error($curl);
//         curl_close($curl);    
//         if ($err) {
//             echo "cURL Error #:" . $err;
//         } else {
//             $arrayofconversion[$currency_convert_to] = $response;
//         }
    
//     }
//         return $arrayofconversion; 
// }

        
//         // echo "<pre>";
//         // print_r(currency_conversion("Eff",2.0));
//         // echo "</pre>";
    
//         try{
//             return currency_conversion("eff");
//             }
//             catch(Exception $e){
//             echo $e->getMessage();
//             exit();
//             }


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
		"x-rapidapi-key: 04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8"
	],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
    $available_currencies=array();
    foreach(explode('"',$response) as $str){
        if(!str_contains($str,",")){
            array_push($available_currencies,$str);
    }  elseif(!str_contains($str,"[")){
            array_push($available_currencies,$str);
    } elseif(!str_contains($str,"]")){
            array_push($available_currencies,$str);
    }
  }
}


echo"<pre>";
	 print_r($available_currencies);
echo"</pre>";


























    ?>

</body>
</html>