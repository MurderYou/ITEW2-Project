<?php

$host     = "localhost";     // XAMPP default host
$user     = "root";          // XAMPP default username
$password = "";              // XAMPP default password (empty)
$database = "student_records_db";
 
// Create connection using mysqli
$conn = mysqli_connect($host, $user, $password, $database);
 
// Check if connection failed
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// If we reach here, connection is successful!
?>