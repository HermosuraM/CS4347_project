<!DOCTYPE html>
<html><head><link rel="stylesheet" href="assets/style.css"></head>
<body>
<h1>Retail System</h1>

<h2>User Login</h2>
<form action="login.php" method="POST">
<input name="username" required><br>
<input name="password" type="password" required><br>
<button>Login</button>
</form>

<h2>Admin Login</h2>
<form action="login.php" method="POST">
<input name="username" required><br>
<input name="password" required><br>
<button>Admin</button>
</form>

<h2>Guest</h2>
<a href="guest.php"><button>Continue as Guest</button></a>
</body></html>