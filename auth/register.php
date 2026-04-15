<?php include 'db.php'; ?>
<h1>Register</h1>
<form method="POST">
<input name="username" required><br>
<input name="email" required><br>
<input name="password" required><br>
<input name="street" placeholder="Address" required><br>
<input name="city" required><br>
<input name="state" required><br>
<input name="zip" required><br>
<button>Submit</button>
</form>
<?php
if($_POST){
$conn->query("INSERT INTO USER (username,email,password_hash) VALUES ('$_POST[username]','$_POST[email]','$_POST[password]')");
$id=$conn->insert_id;
$conn->query("INSERT INTO ADDRESS (user_id,street,city,state,zip_code) VALUES ($id,'$_POST[street]','$_POST[city]','$_POST[state]','$_POST[zip]')");
echo "Done <a href='index.php'>Login</a>";
}
?>