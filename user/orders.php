<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';

$uid = $_SESSION['user_id'];

// Load all orders for this user
$orders_stmt = $conn->prepare(
    "SELECT o.order_id, o.order_date, o.status, o.total_amount,
            p.payment_method, p.payment_status
     FROM ORDER_TABLE o
     LEFT JOIN PAYMENT p ON p.order_id = o.order_id
     WHERE o.user_id = ?
     ORDER BY o.order_date DESC"
);
$orders_stmt->bind_param('i', $uid);
$orders_stmt->execute();
$orders = $orders_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Load items for each order
$items_stmt = $conn->prepare(
    "SELECT oi.quantity, oi.price_at_purchase, p.name
     FROM ORDER_ITEM oi
     JOIN PRODUCT p ON p.product_id = oi.product_id
     WHERE oi.order_id = ?"
);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .order-card { background: white; border: 1px solid #ddd; border-radius: 6px;
                      padding: 16px; margin-bottom: 20px; }
        .order-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .order-header span { font-size: 13px; color: #666; }
        .status-completed { color: green; font-weight: bold; }
        .status-pending   { color: orange; font-weight: bold; }
        .status-canceled  { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 6px 10px; border-bottom: 1px solid #eee; text-align: left; font-size: 14px; }
        th { background: #f8f8f8; }
    </style>
</head>
<body>
<h1>My Orders</h1>
<nav>
    <a href="dashboard.php">&larr; Dashboard</a> |
    <a href="../shop/browse.php">Browse Products</a> |
    <a href="../auth/logout.php">Logout</a>
</nav>

<?php if (empty($orders)): ?>
    <p>You haven't placed any orders yet. <a href="../shop/browse.php">Start shopping</a></p>
<?php else: ?>
    <?php foreach ($orders as $order):
        $items_stmt->bind_param('i', $order['order_id']);
        $items_stmt->execute();
        $items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $status_class = 'status-' . $order['status'];
    ?>
        <div class="order-card">
            <div class="order-header">
                <strong>Order #<?php echo (int)$order['order_id']; ?></strong>
                <span><?php echo htmlspecialchars($order['order_date']); ?></span>
            </div>
            <p>
                Status: <span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                &nbsp;&nbsp;
                Payment: <?php echo htmlspecialchars($order['payment_method'] ?? '—'); ?>
                (<?php echo htmlspecialchars($order['payment_status'] ?? '—'); ?>)
            </p>
            <table>
                <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo (int)$item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price_at_purchase'], 2); ?></td>
                        <td>$<?php echo number_format($item['quantity'] * $item['price_at_purchase'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p style="text-align:right; font-weight:bold; margin-top:8px">
                Total: $<?php echo number_format($order['total_amount'], 2); ?>
            </p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
