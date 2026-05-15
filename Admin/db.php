<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sis_database"; // Change to your DB name

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
