<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user is an admin
if ($_SESSION['role'] !== "admin") {
    // If not an admin, output the HTML directly
    echo '<div id="permissionAlert" class="permission-alert">
            <div class="alert-content">
                <p>You do not have permission to delete products.</p>
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

// ... (Rest of your delete_product.php code for handling deletion) ...

// Database connection
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the product
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product deleted successfully.";
        $_SESSION['message_type'] = "success";
        header("Location: products.php");
        exit();
    } else {
        $_SESSION['message'] = "Error deleting product: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
        header("Location: products.php");
        exit();
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "warning";
    header("Location: products.php");
    exit();
}

$conn->close();
?>