<?php
session_start();
include '../config/db.php';

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hash  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt  = $conn->prepare("INSERT INTO USER (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $_POST['username'], $_POST['email'], $hash);

    if ($stmt->execute()) {
        $id    = $conn->insert_id;
        $stmt2 = $conn->prepare("INSERT INTO ADDRESS (user_id, street, city, state, zip_code) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param('issss', $id, $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip']);
        $stmt2->execute();
        $success = true;
    } else {
        $error = "Username or email already taken.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/style.css"></head>
<body>
<h1>Register</h1>
<?php if ($success): ?>
    <p>Account created! <a href="../index.php">Login</a></p>
<?php else: ?>
    <?php if ($error): ?>
        <p style="color:red"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST">
        <input name="username" placeholder="Username" required><br>
        <input name="email" type="email" placeholder="Email" required><br>
        <input name="password" type="password" placeholder="Password" required><br>
        <input name="street" placeholder="Street address" required><br>
        <input name="city" placeholder="City" required><br>
        <input name="state" placeholder="State" required><br>
        <input name="zip" placeholder="ZIP code" required><br>
        <button>Register</button>
    </form>
    <p><a href="../index.php">Back to login</a></p>
<?php endif; ?>
</body>
</html>
