<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_rayzen";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Log the error instead of displaying it
    error_log("Database connection failed: " . $conn->connect_error);
    // Display a generic error message to the user
    die("A database error occurred.");
}

// Set the character set
$conn->set_charset("utf8mb4");
?>