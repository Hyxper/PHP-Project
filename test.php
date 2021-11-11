<?php

session_start();

$_SESSION["3565_Chantel_Hoffman"]="3565_Chantel_Hoffman";
require __DIR__ . '/functions.php';
// require_once 'C:/xampp/htdocs/Project/PHP-Project/dompdf/autoload.inc.php';
require_once __DIR__.'/dompdf/autoload.inc.php';


$yolo = "yeppers";
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml('
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 80%;
  margin: auto;
  padding: 10px;
  margin-top:60px;
}

td, th {
  border: 3px solid #000000;
  text-align: left;
  padding: 8px;
}
.header{
    text-align: center;
    background-color: #99ffeb;
    font-weight: bold;
    font-size: x-large;
}

.headings{
    text-align: center;
    background-color: #ffcc00;
    font-weight: bold;
}

.content{
    background-color: #ccffe6;
}

.footer{
    text-align: center;
    background-color: #ffff66;
    font-weight: bold;
}

.center {
  
}
</style>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table>
        <tr>
            <th colspan="4" rowspan="1" class="header">PAYSLIP FOR</th>
        </tr>
        <tr>
            <th colspan="2">Name: </th>
            <th colspan="2">Role: </th>
        </tr>
        <tr>
            <th class="headings">Earnings</th>
            <th class="headings">Amount</th>
            <th class="headings">Deductions</th>
            <th class="headings">Amount</th>
        </tr>
        <tr>
            <td>Basic</td>
            <td class="content">999</td>
            <td>Tax</td>
            <td class="content">999</td>
        </tr>
        <tr>
            <td>Leave Encashment</td>
            <td class="content">0.00</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Holiday Wages</td>
            <td class="content">0.00</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Special Allowance</td>
            <td class="content">0.00</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Bonus</td>
            <td class="content">0.00</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Individual Incentive</td>
            <td class="content">0.00</td>
            <td>-</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Total Earnings</td>
            <td class="content">0.00</td>
            <td>Total Deductions</td>
            <td class="content">0.00</td>
        </tr>
        <tr>
            <td colspan="4" rowspan="1" class="footer">Net Pay: 999</td>
        </tr>
    </table>
</body>');


// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();

?>



