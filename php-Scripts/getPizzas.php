<?php

$host = 'localhost';
$db_name = 'pizzawebsite';
$username = 'root';
$password = '';

$DB_Connect = mysqli_connect($host, $username, $password)
            or exit ("Es konnte keine Verbindung zur Datenbank ergestellt werden");

$DB_SELECT = mysqli_select_db($DB_Connect, $db_name)
            or exit ("Es konnte keine Verbindung zur Datenbank ergestellt werden");

?>
