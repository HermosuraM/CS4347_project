<?php
session_start();

// Admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

include '../config/db.php';

// CSRF check
if (!isset($_POST['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Invalid CSRF token.");
}

// Validate ID
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    die("Invalid user ID.");
}

// IMPORTANT: get current status from DB
$stmt = $conn->prepare("SELECT status FROM USER WHERE user_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result) {
    die("User not found.");
}

// Toggle safely
$new_status = ($result['status'] === 'active') ? 'suspended' : 'active';

$stmt = $conn->prepare("UPDATE USER SET status = ? WHERE user_id = ?");
$stmt->bind_param('si', $new_status, $id);
$stmt->execute();

header('Location: panel.php');
exit;
?>
