<?php
// record_transaction.php

include 'db_connect.php'; // Include your database connection

// Check if the required data is sent via POST
if (isset($_POST['product_id']) && isset($_POST['quantity']) && isset($_POST['transaction_type'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $transaction_type = $_POST['transaction_type'];
    $transaction_date = date("Y-m-d H:i:s"); // Current date and time

    // Prepare and execute the INSERT query
    $insertTransactionQuery = $conn->prepare("INSERT INTO transactions (product_id, quantity, transaction_date, transaction_type) VALUES (?, ?, ?, ?)");
    $insertTransactionQuery->bind_param("iiss", $product_id, $quantity, $transaction_date, $transaction_type);

    if ($insertTransactionQuery->execute()) {
        echo "Transaction recorded successfully.";
    } else {
        echo "Error recording transaction: " . $insertTransactionQuery->error;
    }

    $insertTransactionQuery->close();
} else {
    echo "Missing required data.";
}

$conn->close(); // Close the database connection
?>