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

    //    create_personel_data();
    //    create_taxbands();
    
       $personeldatapre = array();
       $personeldata = array();
       $json = file_get_contents("./employees-final.json");
       $personeldatapre = json_decode($json,true);
   
       
       foreach($personeldatapre as $person){
           $elementrename =  $person["id"]."_".$person["firstname"]."_".$person["lastname"];
           $personeldata[$elementrename] = $person;
           $elementrename=""; 
       } //this adds the ID, firstname and last name to the parent array element, rather than normal.
   
       $taxdatapre = array();
       $taxdata = array();
       $json = file_get_contents("./tax-tables.json");
       $taxdatapre = json_decode($json,true);
   
   
       foreach($taxdatapre as $tax){
            $elementrename =  "tax_band_".$tax["id"];
            $taxdata[$elementrename] = $tax;
            $elementrename=""; 
       } //this creates an array from tax rate JSON, renaming the parent element to the tax band, and removing the name element
   

       foreach($personeldata as $person){
           $salarytocheck = $person["salary"];
           if($salarytocheck <= $taxdata["tax_band_1"]["maxsalary"]){ // 10k
            array_push($person, array("tax_band"=>"tax_band_1"));
           }elseif($salarytocheck <= $taxdata["tax_band_2"]["maxsalary"]){
            array_push($person, array("tax_band"=>"tax_band_2"));
           }elseif($salarytocheck <= $taxdata["tax_band_3"]["maxsalary"]){
            array_push($person, array("tax_band"=>"tax_band_3"));
           }elseif($salarytocheck > $taxdata["tax_band_4"]["minsalary"]){
            array_push($person, array("tax_band"=>"tax_band_4"));
       }
    }

echo "<pre>";
print_r($personeldata);
echo "</pre>";


    ?>

</body>
</html>