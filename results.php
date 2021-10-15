<?php
session_start();
// print_r($_GET);
// exit;

// echo "<br>".$_GET["username"]."</br>";
// echo "<br>".$_GET["password"]."</br>";

//echo "SUCCESSFUL";

$userdata = array();
$json = file_get_contents("./users.json");
$userdata = json_decode($json,true);
// echo "<pre>";
// print_r($_SESSION["userdetails"]);
// echo "</pre>";
echo "<pre>";
print_r($_SESSION["userdetails"]);
echo "</pre>";

?>