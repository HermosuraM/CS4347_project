<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}
include '../config/db.php';

$uid            = $_SESSION['user_id'];
$address_id     = (int)$_POST['address_id'];
$payment_method = $_POST['payment_method'];

$allowed_methods = ['credit_card', 'debit_card', 'paypal', 'wallet'];
if (!in_array($payment_method, $allowed_methods)) {
    header('Location: checkout.php');
    exit;
}

// Get active cart
$cart_stmt = $conn->prepare("SELECT cart_id FROM CART WHERE user_id = ? AND status = 'active' LIMIT 1");
$cart_stmt->bind_param('i', $uid);
$cart_stmt->execute();
$cart = $cart_stmt->get_result()->fetch_assoc();

if (!$cart) {
    header('Location: cart.php');
    exit;
}
$cart_id = $cart['cart_id'];

// Load cart items
$items_stmt = $conn->prepare(
    "SELECT ci.product_id, ci.quantity, ci.price_at_time
     FROM CART_ITEM ci WHERE ci.cart_id = ?"
);
$items_stmt->bind_param('i', $cart_id);
$items_stmt->execute();
$cart_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$total = array_sum(array_map(fn($i) => $i['quantity'] * $i['price_at_time'], $cart_items));

// All DB writes in a transaction
$conn->begin_transaction();
try {
    // Create order
    $order_stmt = $conn->prepare(
        "INSERT INTO ORDER_TABLE (user_id, status, total_amount) VALUES (?, 'pending', ?)"
    );
    $order_stmt->bind_param('id', $uid, $total);
    $order_stmt->execute();
    $order_id = $conn->insert_id;

    // Insert order items and decrement stock
    $oi_stmt  = $conn->prepare(
        "INSERT INTO ORDER_ITEM (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)"
    );
    $stk_stmt = $conn->prepare(
        "UPDATE PRODUCT SET stock_quantity = stock_quantity - ? WHERE product_id = ?"
    );
    foreach ($cart_items as $item) {
        $oi_stmt->bind_param('iiid', $order_id, $item['product_id'], $item['quantity'], $item['price_at_time']);
        $oi_stmt->execute();
        $stk_stmt->bind_param('ii', $item['quantity'], $item['product_id']);
        $stk_stmt->execute();
    }

    // Record payment
    $pay_stmt = $conn->prepare(
        "INSERT INTO PAYMENT (order_id, payment_method, amount, payment_status) VALUES (?, ?, ?, 'pending')"
    );
    $pay_stmt->bind_param('isd', $order_id, $payment_method, $total);
    $pay_stmt->execute();

    // Mark cart as converted
    $conv_stmt = $conn->prepare("UPDATE CART SET status = 'converted' WHERE cart_id = ?");
    $conv_stmt->bind_param('i', $cart_id);
    $conv_stmt->execute();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die('Order failed. Please try again.');
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/style.css"></head>
<body>
<h1>Order Placed!</h1>
<p>Thank you for your order. Your order #<?php echo $order_id; ?> has been received.</p>
<p>Total charged: $<?php echo number_format($total, 2); ?></p>
<nav>
    <a href="../user/orders.php">View My Orders</a> |
    <a href="browse.php">Continue Shopping</a> |
    <a href="../user/dashboard.php">Dashboard</a>
</nav>
</body>
</html>
