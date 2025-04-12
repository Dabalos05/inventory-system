<?php
session_start();
include 'db_connect.php';

$notifications = [];
$lowStockCount = 0; // Initialize low stock count

// Low Stock Notifications
$lowStockQuery = $conn->prepare("SELECT id, name, quantity FROM products WHERE quantity < 10");
$lowStockQuery->execute();
$lowStockResult = $lowStockQuery->get_result();

while ($row = $lowStockResult->fetch_assoc()) {
    $notifications[] = [
        "product_id" => $row['id'],
        "product_name" => $row['name'],
        "current_stock" => $row['quantity'],
    ];
    $lowStockCount++; // Increment count for each low stock item
}
$lowStockQuery->close();

// New Sales Notifications (Last 24 Hours)
$newSalesQuery = $conn->prepare("SELECT p.name, s.sale_amount, s.sale_date FROM sales_records s 
                                JOIN products p ON s.product_id = p.id 
                                WHERE s.sale_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
$newSalesQuery->execute();
$newSalesResult = $newSalesQuery->get_result();

while ($row = $newSalesResult->fetch_assoc()) {
    $notifications[] = [
        "message" => "New sale: {$row['name']} (₱{$row['sale_amount']})",
    ];
}
$newSalesQuery->close();

// Return JSON Response with low stock count
header('Content-Type: application/json');
echo json_encode(["notifications" => $notifications, "lowStockCount" => $lowStockCount]);
?>