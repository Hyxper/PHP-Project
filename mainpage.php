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

    
      

       
        <nav class='navbar navbar-light bg-warning justify-content-center h2 mb-0'>
        <span class='navbar-brand mb-0'>Jack's PHP Project</span>
        </nav>
        
        
        
        <table class='table table-bordered table-light table-hover table-striped text-center'>
            <thead class='text-center'>
            <tr>
                <th scope='col'>ID</th>
                <th scope='col'>Photo</th>
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
                <th scope='col'>Records</th>
            </tr>
            </thead>
            <tbody>
        <?php

        require __DIR__ . '/functions.php';

        session_start();
        $GBP_rates = currency_conversion("GBP");
        $personel = create_personel_data("GBP",$GBP_rates);
        $tax_information = create_tax_data();

        foreach($personel as $person){
            $returned_values=calculate_standard_tax($person, $tax_information, "GBP", $GBP_rates);
            $ID_name = $person["id"]."_".$person["firstname"]."_".$person["lastname"];
            $_SESSION[$ID_name] = $person; //$returned_values;
            $_SESSION[$ID_name]["calculated_salary_and_tax_info"]=$returned_values;
       echo
       "
            <tr>
                <th scope='row' id='".$ID_name."_ID'>".$person["id"]."</th>
                <td id='".$ID_name."_photo' class='text-center'> <a href='https://placeholder.com'><img src='https://via.placeholder.com/100'></td>
                <td id='".$ID_name."_lastname'>".$person["lastname"]."</td>
                <td id='".$ID_name."_firstname'>".$person["firstname"]."</td>
                <td id='".$ID_name."_jobtitle'>".$person["jobtitle"]."</td>
                <td id='".$ID_name."_department'>".$person["department"]."</td>
                <td id='".$ID_name."_salary_per_year'>".$returned_values["salary_year"]."</td>
                <td id='".$ID_name."_tax_per_year' class='text-danger'>".$returned_values["tax_year"]."</td>
                <td id='".$ID_name."_net_salary_per_year'class='text-success'>".$returned_values["net_salary_year"]."</td>
                <td id='".$ID_name."_salary_per_month'>".$returned_values["salary_month"]."</td>
                <td id='".$ID_name."_tax_per_month'class='text-danger'>".$returned_values["tax_month"]."</td>
                <td id='".$ID_name."_salary_per_month' class='text-success'>".$returned_values["net_salary_month"]."</td>
                <td id='".$ID_name."_Records' class='text-success'><a href='person.php?person=".$ID_name."'><ul>View Record</ul></a></td>
            </tr>";
        }
        ?>

            </tbody>   
        </table>
        
  




















 

</body>
</html>