<?php include 'db.php'; ?>
<h1>Admin</h1>
<form action="add_product.php" method="POST">
<input name="name" required>
<input name="price" required>
<input name="quantity" required>
<input name="description" required>
<button>Add</button>
</form>
<?php
$r=$conn->query("SELECT * FROM PRODUCT");
while($row=$r->fetch_assoc()){
 echo $row['name']." 
 <a href='edit_product.php?id=".$row['product_id']."'>Edit</a>
 <a href='delete_product.php?id=".$row['product_id']."'>Delete</a><br>";
}
?>