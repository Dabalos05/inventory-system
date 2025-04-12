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
    echo '<div id="permissionAlert" class="permission-alert">
              <div class="alert-content">
                  <p>You do not have permission to access this page.</p>
                  <button id="backButton">OK</button>
              </div>
          </div>

          <script>
              document.getElementById("backButton").addEventListener("click", function() {
                  window.history.back();
              });
          </script>';
    echo '<style>
              .permission-alert {
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  position: fixed;
                  top: 0;
                  left: 0;
                  width: 100%;
                  height: 100%;
                  background-color: rgba(0, 0, 0, 0.5);
                  z-index: 1000;
              }

              .alert-content {
                  background-color: white;
                  padding: 20px;
                  border-radius: 8px;
                  text-align: center;
                  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
              }

              .alert-content p {
                  margin-bottom: 20px;
                  font-size: 16px;
                  color: #333;
              }

              #backButton {
                  background-color: #007bff;
                  color: white;
                  padding: 10px 20px;
                  border: none;
                  border-radius: 5px;
                  cursor: pointer;
                  font-size: 16px;
              }

              #backButton:hover {
                  background-color: #0056b3;
              }
          </style>';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (empty($name) || !is_numeric($price) || !is_numeric($quantity) || $price < 0 || $quantity < 0) {
        $_SESSION['message'] = "Invalid input. Please check your data.";
        $_SESSION['message_type'] = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("sdd", $name, $price, $quantity);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Product added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to add product: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
        }
    }
    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Product</h2>

        <form method="POST" class="mt-3">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Product Name" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" name="price" id="price" class="form-control" placeholder="Price" required step="0.01">
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Quantity" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>