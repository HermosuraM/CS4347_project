<?php include 'db.php';
$p=$conn->query("SELECT * FROM PRODUCT WHERE product_id=$_GET[id]")->fetch_assoc();
?>
<form action="update_product.php" method="POST">
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
<input name="name" value="<?php echo $p['name']; ?>">
<input name="price" value="<?php echo $p['price']; ?>">
<input name="quantity" value="<?php echo $p['stock_quantity']; ?>">
<input name="description" value="<?php echo $p['description']; ?>">
<button>Update</button>
</form>