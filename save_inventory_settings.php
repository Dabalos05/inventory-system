<?php
// save_inventory_settings.php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process Stock Threshold Alerts
    $lowStockThreshold = $_POST['lowStockThreshold'];

    // Save threshold to database
    // Example:
    // $stmt = $conn->prepare("UPDATE system_settings SET low_stock_threshold = ?");
    // $stmt->bind_param("i", $lowStockThreshold);
    // $stmt->execute();

    // Process Automatic Stock Adjustment
    $autoStockAdjust = isset($_POST['autoStockAdjust']) ? 1 : 0;

    // Save auto adjust setting to database
    // Example:
    // $stmt = $conn->prepare("UPDATE system_settings SET auto_stock_adjust = ?");
    // $stmt->bind_param("i", $autoStockAdjust);
    // $stmt->execute();

    // Redirect back to settings page or show success message
    header("Location: settings.php?inventory_settings_saved=1"); // Example: Add a query parameter
    exit();
} else {
    // Handle cases where the form was not submitted via POST
    header("Location: settings.php");
    exit();
}

$conn->close();
?>