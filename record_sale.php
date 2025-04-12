<?php
include 'db_connect.php';

var_dump($_POST); // Debugging

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $sale_amount = $_POST['sale_amount'];
    $sale_date = date('Y-m-d H:i:s');

    $sql = "INSERT INTO sales_records (product_id, quantity, sale_amount, sale_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iids", $product_id, $quantity, $sale_amount, $sale_date);

    if ($stmt->execute()) {
        echo "Sale recorded successfully.";
    } else {
        echo "Error recording sale: " . $stmt->error;
        error_log("Database Error: " . $stmt->error); // Log to server's error log
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>