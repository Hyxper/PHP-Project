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

        // print_r($GBP_rates);
        // echo "<br>";


        // try{
        //     print_r(currency_conversion("eur"));
        //     }
        //     catch(Exception $e){
        //     echo $e->getMessage();
        //     exit();
        //     }

        echo 
        "
        <nav class='navbar navbar-light bg-warning justify-content-center h2 mb-0'>
        <span class='navbar-brand mb-0'>Jack's PHP Project</span>
        </nav>
        
        
        
        <table class='table table-bordered table-dark table-hover table-striped'>
            <thead class='text-center'>
            <tr>
                <th scope='col'>ID</th>
                <th scope='col'>Surname</th>
                <th scope='col'>First name</th>
                <th scope='col'>Job title</th>
                <th scope='col'>Department</th>
                <th scope='col'>Salary</th>
                <th scope='col'>Tax per year</th>
                <th scope='col'>Net per year</th>
                <th scope='col'>Salary per month</th>
                <th scope='col'>Tax per month</th>
                <th scope='col'>Net pay per month</th>
            </tr>
            </thead>
            <tbody>";
        foreach($personel as $person){
            $returned_values=calculate_standard_tax($person, $tax_information, "GBP", $GBP_rates);
       echo
       "
            <tr>
                <th scope='row'>".$person["id"]."</th>
                <td>".$person["lastname"]."</td>
                <td>".$person["firstname"]."</td>
                <td>".$person["jobtitle"]."</td>
                <td>".$person["department"]."</td>
                <td>".$returned_values["salary_year"]."</td>
                <td>".$returned_values["tax_year"]."</td>
                <td>".$returned_values["net_salary_year"]."</td>
                <td>".$returned_values["salary_month"]."</td>
                <td>".$returned_values["tax_month"]."</td>
                <td>".$returned_values["net_salary_month"]."</td>
            </tr>";
        }
        echo"</tbody>   
         </table"

        // print_r($GBP_rates);
    
        // echo var_dump(format_currency_USD(exchange_currenncy($GBP_rates,31999.022570424,"USD",true)))."<br>";
        // echo "<br>";
        // echo calculate_standard_tax($personel["8734_Laura_Waterman"], $tax_information, "GBP", $GBP_rates)."<br>";
        // echo 31999.022570424*1.375042;       




















    ?>

</body>
</html>