<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';

$uid = $_SESSION['user_id'];

// Find or create the user's active cart
$cart_row = $conn->prepare("SELECT cart_id FROM CART WHERE user_id = ? AND status = 'active' LIMIT 1");
$cart_row->bind_param('i', $uid);
$cart_row->execute();
$cart = $cart_row->get_result()->fetch_assoc();

if (!$cart) {
    $nc = $conn->prepare("INSERT INTO CART (user_id, status) VALUES (?, 'active')");
    $nc->bind_param('i', $uid);
    $nc->execute();
    $cart_id = $conn->insert_id;
} else {
    $cart_id = $cart['cart_id'];
}

// Add item
if (isset($_GET['add'])) {
    $pid = (int)$_GET['add'];
    $price_row = $conn->prepare("SELECT price, stock_quantity FROM PRODUCT WHERE product_id = ? AND is_active = 1");
    $price_row->bind_param('i', $pid);
    $price_row->execute();
    $product = $price_row->get_result()->fetch_assoc();

    if ($product && $product['stock_quantity'] > 0) {
        // Increment if already in cart, otherwise insert
        $existing = $conn->prepare("SELECT cart_item_id, quantity FROM CART_ITEM WHERE cart_id = ? AND product_id = ?");
        $existing->bind_param('ii', $cart_id, $pid);
        $existing->execute();
        $item = $existing->get_result()->fetch_assoc();

        if ($item) {
            $new_qty = $item['quantity'] + 1;
            $upd = $conn->prepare("UPDATE CART_ITEM SET quantity = ? WHERE cart_item_id = ?");
            $upd->bind_param('ii', $new_qty, $item['cart_item_id']);
            $upd->execute();
        } else {
            $ins = $conn->prepare("INSERT INTO CART_ITEM (cart_id, product_id, quantity, price_at_time) VALUES (?, ?, 1, ?)");
            $ins->bind_param('iid', $cart_id, $pid, $product['price']);
            $ins->execute();
        }
    }
    header('Location: cart.php');
    exit;
}

// Remove item
if (isset($_GET['remove'])) {
    $stmt = $conn->prepare("DELETE FROM CART_ITEM WHERE cart_item_id = ? AND cart_id = ?");
    $stmt->bind_param('ii', $_GET['remove'], $cart_id);
    $stmt->execute();
    header('Location: cart.php');
    exit;
}

// Update quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty'], $_POST['item_id'])) {
    $new_qty = (int)$_POST['qty'];
    if ($new_qty < 1) {
        $stmt = $conn->prepare("DELETE FROM CART_ITEM WHERE cart_item_id = ? AND cart_id = ?");
        $stmt->bind_param('ii', $_POST['item_id'], $cart_id);
    } else {
        $stmt = $conn->prepare("UPDATE CART_ITEM SET quantity = ? WHERE cart_item_id = ? AND cart_id = ?");
        $stmt->bind_param('iii', $new_qty, $_POST['item_id'], $cart_id);
    }
    $stmt->execute();
    header('Location: cart.php');
    exit;
}

// Load cart items
$items = $conn->prepare(
    "SELECT ci.cart_item_id, ci.quantity, ci.price_at_time,
            p.name, p.stock_quantity
     FROM CART_ITEM ci
     JOIN PRODUCT p ON p.product_id = ci.product_id
     WHERE ci.cart_id = ?"
);
$items->bind_param('i', $cart_id);
$items->execute();
$rows = $items->get_result();

$total = 0;
$cart_items = [];
while ($r = $rows->fetch_assoc()) {
    $r['subtotal'] = $r['quantity'] * $r['price_at_time'];
    $total += $r['subtotal'];
    $cart_items[] = $r;
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 8px 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f0f0f0; }
        .total { font-size: 18px; font-weight: bold; margin: 16px 0; }
    </style>
</head>
<body>
<h1>My Cart</h1>
<nav>
    <a href="browse.php">&larr; Continue Shopping</a> |
    <a href="../user/dashboard.php">Dashboard</a> |
    <a href="../auth/logout.php">Logout</a>
</nav>

<?php if (empty($cart_items)): ?>
    <p>Your cart is empty. <a href="browse.php">Browse products</a></p>
<?php else: ?>
    <table>
        <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr>
        <?php foreach ($cart_items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>$<?php echo number_format($item['price_at_time'], 2); ?></td>
                <td>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="item_id" value="<?php echo (int)$item['cart_item_id']; ?>">
                        <input type="number" name="qty" value="<?php echo (int)$item['quantity']; ?>"
                               min="0" max="<?php echo (int)$item['stock_quantity']; ?>" style="width:55px">
                        <button type="submit">Update</button>
                    </form>
                </td>
                <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                <td><a href="cart.php?remove=<?php echo (int)$item['cart_item_id']; ?>">Remove</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p class="total">Total: $<?php echo number_format($total, 2); ?></p>
    <a href="checkout.php"><button>Proceed to Checkout</button></a>
<?php endif; ?>
</body>
</html>
