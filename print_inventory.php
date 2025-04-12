<?php
include 'db_connect.php';

// Corrected query: Order by ID to match products.php
$productsQuery = $conn->query("SELECT * FROM products ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Inventory</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }
        @media print {
            .no-print { display: none; }
            @page {
                margin: 10mm; /* Adjust margins as needed */
                size: auto;   /* auto is the initial value */
            }
            @page :left {
                @bottom-center {
                    content: counter(page); /* Display page number */
                }
                @top-left {
                    content: ""; /* Clear top-left on left pages */
                }
                @top-center {
                    content: ""; /* Clear top-center on left pages */
                }
            }
            @page :right {
                @bottom-center {
                    content: counter(page); /* Display page number */
                }
                @top-left {
                    content: ""; /* Clear top-left on right pages */
                }
                @top-center {
                    content: ""; /* Clear top-center on right pages */
                }
            }
            @page :first {
                @bottom-center {
                    content: counter(page); /* Display page number on the first page too */
                }
                @top-left {
                    content: ""; /* Clear top-left on first page */
                }
                @top-center {
                    content: ""; /* Clear top-center on first page */
                }
            }
        }
        .company-info h1, .company-info p, #printDate {
            font-family: "Algerian", sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mt-4 mb-4 no-print">
            <div class="col-md-auto">
                <a href="products.php" class="btn btn-secondary">← Back</a>
            </div>
        </div>

        <div class="company-info text-center mt-4">
            <h1>Rayzen Office and School Supplies</h1>
            <p>Bitalag, Tagudin, Ilocos Sur</p>
            <p>Call: 0946-526-0820 | Email: rayzen@gmail.com</p>
            <p id="printDate"></p>
        </div>

        <table class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $productsQuery->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        var currentDate = new Date();
        var options = { year: 'numeric', month: 'long', day: 'numeric' };
        var formattedDate = currentDate.toLocaleDateString('en-US', options);
        document.getElementById('printDate').textContent = "Date: " + formattedDate;

        window.onload = function() {
            window.print();
            setTimeout(function(){ window.close(); }, 100); // Optional: Close the window after printing
        };
    </script>
</body>
</html>