<?php session_start(); include 'db.php';
$user=$_SESSION['user']??'guest';
$u=$conn->query("SELECT * FROM USER WHERE username='$user'")->fetch_assoc();
$uid=$u['user_id']??0;

if(isset($_GET['id'])){
$conn->query("INSERT INTO CART_ITEM (cart_id,product_id,quantity,price_at_time)
VALUES (1,$_GET[id],1,10)");
}

echo "<h1>Cart</h1>";
$r=$conn->query("SELECT * FROM CART_ITEM");
while($row=$r->fetch_assoc()){
 echo "Product ".$row['product_id']."<br>";
}
echo "<a href='checkout.php'>Checkout</a>";
?>