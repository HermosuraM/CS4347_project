<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';

if (isset($_GET['id'])) {
    $stmt = $conn->prepare("DELETE FROM USER WHERE user_id = ?");
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
}

header('Location: panel.php');
exit;
