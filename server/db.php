<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "wonderpets_db";

($conn = mysqli_connect($host, $username, $password, $database)) or
    die("Connection failed: " . mysqli_connect_error());

$use = mysqli_select_db($conn, $database);
mysqli_set_charset($conn, "utf8");
?>
