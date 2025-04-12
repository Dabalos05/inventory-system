<?php
session_start();
include 'db_connect.php';

// Initialize $result to avoid undefined variable warning
$result = $conn->query("SELECT * FROM products");

// Function to generate table rows
function generateTableRows($result) {
    $rows = '';
    while ($row = $result->fetch_assoc()) {
        $rows .= "<tr>";
        $rows .= "<td>" . $row['id'] . "</td>";
        $rows .= "<td>" . $row['name'] . "</td>";
        $rows .= "<td>" . $row['price'] . "</td>";
        $rows .= "<td>" . $row['quantity'] . "</td>";
        $rows .= "<td>";
        $rows .= "<a href='edit_product.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Edit</a> ";
        $rows .= "<a href='sell_product.php?id=" . $row['id'] . "' class='btn btn-success btn-sm'>Sell</a> ";
        $rows .= "<a href='delete_product.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Delete</a>";
        $rows .= "</td>";
        $rows .= "</tr>";
    }
    return $rows;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table td,
        .table th {
            text-align: center;
            vertical-align: middle;
        }

        .table {
            width: 90%;
            margin: 0 auto;
        }

        .search-bar-container {
    width: 30%;
    margin: 0;
    padding: 0;
    margin-left: 50px;
}

#searchInput {
    width: 100%;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="row mt-4 mb-4">
            <div class="col-md-auto">
                <a href="index.php" class="btn btn-secondary no-print">← Back</a>
            </div>
            <div class="col-md"></div>
            <div class="col-md-auto">
                <div class="d-flex flex-column align-items-end">
                    <button onclick="printAndReturn();" class="btn btn-primary no-print mb-2">Print Inventory</button>
                    <a href="add_product.php" class="btn btn-primary no-print">Add Product</a>
                </div>
            </div>
        </div>

        <!-- ✅ Success Message Above Search Bar -->
        <?php
        if (isset($_SESSION['message'])) {
            $message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
            echo "<div class='row'>
                    <div class='col-md-12'>
                        <div class='alert alert-$message_type text-center' id='session-message' style='width: 50%; margin: 10px auto;'>
                            {$_SESSION['message']}
                        </div>
                    </div>
                  </div>";
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="search-bar-container">
            <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
            <div id="searchResults" class="list-group"></div>
        </div>
    </div>
</div>


        <div class="row mt-3">
            <div class="col-md-12">
                <h2 style="width: 90%; margin: 0 auto;">Products</h2>
            </div>
        </div>

        <table class="table" style="width: 90%; margin: 0 auto;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                <?php
                if (isset($_GET['search'])) {
                    $search = $conn->real_escape_string($_GET['search']);
                    $searchResult = $conn->query("SELECT * FROM products WHERE name LIKE '%$search%'");
                    echo generateTableRows($searchResult);
                } else {
                    echo generateTableRows($result);
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="message-container"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let sessionMessage = document.getElementById('session-message');
            if (sessionMessage) {
                setTimeout(function() {
                    sessionMessage.style.display = 'none';
                }, 3000);
            }

            const searchInput = document.getElementById('searchInput');
            const productTableBody = document.getElementById('productTableBody');

            searchInput.addEventListener('input', function() {
                const query = this.value;
                if (query.length >= 3) {
                    fetch('products.php?search=' + encodeURIComponent(query))
                        .then(response => response.text())
                        .then(data => {
                            let tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data;
                            productTableBody.innerHTML = tempDiv.querySelector('#productTableBody').innerHTML;
                        });
                } else {
                    fetch('products.php')
                        .then(response => response.text())
                        .then(data => {
                            let tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data;
                            productTableBody.innerHTML = tempDiv.querySelector('#productTableBody').innerHTML;
                        });
                }
            });
        });

        function printAndReturn() {
            var printWindow = window.open('print_inventory.php', '_blank');
            printWindow.onload = function() {
                printWindow.print();
                printWindow.onafterprint = function() {
                    printWindow.close();
                    window.location.href = 'products.php';
                };
            };
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
