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

        $GBP_rates = currency_conversion("GBP");
        $EUR_rates = currency_conversion("EUR");
        $personel = create_personel_data("GBP",$GBP_rates);
        $tax_information = create_tax_data();

        print_r($GBP_rates);
        echo "<br>";


        // try{
        //     print_r(currency_conversion("eur"));
        //     }
        //     catch(Exception $e){
        //     echo $e->getMessage();
        //     exit();
        //     }

        foreach($personel as $person){
            echo calculate_standard_tax($person, $tax_information, "GBP", $GBP_rates);
            echo $person["firstname"]." ".$person["lastname"]." <br>";
            // echo $person["currency"]."<br>";
            // echo calculate_standard_tax($person, $tax_information, "EUR", $EUR_rates)."<br>";
        }

        // print_r($GBP_rates);
        echo "<br>";
        // echo format_currency_USD(exchange_currenncy($GBP_rates,31999.022570424,"USD",true))."<br>";
        // echo "<br>";
        // echo calculate_standard_tax($personel["8734_Laura_Waterman"], $tax_information, "GBP", $GBP_rates)."<br>";
        // echo 31999.022570424*1.375042;       




















    ?>

</body>
</html>