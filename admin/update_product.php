<?php include 'db.php';
$conn->query("UPDATE PRODUCT SET name='$_POST[name]',price='$_POST[price]',stock_quantity='$_POST[quantity]',description='$_POST[description]' WHERE product_id=$_POST[id]");
header("Location: admin_panel.php");
?>