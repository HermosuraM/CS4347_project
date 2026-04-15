<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/style.css"></head>
<body>
<h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
<nav>
    <a href="../shop/browse.php">Browse Products</a> |
    <a href="../shop/cart.php">My Cart</a> |
    <a href="profile.php">My Profile</a> |
    <a href="../auth/logout.php">Logout</a>
</nav>
</body>
</html>
