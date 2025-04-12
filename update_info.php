<?php
session_start();
require 'config.php';

// Set response type to JSON
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => "Unauthorized request."]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ✅ 1. UPDATE PASSWORD LOGIC
    if (!empty($_POST['old_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => "New passwords do not match."]);
            exit();
        }

        // Fetch current password hash from database
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => "Database error: " . $conn->error]);
            exit();
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($stored_password_hash);
        $stmt->fetch();
        $stmt->close();

        // Verify old password
        if (!password_verify($old_password, $stored_password_hash)) {
            echo json_encode(['success' => false, 'message' => "Incorrect current password."]);
            exit();
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => "Database error: " . $conn->error]);
            exit();
        }
        $stmt->bind_param("si", $hashed_password, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        } else {
            error_log(date('Y-m-d H:i:s') . " - Password update error: " . $stmt->error . "\n", 3, "error.log");
            echo json_encode(['success' => false, 'message' => "Error updating password: " . $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    // ✅ 2. UPDATE USER INFO LOGIC
    if (!empty($_POST['username']) && !empty($_POST['email']) && isset($_POST['contact'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $contact = trim($_POST['contact']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => "Invalid email format."]);
            exit();
        }

        // Update user information
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, contact = ? WHERE id = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => "Database error: " . $conn->error]);
            exit();
        }
        $stmt->bind_param("sssi", $username, $email, $contact, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User info updated successfully.']);
        } else {
            error_log(date('Y-m-d H:i:s') . " - User info update error: " . $stmt->error . "\n", 3, "error.log");
            echo json_encode(['success' => false, 'message' => "Error updating user info: " . $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    echo json_encode(['success' => false, 'message' => "No update parameters provided."]);
    exit();
}

echo json_encode(['success' => false, 'message' => "Invalid request."]);
exit();
?>
