<?php
$host = "localhost";
$user = "root";  // default XAMPP user
$pass = "";      // default password empty
$db   = "test";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

