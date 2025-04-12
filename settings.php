<?php
    session_start();
    require 'config.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Fetch current theme preference
    $stmt = $conn->prepare("SELECT theme FROM settings WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($currentTheme);
    $stmt->fetch();
    $stmt->close();

    if (!$currentTheme) {
        $currentTheme = 'light'; // Default to light if not found
    }
    ?>

    <!DOCTYPE html>
    <html lang="en" data-theme="<?php echo $currentTheme; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Settings - Theme</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="themes.css">
    </head>
    <body>

        <nav class="navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Inventory System</a>
                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </nav>

        <div class="settings-container" style="display:flex">
           

            <div class="settings-content">
                <div id="theme" class="settings-form active">
                    <h2>Theme Settings</h2>
                    <form action="save_theme_settings.php" method="POST">
                    <div class="mb-3">
    <label class="form-check-label" for="themeToggle">Dark Mode:</label>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="themeToggle" <?php echo ($currentTheme === 'dark') ? 'checked' : ''; ?>>
    </div>
</div>
                        <button type="submit" class="btn btn-primary">Save Theme Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;

        themeToggle.addEventListener('change', function() {
            if (this.checked) {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            } else {
                body.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            }
        });

        // Load theme from local storage (if available)
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.setAttribute('data-theme', savedTheme);
            if (savedTheme === 'dark') {
                themeToggle.checked = true;
            }
        }
    });
</script>

    </body>
    </html>