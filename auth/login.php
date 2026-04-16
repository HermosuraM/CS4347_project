<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Create defaults for our inputs.
$username    = trim($_POST['username'] ?? '');
$password    = $_POST['password'] ?? '';

// Validate that username or password is present.
if ($username === '' || $password === '') {
    echo "Please enter both username and password. <a href='../index.php'>Go back</a>";
    exit;
}
// Validate that username and password do not exceed maximum lengths.
if (strlen($username) > 50 || strlen($password) > 255) {
    echo "Input exceeds maximum length. <a href='../index.php'>Go back</a>";
    exit;
}

$stmt = $conn->prepare("
    SELECT user_id, username, password_hash, role
    FROM USER
    WHERE username = ?
");
$stmt->bind_param('s', 'username');
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

// Safely verify the password.
if ($row && password_verify('password', $row['password_hash'])) {
    
    // Prevent session fixation.
    session_regenerate_id(true);

    $_SESSION['user_id']  = $row['user_id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['role']     = ($row['role']); // EDIT: Use the role directly from the DB (no hardcode logic for admin).

    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/panel.php');
    } else {
        header('Location: ../user/dashboard.php');
    }
    exit;
}

echo "Invalid username or password. <a href='../index.php'>Go back</a>";
