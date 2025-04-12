<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to index.php
    exit();
}
require 'config.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $role = $_POST['role']; // Kukunin ang role mula sa dropdown

    // Check if username already exists
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        $error = "Username already exists.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Ipasok ang role sa database
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password_hash, $email, $role);

        if ($stmt->execute()) {
            $success = "User registered successfully!";
        } else {
            $error = "Failed to register user.";
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="login_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="background-image"></div>
    <div class="form-container">
        <div class="title">
            <h1>Rayzen Office</h1>
            <h2>Inventory System</h2>
        </div>
        <div class="login-form">
            <h2>Sign Up</h2>
            <?php if (!empty($error)) { ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php } ?>
            <?php if (!empty($success)) { ?>
                <p class="success-message" id="successMessage"><?php echo $success; ?></p>
            <?php } ?>
            <form method="POST">
                <div class="input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-container">
                    <div class="input-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <select name="role" required>
    <option value="">Select Role</option>
    <option value="admin">Admin</option>
    <option value="staff">Staff</option>
</select>
                </div>
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Sign in</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 3000); // 3000 milliseconds = 3 seconds
            }
        });
    </script>
</body>
</html>