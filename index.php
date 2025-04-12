<?php
date_default_timezone_set('Asia/Manila');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

function logUserActivity($conn, $userId, $action) {
    $timestamp = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("INSERT INTO user_activity (user_id, action, timestamp) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $action, $timestamp);
    $stmt->execute();
    $stmt->close();
}

logUserActivity($conn, $_SESSION['user_id'], "User viewed Dashboard page.");

// Fetch product stats
$totalProducts = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'] ?? 0;

$lowStockProductsQuery = $conn->query("SELECT name, id, quantity FROM products WHERE quantity < 10 ORDER BY quantity ASC");
$lowStock = $lowStockProductsQuery->num_rows;

$outOfStock = $conn->query("SELECT COUNT(*) as `out` FROM products WHERE quantity = 0")->fetch_assoc()['out'] ?? 0;
$mostStocked = $conn->query("SELECT name, quantity FROM products ORDER BY quantity DESC LIMIT 1")->fetch_assoc() ?? ['name' => 'N/A', 'quantity' => 0];

// Fetch recent transactions
$transactionsQuery = $conn->query("
    SELECT t.*, p.name AS product_name 
    FROM transactions t 
    JOIN products p ON t.product_id = p.id 
    ORDER BY t.transaction_date DESC 
    LIMIT 5
");

// Fetch sales stats
$totalSales = $conn->query("SELECT SUM(sale_amount) as total FROM sales_records")->fetch_assoc()['total'] ?? 0;
$topProduct = $conn->query("SELECT p.name FROM sales_records s JOIN products p ON s.product_id = p.id GROUP BY s.product_id ORDER BY SUM(s.quantity_sold) DESC LIMIT 1")->fetch_assoc()['name'] ?? 'No sales recorded.';

// Fetch user activity
$userActivityQuery = $conn->query("
    SELECT u.username, ua.action, ua.timestamp 
    FROM user_activity ua 
    JOIN users u ON ua.user_id = u.id 
    ORDER BY ua.timestamp DESC 
    LIMIT 5
");

// Fetch Low Stock Count for Notifications
$lowStockCountQuery = $conn->query("SELECT COUNT(*) as count FROM products WHERE quantity < 10");
$lowStockCount = $lowStockCountQuery->fetch_assoc()['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    <style>
        .notification-count {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 12px;
            position: absolute;
            top: -10px;
            right: -10px;
        }
        .notification-icon {
            position: relative;
            display: inline-block;
        }
    </style>
</head>
<body>
    <nav id="sidebar">
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li class="profile-item">
                <a href="#" class="profile-toggle">
                    <i class="fas fa-user"></i><span>Profile</span> <span class="dropdown-arrow">▼</span>
                </a>
                <ul class="profile-dropdown" style="display: none;">
                    <li><a href="user_settings.php"><i class="fas fa-cog"></i><span>User Settings</span></a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
                </ul>
            </li>
            <li><a href="suppliers.php"><i class="fas fa-truck"></i><span>Suppliers</span></a></li>
            <li><a href="products.php"><i class="fas fa-box"></i><span>Products</span></a></li>
            <li><a href="users.php"><i class="fas fa-users"></i><span>Users</span></a></li>
        <!-- <li><a href="settings.php"><i class="fas fa-cog"></i><span>Settings</span></a></li> -->


            <li>
                <a href="notification.html">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php if ($lowStockCount > 0): ?>
                            <span class="notification-count"><?php echo $lowStockCount; ?></span>
                        <?php endif; ?>
                    </div>
                    <span>Notification</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="main-content">
        <h1>Welcome to Rayzen Inventory System</h1>
        <div class="dashboard">
            <div class="card-container">
                <div class="card blue"><h3>Total Products</h3><p><?php echo $totalProducts; ?></p></div>
            </div>
            <div class="card-container">
                <div class="card yellow"><h3>Low Stock</h3><p><?php echo $lowStock; ?></p></div>
            </div>
            <div class="card-container">
    <div class="card red"><h3>Out of Stock</h3><p><?php echo $outOfStock; ?></p></div>
</div>
            <div class="card-container">
                <div class="card green"><h3>Most Stocked Product</h3><p><?php echo $mostStocked['name'] . ' (' . $mostStocked['quantity'] . ')'; ?></p></div>
            </div>
        </div>


        <div class="container mt-4">
            <div class="card">
                <div class="card-body">
                    <h2>Low Stock Products</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $lowStockProductsQuery->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['quantity']; ?></td>
                                <td><a href='edit_product.php?id=<?php echo $product['id']; ?>' class='btn btn-sm btn-primary'>Restock</a></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="index.js"></script>
<script>
$(document).ready(function () {
    // Profile Dropdown Toggle
    $(".profile-toggle").on("click", function (e) {
        e.preventDefault();
        let dropdown = $(this).closest(".profile-item").find(".profile-dropdown");

        // Toggle dropdown visibility
        dropdown.slideToggle(400, function() { // Added animation and callback
            console.log("Dropdown toggled. Visible:", dropdown.is(":visible"));
        });

        // Debugging: Log the click event and target
        console.log("Profile toggle clicked:", e.target);
    });

    // Debugging: Log any clicks outside the profile item
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".profile-item").length) {
            console.log("Click outside profile item:", e.target);
            $(".profile-dropdown").slideUp(400, function() {
                console.log("Dropdown closed due to click outside.");
            });
        }
    });

    // Debugging: Prevent clicks inside the dropdown from closing it
    $(".profile-dropdown").on("click", function(e) {
        e.stopPropagation();
        console.log("Click inside dropdown, event stopped.");
    });

    // 🔔 Function to Load Notifications
    function loadNotifications() {
        $.ajax({
            url: "fetch_notifications.php",
            method: "GET",
            dataType: "json",
            success: function (data) {
                let notificationContainer = $("#notification-container");
                notificationContainer.empty();

                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        notificationContainer.append(`<li>${notification}</li>`);
                    });

                    // Play notification sound
                    let audio = new Audio('/inventory_system/sounds/notification_sound.mp3');
                    audio.play().catch(error => console.log("Autoplay Blocked:", error));
                } else {
                    notificationContainer.append("<li>No new notifications</li>");
                }
            },
            error: function (xhr, status, error) {
                console.log("Error fetching notifications:", error);
            }
        });
    }

 

</body>
</html>