<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

// CSRF check
if (!isset($_POST['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Invalid CSRF token.");
}

// Validate inputs
$id = (int)($_POST['id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$price = (float)($_POST['price'] ?? 0);
$qty = (int)($_POST['quantity'] ?? 0);
$desc = trim($_POST['description'] ?? '');

if ($id <= 0 || $name === '' || $price < 0 || $qty < 0) {
    die("Invalid input.");
}

include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare(
        "UPDATE PRODUCT SET name=?, price=?, stock_quantity=?, description=? WHERE product_id=?"
    );
    $stmt->bind_param('sdisi', $_POST['name'], $_POST['price'], $_POST['quantity'], $_POST['description'], $_POST['id']);
    $stmt->execute();
}

header('Location: panel.php');
exit;
?>
