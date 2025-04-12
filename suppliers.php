<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

// Fetch suppliers data
$result = $conn->query("SELECT idsupplier, supplier_name, `Company Name`, `Contact Number`, Email, Address, `Zip code` FROM suppliers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers Management</title>
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
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        .action-links {
            white-space: nowrap;
        }

        .action-links a {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px;
            font-size: 0.9rem;
        }

        .action-links a:nth-child(1) {
            background-color: #4CAF50;
            color: white;
        }

        .action-links a:nth-child(2) {
            background-color: #f44336;
            color: white;
        }

        .back-link {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            background-color: #f0f0f0;
            color: #333;
            margin-bottom: 20px;
        }

        h2 {
            margin-left: 20px;
        }

        .add-supplier-btn {
    display: inline-block;
    padding: 10px 15px;
    background-color: #007bff; /* Blue */
    color: white;
    text-decoration: none; /* Remove text decoration */
    border-radius: 5px;
    margin-bottom: 20px;
    float: right; /* Move to the right */
}
    </style>
</head>
<body>
    <div class="container mt-5">
        <a href="index.php" class="back-link">← Back</a>
        <h2>Suppliers Management</h2>

        <a href="add_supplier.php" class="add-supplier-btn">Add Supplier</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier Name</th>
                    <th>Company Name</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Zip Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['idsupplier'] . "</td>";
                        echo "<td>" . $row['supplier_name'] . "</td>";
                        echo "<td>" . $row['Company Name'] . "</td>";
                        echo "<td>" . $row['Contact Number'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        echo "<td>" . $row['Address'] . "</td>";
                        echo "<td>" . $row['Zip code'] . "</td>";
                        echo "<td class='action-links'>";
                        echo "<a href='edit_supplier.php?id=" . $row['idsupplier'] . "'>Edit</a>";
                        echo "<a href='delete_supplier.php?id=" . $row['idsupplier'] . "'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No suppliers found.</td></tr>";
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