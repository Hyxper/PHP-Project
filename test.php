<?php
require __DIR__ . '/functions.php';





session_start();
$GLOBALS["working_currency"]="USD";//define set currency to use--------------------NEEDED--------------------     
// try{
$GLOBALS["currency_rate"] = currency_conversion($GLOBALS["working_currency"]); //calculate currency exchange rates (works out 1 > other currencies. In this case will call api to check what £1 is in USD, EUR)--------------------NEEDED--------------------
// }catch(Exception $e){
//     echo $e->getMessage();//if the currency isnt supported, this will catch an exception and report to the user what is available.
//     exit();
// }

// $json = file_get_contents("./JSON/conversion_rates.json");
// $conversion_backup = json_decode($json,true);


// echo "<pre>";
// print_r(API_Invoke("https://currency-exchange.p.rapidapi.com/exchange?from=GBP&to=USD","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8")); //define tax table to use --------------------NEEDED--------------------
// echo "</pre>";

// echo "<pre>";
// print_r(currency_conversion($GLOBALS["working_currency"])); //define tax table to use --------------------NEEDED--------------------
// echo "</pre>";





// echo API_Invoke("https://currency-exchange.p.rapidapi.com/listquotes","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8")."<br>";
// echo (API_Invoke("https://currency-exchange.p.rapidapi.com/exchange?from=GBP&to=USD","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8"));
// echo "<br>";
// echo 10+API_Invoke("https://currency-exchange.p.rapidapi.com/exchange?from=GBP&to=USD","04f44a4054msh92583eb306794d9p1f7b99jsn27c76c1fd7d8");

// $conversion_backup["GBP_rates"]["USD"] = "gotem";
// $updater_string = json_encode($conversion_backup);
// file_put_contents("./JSON/conversion_rates.json",$updater_string);

// set_timezone("GMT");
// echo date('D M j G:i:s a');

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

<div class="container-fluid">
            <div class="row d-flex justify-content-center">
                <div class="container d-flex w-50 border border-secondary">
                    <form class="form-inline" action="test.php" method="POST">
                        <div class="form-group">
                            <label for="currency_sel">Select Currency Type:</label>
                            <select type="text" class="form-control" id="currency_sel" name="currency_sel">
                                <?php
                                    foreach(check_currency_functions() as $currency){
                                        $currency_returned = strtoupper(substr($currency,-3));
                                        echo "<option>".$currency_returned."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="currency_sel">Select Tax Band:</label>
                            <select type="text" class="form-control" id="tax_sel" name="tax_sel">
                                <?php
                                    foreach(check_tax_files("./JSON",true) as $file){
                                        echo "<option>".$file["reigon"]."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <button class="btn btn-outline-dark btn-lg px-5" type="submit" name="submit">Login</button>
                    </form>             
                </div>
        </div>
</div>



</body>
</html>

<?php
if(isset($_POST["currency_sel"])==false && isset($_POST["tax_sel"])==false){
    $selectedcur = "GBP";
    $selectedtax = "British";
}else{
    $selectedcur = $_POST["currency_sel"];
    $selectedtax = $_POST["tax_sel"];
}

echo $selectedcur;
echo "<br>";
echo $selectedtax;


?>



