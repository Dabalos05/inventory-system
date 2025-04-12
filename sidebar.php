<nav id="sidebar">
    <ul>
        <li><a href="index.php" <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') echo 'class="active"'; ?>><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        <li><a href="suppliers.php" <?php if (basename($_SERVER['PHP_SELF']) == 'suppliers.php') echo 'class="active"'; ?>><i class="fas fa-truck"></i><span>Suppliers</span></a></li>
        <li><a href="products.php" <?php if (basename($_SERVER['PHP_SELF']) == 'products.php') echo 'class="active"'; ?>><i class="fas fa-box"></i><span>Products</span></a></li>
        <li><a href="transactions.php" <?php if (basename($_SERVER['PHP_SELF']) == 'transactions.php') echo 'class="active"'; ?>><i class="fas fa-exchange-alt"></i><span>Transactions</span></a></li>
        <li><a href="users.php" <?php if (basename($_SERVER['PHP_SELF']) == 'users.php') echo 'class="active"'; ?>><i class="fas fa-users"></i><span>Users</span></a></li>
        <li><a href="admin.php" <?php if (basename($_SERVER['PHP_SELF']) == 'admin.php') echo 'class="active"'; ?>><i class="fas fa-user-shield"></i><span>Administration</span></a></li>
        <li><a href="settings.php" <?php if (basename($_SERVER['PHP_SELF']) == 'settings.php') echo 'class="active"'; ?>><i class="fas fa-cog"></i><span>Settings</span></a></li>
        <li><a href="profile.php" <?php if (basename($_SERVER['PHP_SELF']) == 'profile.php') echo 'class="active"'; ?>><i class="fas fa-user"></i><span>Profile</span></a></li>
        <li><a href="logout.php" <?php if (basename($_SERVER['PHP_SELF']) == 'logout.php') echo 'class="active"'; ?>><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
    </ul>
</nav>