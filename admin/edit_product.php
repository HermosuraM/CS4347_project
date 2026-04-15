<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';

$stmt = $conn->prepare("SELECT * FROM PRODUCT WHERE product_id = ?");
$stmt->bind_param('i', $_GET['id']);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();

if (!$p) {
    header('Location: panel.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/style.css"></head>
<body>
<h1>Edit Product</h1>
<form action="update_product.php" method="POST">
    <input type="hidden" name="id" value="<?php echo (int)$p['product_id']; ?>">
    <input name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required><br>
    <input name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($p['price']); ?>" required><br>
    <input name="quantity" type="number" value="<?php echo (int)$p['stock_quantity']; ?>" required><br>
    <input name="description" value="<?php echo htmlspecialchars($p['description']); ?>" required><br>
    <button>Update</button>
</form>
<a href="panel.php">Cancel</a>
</body>
</html>
