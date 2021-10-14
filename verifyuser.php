<?php

session_start();

$logindetails = array("username"=> $_POST["username"],"password" => $_POST["password"]);


if($logindetails["username"] != "Admin"){
    $_SESSION["usercreds"] = 1; 
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit; //need exit after each header call so page does not continue loading
}elseif($logindetails["password"] != "password"){
    $_SESSION["usercreds"] = 2; 
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit; //need exit after each header call so page does not continue loading
}else{
    header("location: results.php");
    exit; //need exit after each header call so page does not continue loading
}


?>