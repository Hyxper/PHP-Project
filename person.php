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
        session_start();
        require __DIR__ . '/functions.php';
        $person = $_GET["person"];
        if (isset($_SESSION[$person])){
            $person = $_SESSION[$person];
        }
    ?>  


       
        <nav class='navbar navbar-light bg-warning justify-content-center h2 mb-0'>
        <span class='navbar-brand mb-0'>Jack's PHP Project</span>
        </nav>
        
        <div class="container-fluid vh-100">

            <div class="d-flex flex-row h-50">

                <div class="col-2 d-flex align-items-center justify-content-center h-100">
                     <a href='https://placeholder.com'><img src='https://via.placeholder.com/300' class=""></a>
                </div>

                <div class="col-5 h-100 d-flex justify-content-center align-items-center flex-column">
                    <h3><u>Personal Info</u></h3>
                    <div class="container d-flex flex-column flex-wrap h-75 justify-content-center align-items-center">
                    <div class="container d-inline-flex flex-column w-25">
                        <h5>ID</h5>
                        <p><?php echo $person["id"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>Surname</h5>
                        <p><?php echo $person["lastname"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>First Name</h5>
                        <p><?php echo $person["firstname"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>Home Address</h5>
                        <p><?php echo $person["homeaddress"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>Personal E-mail</h5>
                        <p><?php echo $person["homeemail"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>Phone Number</h5>
                        <p><?php echo $person["phone"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>NI Number</h5>
                        <p><?php echo $person["nationalinsurance"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>DOB</h5>
                        <p><?php echo $person["dob"] ?></p>
                    </div>
                    </div>
                    
                </div>

                <div class="col-5 h-100 d-flex justify-content-center align-items-center flex-column">
                    <h3><u>Employment Info</u></h3>
                    <div class="container d-flex flex-column flex-wrap h-75 justify-content-center align-items-center">

                    <div class="container d-inline-flex flex-column w-25">
                        <h5>Job Title</h5>
                        <p><?php echo $person["jobtitle"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>Department</h5>
                        <p><?php echo $person["department"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>Report to</h5>
                        <p><?php echo $person["linemanager"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>Start Date</h5>
                        <p><?php echo $person["employmentstart"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>E-mail</h5>
                        <p><?php echo $person["email"] ?></p>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>Other Roles</h5>
                        <?php     
                        echo "<ul>";
                        if(count($person["otherroles"]) < 1){
                            echo "<li>N/A</li>";
                        }else{
                            foreach($person["otherroles"] as $role){
                                echo "<li>".$role."</li>";
                            }
                        }
                        echo "</ul>";
                         ?>
                    </div>

                    <div class="container d-flex flex-column w-25">
                        <h5>Company Car</h5>
                        <p><?php 
                        if($person["companycar"] == "y"){
                            echo "Yes";
                        }else{
                            echo "No";
                        }
                        ?></p>
                    </div>
                </div>              
                </div>   
            </div>

            <div class="d-flex flex-row h-50"> 
<!-- Template copied from https://bbbootstrap.com/snippets/bootstrap-5-employee-salary-slip-template-16254247  -->
            <div class="container mt-5 mb-5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center lh-1 mb-2">
                            <h6 class="fw-bold">Payslip</h6> <span class="fw-normal">Payment slip for the month of June 2021</span>
                        </div>
                        <div class="d-flex justify-content-end"> <span>Working Branch:PGSUS</span> </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div> <span class="fw-bolder">Name</span> <small class="ms-3"><?php echo $person["firstname"]." ".$person["lastname"]?></small> </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div> <span class="fw-bolder">NOD</span> <small class="ms-3">28</small> </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div> <span class="fw-bolder">ESI No.</span> <small class="ms-3"></small> </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div> <span class="fw-bolder">Mode of Pay</span> <small class="ms-3">Transfer</small> </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div> <span class="fw-bolder">Designation</span> <small class="ms-3">Staff</small> </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div> <span class="fw-bolder">Ac No.</span> <small class="ms-3">*******0701</small> </div>
                                    </div>
                                </div>
                            </div>
                            <table class="mt-4 table table-bordered">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th scope="col">Earnings</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Deductions</th>
                                        <th scope="col">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">Basic</th>
                                        <td><?php echo $person["calculated_salary_and_tax_info"]["salary_month"]?></td>
                                        <td>Tax</td>
                                        <td><?php echo $person["calculated_salary_and_tax_info"]["tax_month"]?></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Leave Encashment</th>
                                        <td>0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Holiday Wages</th>
                                        <td>0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Special Allowance</th>
                                        <td>0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Bonus</th>
                                        <td>0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Individual Incentive</th>
                                        <td>0.00</td>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr class="border-top">
                                        <th scope="row">Total Earning</th>
                                        <td><?php echo $person["calculated_salary_and_tax_info"]["salary_month"]?></td>
                                        <td>Total Deductions</td>
                                        <td><?php echo $person["calculated_salary_and_tax_info"]["tax_month"]?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row pb-5">
                            <div class="col-md-4"> <br> <span class="fw-bold">Net Pay: <?php echo $person["calculated_salary_and_tax_info"]["net_salary_month"]?></span> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


            </tbody>   
        </table>
        
    </body>
</html>