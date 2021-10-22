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
        // print_r(calculate_standard_tax($personel["7265_Robert_Holder"],$tax_information));
        // echo "</pre>";

       
        foreach($personel as $key => $person){
            echo $key;
            echo "<pre>";
            echo calculate_standard_tax($person,$tax_information,"GBP");
            echo "</pre>";
        }

        
      
        //$formattedcost = '£' . number_format( (float) $costcounter, 2, '.', ',' ); //chose this because I dont like number format
        // echo "<pre>";
        // print_r($tax_information);
        // echo "</pre>";
    
    
    //    echo calculate_standard_tax($personel["7265_Robert_Holder"]);
        

    ?>

</body>
</html>