<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="assets/style.css"></head>
<body>
<h1>Retail System</h1>

<h2>Login</h2>
<form action="auth/login.php" method="POST">
    <input name="username" placeholder="Username" required><br>
    <input name="password" type="password" placeholder="Password" required><br>
    <button>Login</button>
</form>

<p><a href="auth/register.php">Create an account</a> &nbsp;|&nbsp; <a href="guest.php">Continue as Guest</a></p>
</body>
</html>
