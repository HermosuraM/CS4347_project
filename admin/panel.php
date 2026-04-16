<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

include '../config/db.php';

$search_user    = isset($_GET['search_user'])    ? trim($_GET['search_user'])    : '';
$search_product = isset($_GET['search_product']) ? trim($_GET['search_product']) : '';

// Users query
if ($search_user !== '') {
    $su = $conn->prepare(
        "SELECT user_id, username, email, status FROM USER
         WHERE username LIKE ? OR email LIKE ? ORDER BY username"
    );
    $like = '%' . $search_user . '%';
    $su->bind_param('ss', $like, $like);
    $su->execute();
    $users = $su->get_result();
} else {
    $users = $conn->query("SELECT user_id, username, email, status FROM USER ORDER BY username");
}

// Products query
if ($search_product !== '') {
    $sp = $conn->prepare(
        "SELECT * FROM PRODUCT WHERE name LIKE ? OR description LIKE ? ORDER BY name"
    );
    $like = '%' . $search_product . '%';
    $sp->bind_param('ss', $like, $like);
    $sp->execute();
    $products = $sp->get_result();
} else {
    $products = $conn->query("SELECT * FROM PRODUCT ORDER BY name");
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; margin-bottom: 20px; }
        th, td { padding: 8px 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f0f0f0; }
        .suspended { color: #c0392b; }
        section { margin-bottom: 40px; }
        input[type=text], input[type=number] { padding: 4px 8px; margin-right: 4px; }
    </style>
</head>
<body>
<h1>Admin Panel</h1>
<nav>
    <a href="analytics.php">Analytics</a> |
    <a href="../auth/logout.php">Logout</a>
</nav>

<!-- ====== USER MANAGEMENT ====== -->
<section>
    <h2>Manage Users</h2>
    <form method="GET">
        <input type="hidden" name="search_product" value="<?php echo htmlspecialchars($search_product); ?>">
        <input type="text" name="search_user" placeholder="Search by username or email"
               value="<?php echo htmlspecialchars($search_user); ?>">
        <button type="submit">Search</button>
        <?php if ($search_user): ?>
            <a href="panel.php">Clear</a>
        <?php endif; ?>
    </form>
    <table>
        <tr>
            <th>ID</th><th>Username</th><th>Email</th><th>Status</th><th>Actions</th>
        </tr>
        <?php while ($u = $users->fetch_assoc()): ?>
            <tr>
                <td><?php echo (int)$u['user_id']; ?></td>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td class="<?php echo $u['status'] !== 'active' ? 'suspended' : ''; ?>">
                    <?php echo htmlspecialchars($u['status']); ?>
                </td>
                <td>
                    <a href="toggle_user.php?id=<?php echo (int)$u['user_id']; ?>&status=<?php echo $u['status']; ?>">
                        <?php echo $u['status'] === 'active' ? 'Suspend' : 'Activate'; ?>
                    </a>
                    &nbsp;|&nbsp;
                    <form action="delete_user.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo (int)$u['user_id']; ?>">
                        <button onclick="return confirm('Delete this user permanently?')">Delete</button>
                    </form>
                        onclick="return confirm('Delete this user permanently?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>

<!-- ====== PRODUCT MANAGEMENT ====== -->
<section>
    <h2>Manage Products</h2>

    <h3>Add Product</h3>
    <form action="add_product.php" method="POST">
        <input name="name" placeholder="Name" required>
        <input name="price" type="number" step="0.01" placeholder="Price" required>
        <input name="quantity" type="number" placeholder="Qty" required>
        <input name="description" placeholder="Description" required>
        <button>Add</button>
    </form>

    <h3>Search Products</h3>
    <form method="GET">
        <input type="hidden" name="search_user" value="<?php echo htmlspecialchars($search_user); ?>">
        <input type="text" name="search_product" placeholder="Search by name or description"
               value="<?php echo htmlspecialchars($search_product); ?>">
        <button type="submit">Search</button>
        <?php if ($search_product): ?>
            <a href="panel.php">Clear</a>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Description</th><th>Actions</th>
        </tr>
        <?php while ($row = $products->fetch_assoc()): ?>
            <tr>
                <td><?php echo (int)$row['product_id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td>$<?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo (int)$row['stock_quantity']; ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                    <a href="edit_product.php?id=<?php echo (int)$row['product_id']; ?>">Edit</a>
                    &nbsp;|&nbsp;
                    <form action="delete_product.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo (int)$row['product_id']; ?>">
                        <button onclick="return confirm('Delete this product?')">Delete</button>
                    </form>
                        onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>
</body>
</html>
