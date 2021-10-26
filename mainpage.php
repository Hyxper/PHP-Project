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
        $GBP_rates = currency_conversion("GBP");

        print_r($GBP_rates);



        // try{
        //     print_r(currency_conversion("eur"));
        //     }
        //     catch(Exception $e){
        //     echo $e->getMessage();
        //     exit();
        //     }

        




























    ?>

</body>
</html>