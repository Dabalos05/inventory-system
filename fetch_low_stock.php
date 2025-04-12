<?php
date_default_timezone_set('Asia/Manila');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Fetch low stock products (quantity <= 5)
$lowStockProductsQuery = $conn->query("SELECT name, id, quantity FROM products WHERE quantity <= 5 ORDER BY quantity ASC");
$lowStockProducts = [];
while ($row = $lowStockProductsQuery->fetch_assoc()) {
    $lowStockProducts[] = $row;
}

// Output data as JSON
$data = [
    'lowStockProducts' => $lowStockProducts,
];

header('Content-Type: application/json');
echo json_encode($data);
?>