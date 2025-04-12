<?php
session_start();

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

// Database connection
$conn = new mysqli('localhost', 'root', '', 'inventory_rayzen');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch supplier data
    $sql = "SELECT * FROM suppliers WHERE idsupplier = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();

    if (!$supplier) {
        $message = "Supplier not found.";
    }

    $stmt->close();

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $supplier_name = $_POST['supplier_name'];
        $company_name = $_POST['company_name'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $zip_code = $_POST['zip_code'];

        // Check if any changes were made
        if (
            $supplier_name != $supplier['supplier_name'] ||
            $company_name != $supplier['Company Name'] ||
            $contact_number != $supplier['Contact Number'] ||
            $email != $supplier['Email'] ||
            $address != $supplier['Address'] ||
            $zip_code != $supplier['Zip code']
        ) {
            $sql = "UPDATE suppliers SET supplier_name = ?, `Company Name` = ?, `Contact Number` = ?, Email = ?, Address = ?, `Zip code` = ? WHERE idsupplier = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $supplier_name, $company_name, $contact_number, $email, $address, $zip_code, $id);

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header("Location: suppliers.php?message=Supplier updated successfully!");
                exit;
            } else {
                $message = "Error updating supplier: " . $stmt->error;
            }

            $stmt->close();
        } else {
            header("Location: suppliers.php");
            exit;
        }
    }
} else {
    $message = "Invalid request.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .button-container {
            margin-top: 15px;
        }

        .button-container input[type="submit"],
        .button-container a {
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }

        .button-container input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .button-container a {
            background-color: #f0f0f0;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Supplier</h2>

    <?php if ($message && strpos($message, 'successfully') === false): ?>
        <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if (isset($supplier)): ?>
        <form method="post">
            <div class="form-group">
                <label for="supplier_name">Supplier Name:</label>
                <input type="text" id="supplier_name" name="supplier_name" value="<?php echo htmlspecialchars($supplier['supplier_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($supplier['Company Name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($supplier['Contact Number']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label><input type="text" id="email" name="email" value="<?php echo htmlspecialchars($supplier['Email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($supplier['Address']); ?>" required>
            </div>
            <div class="form-group">
                <label for="zip_code">Zip Code:</label>
                <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($supplier['Zip code']); ?>" required>
            </div>
            <div class="button-container">
                <input type="submit" value="Update Supplier">
                <a href="suppliers.php">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>