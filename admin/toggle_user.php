<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';

if (isset($_GET['id'], $_GET['status'])) {
    $new_status = ($_GET['status'] === 'active') ? 'suspended' : 'active';
    $stmt = $conn->prepare("UPDATE USER SET status = ? WHERE user_id = ?");
    $stmt->bind_param('si', $new_status, $_GET['id']);
    $stmt->execute();
}

header('Location: panel.php');
exit;
