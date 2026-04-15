<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$stmt = $conn->prepare("SELECT user_id, username, password_hash FROM USER WHERE username = ?");
$stmt->bind_param('s', $_POST['username']);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row && password_verify($_POST['password'], $row['password_hash'])) {
    $_SESSION['user_id']  = $row['user_id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['role']     = ($row['username'] === 'admin') ? 'admin' : 'user';

    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/panel.php');
    } else {
        header('Location: ../user/dashboard.php');
    }
    exit;
}

echo "Invalid username or password. <a href='../index.php'>Go back</a>";
