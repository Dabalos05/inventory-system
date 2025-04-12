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

include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $id");
    $user = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $email, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px auto;
            max-width: 100%;
        }

        .header-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-container {
            width: 50%;
            margin: 30px auto;
        }

        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="header-container">
            <a href="users.php" class="btn btn-secondary">← Back</a>
            <h2 style="margin-left: 20px;">Edit User</h2>
        </div>

        <div class="form-container">
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo $user['username']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="users.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>