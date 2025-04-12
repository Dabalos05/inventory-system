<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user info from database
$stmt = $conn->prepare("SELECT username, email, contact FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$username = $user['username'] ?? '';
$email = $user['email'] ?? '';
$contact = $user['contact'] ?? '';

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Personal Info</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
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
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Edit Personal Information</h4>
        </div>
        <div class="card-body">
            <form id="updateForm">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($contact) ?>">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="user_settings.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="custom-alert" id="customAlert">
    <p id="alertMessage"></p>
    <button id="alertButton">OK</button>
</div>

<script>
$(document).ready(function() {
    $("#updateForm").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "update_info.php",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $('#alertMessage').text(response.message);
                    $('#customAlert').fadeIn();
                } else {
                    $('#alertMessage').text(response.message);
                    $('#customAlert').fadeIn();
                }
            },
            error: function(xhr) {
                $('#alertMessage').text("An error occurred: " + xhr.responseText);
                $('#customAlert').fadeIn();
            }
        });
    });

    $('#alertButton').click(function() {
        $('#customAlert').fadeOut();
        window.location.href = "user_settings.php"; // Redirect to user_settings.php
    });
});
</script>

</body>
</html>