<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user is an admin
if ($_SESSION['role'] !== "admin") {
    echo "You do not have permission to access this page.";
    exit(); // Or you could redirect to another page
}

// ... (Rest of your manage_users.php code) ...
?>