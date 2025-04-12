<?php
session_start();
require 'config.php';

if (!isset($_GET['id'])) {
    die("Product ID not specified.");
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);

    $sql = "UPDATE products SET name = '$name', quantity = $quantity, price = $price, category = '$category' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: products.php?msg=updated");
        exit;
    } else {
        die("Error updating product: " . $conn->error);
    }
}

$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <a href="products.php" class="btn btn-secondary mb-3">← Back to Products</a>
        <h2 class="text-center mb-4">Edit Product</h2>
        <form action="" method="POST" class="border p-4 shadow-sm bg-light">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" class="form-control" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select name="category" class="form-control">
                    <option value="Electronics" <?php if ($product['category'] === 'Electronics') echo 'selected'; ?>>Electronics</option>
                    <option value="Clothing" <?php if ($product['category'] === 'Clothing') echo 'selected'; ?>>Clothing</option>
                    <option value="Books" <?php if ($product['category'] === 'Books') echo 'selected'; ?>>Books</option>
                    <option value="Other" <?php if ($product['category'] === 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
</body>
</html>