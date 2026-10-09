<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "campus_hub";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>