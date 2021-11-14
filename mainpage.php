<?php 
require __DIR__ . '/functions.php';
session_start();
create_check_project_file("tax-tables_GBP.json");
set_timezone("GMT");
$json_file_locations = "./JSON/";

            if(isset($_SESSION["userdetails"])==false){
                echo "Please login through login page first!";
                exit;
            }

            if(isset($_POST["currency_sel"])==false && isset($_POST["tax_sel"])==false){ //SETS DEFAULT VALUES FOR FORM NEEDS TO BE DEFAULT ON LOAD
                $selectedcur = "GBP"; //default
                $selectedtax = "British"; //default
                foreach(check_tax_files($json_file_locations,true) as $tax_file){ //decode what I have put into tax select, makes sure right tax data is loaded.
                    if ($tax_file["reigon"] == $selectedtax){
                        $file_to_load=array("file" => $json_file_locations.$tax_file["file"], "currency"=> $tax_file["code"]);
                    }
                }


            }else{ //if post files have already been set, feed them into the right channels
                $selectedcur = $_POST["currency_sel"];
                $selectedtax = $_POST["tax_sel"];
                foreach(check_tax_files($json_file_locations,true) as $tax_file){ //decode what I have put into tax select, makes sure right tax data is loaded.
                    if ($tax_file["reigon"] == $selectedtax){
                        $file_to_load=array("file" => $json_file_locations.$tax_file["file"], "currency"=> $tax_file["code"]);
                    }
                }
            }

        try{ //wrap all essential functions in a try loop, this ensures any problems will be caught, all exceptions thrown shall be caught.
            $GLOBALS["working_currency"]=$selectedcur;//define set currency to use--------------------NEEDED--------------------     
            $GLOBALS["currency_rate"] = currency_conversion($GLOBALS["working_currency"]); //calculate currency exchange rates (works out 1 > other currencies. In this case will call api to check what £1 is in USD, EUR)--------------------NEEDED--------------------
            $GLOBALS["tax_data"] = create_tax_data($file_to_load["file"],$file_to_load["currency"]); //define tax table to use, ALSO CURRENCY TAX TABLE IS IN..... --------------------NEEDED--------------------
            $personel = create_personel_data($GLOBALS["working_currency"]); //creates our personel data. Feed our working currency, so in case someone is not paid in £, will be able to exchange. Also used for working out in different currency.--------------------NEEDED--------------------
            }
            catch(Exception $e){
            echo $e->getMessage();
            exit();
            }

           
        //try block here will attempt to catch any problems with currency conversion/API interfacing issues
?>

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
        <nav class='navbar navbar-light bg-dark justify-content-center h2 mb-4 border-secondary border-5 border-bottom'>
            <span class='navbar-brand mb-0 text-light'><h1>Pegasus</h1></span>
        </nav>      
    <main>
        <div class="container-fluid">
            <div class="row d-flex justify-content-center">
                <div class="container d-flex w-25 justify-content-center">


                <div class="container w-50">
                    <form class="form" action="mainpage.php" method="POST">


                <div class="row">
                        <div class="form-group p-2">
                            <label for="currency_sel">Select Currency:</label>
                            <select type="text" class="form-control" id="currency_sel" name="currency_sel">
                                <?php
                                    foreach(check_currency_functions() as $currency){
                                        $currency_returned = strtoupper(substr($currency,-3));
                                        echo "<option>".$currency_returned."</option>";
                                    }
                                ?>
                            </select>
                        </div>




                        <div class="form-group p-2">
                            <label for="tax_sel">Select Tax Band:</label>
                            <select type="text" class="form-control" id="tax_sel" name="tax_sel">
                                <?php
                                    foreach(check_tax_files($json_file_locations,true) as $file){
                                        echo "<option>".$file["reigon"]."</option>";
                                    }
                                ?>
                            </select>
                        </div>

                            
                </div>

                <div class="row py-2">
                        <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                </div>
                       
                    </form>
                    </div>

                    <div class="container d-flex align-items-center justify-content-center flex-column">
                       <?php echo "<h5> Current Currency: ".$selectedcur."</h5>" ?>
                       <?php echo "<h5> Current Tax: ".$selectedtax."</h5>" ?>                
                    </div>
             </div>
        </div>
            <div class="row d-flex">
                <div class="col-12 px-5 py-4">
              
                    <table class='table table-bordered table-light table-hover table-striped text-center'>
                        <thead class='text-center'>
                        <tr>
                            <?php
                            $rownames = array("ID","Photo","Surname","First name","Job title", "Department", "Salary",
                            "Tax per year", "Net per year", "Salary per month", "Tax per month", "Net per month", 
                            "Records");

                            foreach ($rownames as $row){
                                echo "<th scope='col'>".$row."</th>";
                            }
                            
                            ?>
                        </tr>
                        </thead>
                        <tbody>
        <?php
        
  
    

        foreach($personel as $person){
            try{
                $returned_values=calculate_standard_tax($person,$GLOBALS["working_currency"]);
            }
            catch(Exception $e){
            echo $e->getMessage();
            exit();
            }
            
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
                <td id='".$ID_name."_Records'>
                <a href='person.php?person=".$ID_name."'><button class='btn p-2 mt-4 btn-dark' type='submit' name='submit'>View Record</button></a>
                </td>
            </tr>";
        }
        ?>

            </tbody>   
            </table>
            </div>
        </div>
        </main>
        
  




















 

</body>
</html>