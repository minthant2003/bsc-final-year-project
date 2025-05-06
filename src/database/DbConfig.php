<?php
$server = "localhost";
$userName = "root"; // use your local DB username
$password = ""; // user your local DB password
$dbName = "final_year_project_db";

// use your local port no
$conn = mysqli_connect($server, $userName, $password, $dbName, 3307);
if (!$conn) {
    die("Db connection error: " . mysqli_connect_error());
}
// echo "Db connection is successful.";