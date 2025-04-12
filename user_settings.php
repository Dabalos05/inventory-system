<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT username, email, contact, profile_picture, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Assign default values if data is missing
$role = $user['role'] ?? 'staff';
$username = $user['username'] ?? 'Unknown';
$email = $user['email'] ?? 'No email';
$contact = $user['contact'] ?? 'No contact';
$profile_picture = !empty($user['profile_picture']) ? $user['profile_picture'] : 'zen.PNG';

// Function to check roles
function isUserAdmin($userRole) {
    return strtolower($userRole) === 'admin';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    .error-input { 
        border-color: red !important; 
        border-width: 3px !important; /* Add this line */
    }
    .custom-alert {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        text-align: center;
    }
    .custom-alert button {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .container.mt-4 .card {
        max-width: 500px; /* Adjust this value as needed */
        margin: 20px auto; /* Center the cards and add some margin */
    }
    /* Change focus color to green */
    .form-control:focus {
        border-color: #28a745; /* Green color */
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25); /* Green shadow */
    }
</style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Inventory System</a>
        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">User Settings</h2>
    <p class="text-center">Manage your personal details, update your password, and view your role & permissions.</p>

    <div class="card">
        <div class="card-header bg-primary text-white">Personal Information</div>
        <div class="card-body">
            <p><strong>Username:</strong> <?= htmlspecialchars($username); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email); ?></p>
            <p><strong>Contact:</strong> <?= htmlspecialchars($contact); ?></p>
            <a href="edit_info.php" class="btn btn-warning">Edit Info</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-success text-white">Change Password</div>
        <div class="card-body">
            <form id="updatePasswordForm">
                <div class="mb-3">
                    <label class="form-label">Current Password:</label>
                    <div class="input-group">
                        <input type="password" id="old_password" name="old_password" class="form-control password-input" required>
                        <span class="input-group-text password-toggle" data-target="#old_password">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password:</label>
                    <div class="input-group">
                        <input type="password" id="new_password" name="new_password" class="form-control password-input" required>
                        <span class="input-group-text password-toggle" data-target="#new_password">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password:</label>
                    <div class="input-group">
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control password-input" required>
                        <span class="input-group-text password-toggle" data-target="#confirm_password">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">Update Password</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-secondary text-white">User Role & Permissions</div>
        <div class="card-body">
            <p><strong>Role:</strong> <?= htmlspecialchars($role); ?></p>
            <p><strong>Permissions:</strong></p>
            <ul>
                <li>View Inventory</li>
                <?php if (isUserAdmin($role)) { ?>
                    <li>Manage Users</li>
                    <li>Add Products</li>
                    <li>Update Products</li>
                    <li>Delete Products</li>
                    <li>View Reports</li>
                    <li>Manage Settings</li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>

<div class="custom-alert" id="customAlert">
    <p id="alertMessage"></p>
    <button id="alertButton">OK</button>
</div>

<script>
$(document).ready(function () {
    $('#updatePasswordForm').on('submit', function (e) {
        e.preventDefault();

        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();

        if (newPassword !== confirmPassword) {
            $('#alertMessage').text("New password and confirm password do not match.");
            $('#customAlert').fadeIn();
            $('#confirm_password').addClass('error-input'); // Add red border
            return;
        } else {
            $('#confirm_password').removeClass('error-input'); // Remove red border if match
        }

        $.ajax({
            type: "POST",
            url: "update_info.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $('#alertMessage').text(response.message);
                    $('#customAlert').fadeIn();
                } else {
                    $('#alertMessage').text(response.message);
                    $('#customAlert').fadeIn();
                }
            },
            error: function (xhr) {
                $('#alertMessage').text("An error occurred: " + xhr.responseText);
                $('#customAlert').fadeIn();
            }
        });
    });

    $('#alertButton').click(function() {
        $('#customAlert').fadeOut();
        window.location.href = "user_settings.php"; // Redirect to user_settings.php
    });

    // Toggle password visibility
    $('.password-toggle').click(function() {
        const target = $(this).data('target');
        const input = $(target);
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    // Real-time password validation
    $('#confirm_password').on('input', function() {
        const newPassword = $('#new_password').val();
        const confirmPassword = $(this).val();

        if (newPassword !== confirmPassword) {
            $(this).addClass('error-input'); // Add red border
        } else {
            $(this).removeClass('error-input'); // Remove red border if match
        }
    });
});
</script>

</body>
</html>