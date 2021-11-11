<?php

session_start();
require __DIR__ . '/dompdf/autoload.inc.php';

if (isset( $_SESSION["pdf_details"])==false){
    echo "ERROR: details to generate PDF have not been set.";
    exit;
}

$details = $_SESSION["pdf_details"];

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
            <th colspan="4" rowspan="1" class="header">PAYSLIP</th>
        </tr>
        <tr>
            <th colspan="2">Name:'.$details["firstname"]." ".$details["lastname"].'</th>
            <th colspan="2">Role: '.$details["jobtitle"].'</th>
        </tr>
        <tr>
            <th class="headings">Earnings</th>
            <th class="headings">Amount</th>
            <th class="headings">Deductions</th>
            <th class="headings">Amount</th>
        </tr>
        <tr>
            <td>Basic</td>
            <td class="content">'.$details["calculated_salary_and_tax_info"]["salary_month"].'</td>
            <td>Tax</td>
            <td class="content">'.$details["calculated_salary_and_tax_info"]["tax_month"].'</td>
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
            <td class="content">'.$details["calculated_salary_and_tax_info"]["salary_month"].'</td>
            <td>Total Deductions</td>
            <td class="content">'.$details["calculated_salary_and_tax_info"]["tax_month"].'</td>
        </tr>
        <tr>
            <td colspan="4" rowspan="1" class="footer">Net Pay: '.$details["calculated_salary_and_tax_info"]["net_salary_month"].'</td>
        </tr>
    </table>
</body>');


// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream($details["id"]."_"."payslip")

?>



