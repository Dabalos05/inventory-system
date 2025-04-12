<?php
// Database Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_rayzen";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log the error (Important for debugging and production)
    error_log("Database connection failed: " . $conn->connect_error);

    // Provide a user-friendly error message (Avoid displaying sensitive details)
    die("Sorry, there was a problem connecting to the database. Please try again later.");
}

// Set character set to utf8 (Highly recommended)
$conn->set_charset("utf8");

// You can add more configurations here if needed

// Example: Setting timezone for database interactions
// $conn->query("SET time_zone = '+00:00'"); // Or your desired timezone

// You can also add a function to close the connection, but it's usually closed automatically at the end of the script.
?>