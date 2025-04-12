<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
$result = $conn->query("SELECT id, username, email FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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

        table {
            width: 99%;
            border-collapse: collapse;
            margin: 30px auto;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        th:nth-child(1) { width: 5%; }
        th:nth-child(2) { width: 25%; }
        th:nth-child(3) { width: 30%; }
        th:nth-child(4) { width: 10%; }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .action-links {
            white-space: nowrap;
        }

        .action-links a {
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            margin-right: 5px;
        }

        .action-links a:nth-child(1) {
            background-color: rgb(0, 255, 13);
            color: black;
        }

        .action-links a:nth-child(2) {
            background-color: red;
            color: white;
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

        .back-link, .add-supplier-btn {
            display: inline-block;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 80px; /* Added margin-top */
        }

        .back-link {
            background-color: #f0f0f0;
            color: #333;
            transform: translateX(30%);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="header-container">
            <a href="index.php" class="back-link">← Back</a>
            <h2>User Management</h2>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td class='action-links'>";
                        echo "<a href='edit_user.php?id=" . $row['id'] . "'>Edit</a>";
                        echo "<a href='delete_user.php?id=" . $row['id'] . "'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No users found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>