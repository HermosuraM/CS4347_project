<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare(
        "INSERT INTO PRODUCT (name, price, stock_quantity, description) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('sdis', $_POST['name'], $_POST['price'], $_POST['quantity'], $_POST['description']);
    $stmt->execute();
}

header('Location: panel.php');
exit;
