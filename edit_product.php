<?php
session_start();

// Include connection
include 'db_connect.php';

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

// Get product ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch product details
    $result = $conn->query("SELECT * FROM products WHERE id = $id");

    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];

            // Update product details
            $sql = "UPDATE products SET name = '$name', price = '$price', quantity = '$quantity' WHERE id = $id";

            if ($conn->query($sql)) {
                // Set session messages here!
                $_SESSION['message'] = "Product updated successfully!";
                $_SESSION['message_type'] = "success";

                header("Location: products.php");
                exit();
            } else {
                die("Error updating product: " . $conn->error);
            }
        }
    } else {
        die("Product not found.");
    }
} else {
    die("Invalid product ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-group input.form-control[readonly] {
            background-color: #e9ecef !important;
            opacity: 1 !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <a href="products.php" class="btn btn-secondary mb-3">← Back to Products</a>
        <h2 class="text-center mb-4">Edit Product</h2>

        <form action="" method="POST" class="border p-4 shadow-sm bg-light">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" class="form-control" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>