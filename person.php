<?php
session_start();

$person = $_GET["person"];


if (isset($_SESSION[$person])) {
    echo "<pre>";
    print_r($_SESSION[$person]);
    echo "</pre>";
}



?>