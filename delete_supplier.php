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
                  <p>You do not have permission to delete suppliers.</p>
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

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM suppliers WHERE idsupplier = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo "<script>alert('Supplier deleted successfully!'); window.location.href='suppliers.php';</script>";
        exit;
    } else {
        $stmt->close();
        $conn->close();
        echo "<script>alert('Error deleting supplier: " . $conn->error . "'); window.location.href='suppliers.php';</script>";
        exit;
    }
} else {
    $conn->close();
    echo "<script>alert('Invalid request.'); window.location.href='suppliers.php';</script>";
    exit;
}
?>