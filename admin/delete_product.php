<?php include 'db.php';
$conn->query("DELETE FROM PRODUCT WHERE product_id=$_GET[id]");
header("Location: admin_panel.php");
?>