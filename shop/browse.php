<?php
session_start();
include '../config/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $stmt = $conn->prepare(
        "SELECT p.*, pi.image_url
         FROM PRODUCT p
         LEFT JOIN PRODUCT_IMAGE pi ON pi.product_id = p.product_id
         WHERE p.is_active = 1 AND (p.name LIKE ? OR p.description LIKE ?)
         GROUP BY p.product_id ORDER BY p.name"
    );
    $like = '%' . $search . '%';
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $products = $conn->query(
        "SELECT p.*, pi.image_url
         FROM PRODUCT p
         LEFT JOIN PRODUCT_IMAGE pi ON pi.product_id = p.product_id
         WHERE p.is_active = 1
         GROUP BY p.product_id ORDER BY p.name"
    );
}

$logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .product-grid { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px; }
        .product-card { background: white; border: 1px solid #ddd; border-radius: 6px; padding: 16px; width: 200px; }
        .product-card img { width: 100%; height: 140px; object-fit: cover; border-radius: 4px; }
        .product-card h3 { font-size: 15px; margin: 10px 0 4px; }
        .product-card .price { font-weight: bold; color: #333; }
        .product-card .stock { font-size: 12px; color: #888; }
    </style>
</head>
<body>
<h1>Browse Products</h1>
<nav>
    <?php if ($logged_in): ?>
        <a href="../user/dashboard.php">Dashboard</a> |
        <a href="cart.php">My Cart</a> |
        <a href="../auth/logout.php">Logout</a>
    <?php else: ?>
        <a href="../index.php">Login</a> |
        <a href="../auth/register.php">Register</a>
    <?php endif; ?>
</nav>

<form method="GET" style="margin-top:16px">
    <input type="text" name="search" placeholder="Search products..."
           value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
    <?php if ($search): ?><a href="browse.php">Clear</a><?php endif; ?>
</form>

<div class="product-grid">
<?php while ($row = $products->fetch_assoc()): ?>
    <div class="product-card">
        <?php if ($row['image_url']): ?>
            <img src="<?php echo htmlspecialchars($row['image_url']); ?>"
                 alt="<?php echo htmlspecialchars($row['name']); ?>">
        <?php endif; ?>
        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
        <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
        <p class="stock">In stock: <?php echo (int)$row['stock_quantity']; ?></p>
        <a href="product_details.php?id=<?php echo (int)$row['product_id']; ?>">View Details</a><br>
        <?php if ($logged_in && $row['stock_quantity'] > 0): ?>
            <a href="cart.php?add=<?php echo (int)$row['product_id']; ?>">Add to Cart</a>
        <?php elseif (!$logged_in): ?>
            <a href="../index.php">Login to buy</a>
        <?php else: ?>
            <span style="color:#999">Out of stock</span>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
</div>
</body>
</html>
