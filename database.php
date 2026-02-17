<?php
session_start();
$servername = "localhost";
$username = "root"; // default XAMPP username
$password = "";     // default XAMPP password
$dbname = "immunitrack_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
