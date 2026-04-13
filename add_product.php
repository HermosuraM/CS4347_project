<?php include 'db.php';
$conn->query("INSERT INTO PRODUCT (name,price,stock_quantity,description)
VALUES ('$_POST[name]','$_POST[price]','$_POST[quantity]','$_POST[description]')");
header("Location: admin_panel.php");
?>