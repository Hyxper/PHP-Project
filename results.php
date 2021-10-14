<?php

// print_r($_GET);
// exit;

// echo "<br>".$_GET["username"]."</br>";
// echo "<br>".$_GET["password"]."</br>";

//echo "SUCCESSFUL";

$userdata = array();
$json = file_get_contents("./users.json");
$userdata = json_decode($json,true);

print_r($userdata);

?>