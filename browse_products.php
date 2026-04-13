<?php include 'db.php'; ?>
<h1>Products</h1>
<?php
$r=$conn->query("SELECT * FROM PRODUCT");
while($row=$r->fetch_assoc()){
 echo $row['name']." $".$row['price']." <a href='cart.php?id=".$row['product_id']."'>Add</a><br>";
}
?>