<?php
session_start();
include '../config/db.php';

if (!isset($_GET['id'])) {
    header('Location: browse.php');
    exit;
}

$stmt = $conn->prepare(
    "SELECT p.*, ANY_VALUE(pi.image_url) AS image_url, ANY_VALUE(pi.alt_text) AS alt_text,
            GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') AS categories
     FROM PRODUCT p
     LEFT JOIN PRODUCT_IMAGE pi ON pi.product_id = p.product_id
     LEFT JOIN PRODUCT_CATEGORY pc ON pc.product_id = p.product_id
     LEFT JOIN CATEGORY c ON c.category_id = pc.category_id
     WHERE p.product_id = ? AND p.is_active = 1
     GROUP BY p.product_id"
);
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header('Location: browse.php');
    exit;
}

$logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .detail-wrap { display: flex; gap: 40px; margin-top: 20px; }
        .detail-wrap img { width: 300px; height: 300px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
        .detail-info h2 { margin-top: 0; }
        .price { font-size: 24px; font-weight: bold; margin: 12px 0; }
        .stock { color: #888; margin-bottom: 16px; }
        .category { font-size: 13px; color: #666; margin-bottom: 12px; }
    </style>
</head>
<body>
<nav>
    <a href="browse.php">&larr; Back to Products</a>
    <?php if ($logged_in): ?>
        | <a href="cart.php">My Cart</a>
        | <a href="../auth/logout.php">Logout</a>
    <?php else: ?>
        | <a href="../index.php">Login</a>
    <?php endif; ?>
</nav>

<div class="detail-wrap">
    <?php if ($product['image_url']): ?>
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
             alt="<?php echo htmlspecialchars($product['alt_text'] ?? $product['name']); ?>">
    <?php endif; ?>

    <div class="detail-info">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>

        <?php if ($product['categories']): ?>
            <p class="category">Category: <?php echo htmlspecialchars($product['categories']); ?></p>
        <?php endif; ?>

        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
        <p class="stock">
            <?php if ($product['stock_quantity'] > 0): ?>
                In stock: <?php echo (int)$product['stock_quantity']; ?> available
            <?php else: ?>
                Out of stock
            <?php endif; ?>
        </p>

        <p><?php echo htmlspecialchars($product['description']); ?></p>

        <?php if ($logged_in && $product['stock_quantity'] > 0): ?>
            <a href="cart.php?add=<?php echo (int)$product['product_id']; ?>">
                <button>Add to Cart</button>
            </a>
        <?php elseif (!$logged_in): ?>
            <a href="../index.php"><button>Login to Buy</button></a>
        <?php else: ?>
            <button disabled>Out of Stock</button>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
