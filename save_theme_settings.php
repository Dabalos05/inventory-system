<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = $_POST['theme'];

    $stmt = $conn->prepare("UPDATE settings SET theme = ? WHERE user_id = ?");
    $stmt->bind_param("si", $theme, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    header("Location: settings.php?success=theme");
    exit();
} else {
    header("Location: settings.php");
    exit();
}
?>