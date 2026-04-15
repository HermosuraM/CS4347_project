<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';

// Total users
$total_users = $conn->query("SELECT COUNT(*) AS c FROM USER")->fetch_assoc()['c'];

// Total orders & revenue
$order_stats = $conn->query(
    "SELECT COUNT(*) AS total_orders, COALESCE(SUM(total_amount), 0) AS revenue FROM ORDER_TABLE"
)->fetch_assoc();

// Pending orders
$pending = $conn->query(
    "SELECT COUNT(*) AS c FROM ORDER_TABLE WHERE status = 'pending'"
)->fetch_assoc()['c'];

// Top 5 products by units sold
$top_products = $conn->query(
    "SELECT p.name, SUM(oi.quantity) AS units_sold
     FROM ORDER_ITEM oi
     JOIN PRODUCT p ON p.product_id = oi.product_id
     GROUP BY oi.product_id
     ORDER BY units_sold DESC
     LIMIT 5"
);

// Low stock (under 5 units)
$low_stock = $conn->query(
    "SELECT name, stock_quantity FROM PRODUCT WHERE stock_quantity < 5 AND is_active = 1 ORDER BY stock_quantity ASC"
);

// Recent 10 orders
$recent_orders = $conn->query(
    "SELECT o.order_id, u.username, o.total_amount, o.status, o.order_date
     FROM ORDER_TABLE o
     JOIN USER u ON u.user_id = o.user_id
     ORDER BY o.order_date DESC
     LIMIT 10"
);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .cards { display: flex; gap: 20px; margin: 20px 0; }
        .card { background: white; padding: 20px; border-radius: 6px; min-width: 160px; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
        .card h3 { margin: 0 0 8px; font-size: 14px; color: #666; }
        .card p  { margin: 0; font-size: 28px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 8px 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f0f0f0; }
        .warn { color: #c0392b; font-weight: bold; }
        section { margin-bottom: 30px; }
    </style>
</head>
<body>
<h1>Analytics Dashboard</h1>
<nav><a href="panel.php">&larr; Back to Admin</a> | <a href="../auth/logout.php">Logout</a></nav>

<div class="cards">
    <div class="card">
        <h3>Total Users</h3>
        <p><?php echo (int)$total_users; ?></p>
    </div>
    <div class="card">
        <h3>Total Orders</h3>
        <p><?php echo (int)$order_stats['total_orders']; ?></p>
    </div>
    <div class="card">
        <h3>Revenue</h3>
        <p>$<?php echo number_format($order_stats['revenue'], 2); ?></p>
    </div>
    <div class="card">
        <h3>Pending Orders</h3>
        <p><?php echo (int)$pending; ?></p>
    </div>
</div>

<section>
    <h2>Top Products by Units Sold</h2>
    <table>
        <tr><th>Product</th><th>Units Sold</th></tr>
        <?php while ($row = $top_products->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo (int)$row['units_sold']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>

<section>
    <h2>Low Stock Alert</h2>
    <table>
        <tr><th>Product</th><th>Stock</th></tr>
        <?php while ($row = $low_stock->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td class="warn"><?php echo (int)$row['stock_quantity']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>

<section>
    <h2>Recent Orders</h2>
    <table>
        <tr><th>Order #</th><th>User</th><th>Amount</th><th>Status</th><th>Date</th></tr>
        <?php while ($row = $recent_orders->fetch_assoc()): ?>
            <tr>
                <td><?php echo (int)$row['order_id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['order_date']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</section>
</body>
</html>
