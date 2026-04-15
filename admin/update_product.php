<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
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
