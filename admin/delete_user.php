<?php
session_start();

// 1. Enforce admin-only access.
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

// 2. Allow only POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

include '.../config/db.php';

// 3. Validate input.
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    die("Invalid user ID.");
}

// 4. Delete user.
$stmt = $conn->prepare("DELETE FROM USER WHERE user_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

// 5. Redirect back to panel.
header('Location: panel.php');
exit;
?>
