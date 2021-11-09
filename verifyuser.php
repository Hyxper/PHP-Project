<?php

session_start();
require __DIR__ . '/functions.php';
set_timezone("GMT");

$logindetails = array("username"=> $_POST["username"],"password" => $_POST["password"]); // what to check

//read JSON containing user data
$userfile ="./JSON/users.json";
try{
    if(is_json($userfile)==true){
        $json = file_get_contents($userfile);
        $userdata = json_decode($json,true); //stores this into an array
    }
}
catch(Exception $e){
    echo $e->getMessage();
    exit();
    }


foreach($userdata as $user){
    if($logindetails["username"] != $user["username"]){
        $_SESSION["usercreds"] = 1; //assigns 1 to session if username is incorrect, will tell user on login page
        
    }elseif($logindetails["password"] != $user["password"]){
        $_SESSION["usercreds"] = 2; //assigns 2 to session if password is incorrect, will tell user on login page
        break;
    }else{
        $_SESSION["usercreds"] = 0; //assigns 0 to session, essentially makes variable "clean" so it does not get caught if below.   
        break;
    }
  
}

if($_SESSION["usercreds"] == 1 or $_SESSION["usercreds"] == 2){
        header('Location: ' . $_SERVER['HTTP_REFERER']); //this returns the user to the login page informing of either unknown user or wrong password
        exit; //need exit after each header call so page does not continue loading
    }else{     
        $_SESSION["userdetails"] = array("user"=>$user["username"], "permission" => $user["permission"]);
        header("location: mainpage.php"); //this will send the user to the main page, ideal outcome as both username and password were correct
        exit; //need exit after each header call so page does not continue loading
}


?>