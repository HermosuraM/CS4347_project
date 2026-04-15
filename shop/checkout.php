<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';

$uid = $_SESSION['user_id'];

// Load active cart
$cart_row = $conn->prepare("SELECT cart_id FROM CART WHERE user_id = ? AND status = 'active' LIMIT 1");
$cart_row->bind_param('i', $uid);
$cart_row->execute();
$cart = $cart_row->get_result()->fetch_assoc();

if (!$cart) {
    header('Location: cart.php');
    exit;
}
$cart_id = $cart['cart_id'];

// Load cart items
$items_stmt = $conn->prepare(
    "SELECT ci.cart_item_id, ci.quantity, ci.price_at_time, p.name
     FROM CART_ITEM ci
     JOIN PRODUCT p ON p.product_id = ci.product_id
     WHERE ci.cart_id = ?"
);
$items_stmt->bind_param('i', $cart_id);
$items_stmt->execute();
$rows = $items_stmt->get_result();

$total = 0;
$cart_items = [];
while ($r = $rows->fetch_assoc()) {
    $r['subtotal'] = $r['quantity'] * $r['price_at_time'];
    $total += $r['subtotal'];
    $cart_items[] = $r;
}

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Load user's addresses
$addr_stmt = $conn->prepare("SELECT * FROM ADDRESS WHERE user_id = ?");
$addr_stmt->bind_param('i', $uid);
$addr_stmt->execute();
$addresses = $addr_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; margin-bottom: 16px; }
        th, td { padding: 8px 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f0f0f0; }
        .total { font-size: 18px; font-weight: bold; margin: 8px 0 20px; }
        section { margin-bottom: 24px; }
        label { display: block; margin: 8px 0 4px; }
        select, input { padding: 6px; width: 300px; }
    </style>
</head>
<body>
<h1>Checkout</h1>
<nav>
    <a href="cart.php">&larr; Back to Cart</a>
</nav>

<section>
    <h2>Order Summary</h2>
    <table>
        <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
        <?php foreach ($cart_items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo (int)$item['quantity']; ?></td>
                <td>$<?php echo number_format($item['price_at_time'], 2); ?></td>
                <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p class="total">Total: $<?php echo number_format($total, 2); ?></p>
</section>

<form action="place_order.php" method="POST">

    <section>
        <h2>Shipping Address</h2>
        <?php if (!empty($addresses)): ?>
            <label>Select address:</label>
            <select name="address_id" required>
                <?php foreach ($addresses as $addr): ?>
                    <option value="<?php echo (int)$addr['address_id']; ?>">
                        <?php echo htmlspecialchars(
                            $addr['street'] . ', ' . $addr['city'] . ', ' .
                            $addr['state'] . ' ' . $addr['zip_code']
                        ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <p>No address on file. <a href="../user/profile.php">Add one in your profile</a> first.</p>
        <?php endif; ?>
    </section>

    <section>
        <h2>Payment Method</h2>
        <label>Select payment method:</label>
        <select name="payment_method" required>
            <option value="credit_card">Credit Card</option>
            <option value="debit_card">Debit Card</option>
            <option value="paypal">PayPal</option>
            <option value="wallet">Wallet</option>
        </select>
    </section>

    <?php if (!empty($addresses)): ?>
        <button type="submit">Place Order</button>
    <?php endif; ?>
</form>
</body>
</html>
