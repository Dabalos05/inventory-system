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
        </script>

        <style>
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

// Get product ID from URL and validate
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch product details
    $result = $conn->query("SELECT * FROM products WHERE id = $id");

    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $quantity_sold = $_POST['quantity_sold'];

            if (is_numeric($quantity_sold) && $quantity_sold > 0) { // Validate quantity sold
                if ($product['quantity'] >= $quantity_sold) {
                    $new_quantity = $product['quantity'] - $quantity_sold;

                    if ($conn->query("UPDATE products SET quantity = '$new_quantity' WHERE id = $id")) {
                        // Set session messages here!
                        $_SESSION['message'] = "Product sold successfully!";
                        $_SESSION['message_type'] = "success";

                        header("Location: products.php"); //Removed ?msg=sold, as we are using sessions
                        exit();
                    } else {
                        echo "<script>alert('Error updating stock: " . $conn->error . "');</script>";
                    }
                } else {
                    echo "<script>alert('Insufficient stock!');</script>";
                }
            } else {
                echo "<script>alert('Invalid quantity entered.');</script>";
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
    <title>Sell Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-group {
            margin-bottom: 20px; /* Adjust as needed */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <a href="products.php" class="btn btn-secondary mb-3">← Back to Products</a>
        <h2 class="text-center mb-4">Sell Product</h2>

        <form action="" method="POST" class="border p-4 shadow-sm bg-light">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" class="form-control" value="<?php echo htmlspecialchars($product['quantity']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="quantity_sold">Quantity to Sell:</label>
                <input type="number" name="quantity_sold" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Sell Product</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>