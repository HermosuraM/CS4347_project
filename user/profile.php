<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';

$uid = $_SESSION['user_id'];
$message = '';

// Load current data
$stmt = $conn->prepare(
    "SELECT u.username, u.email, up.first_name, up.last_name, up.phone_number,
            a.street, a.city, a.state, a.zip_code
     FROM USER u
     LEFT JOIN USER_PROFILE up ON up.user_id = u.user_id
     LEFT JOIN ADDRESS a ON a.user_id = u.user_id
     WHERE u.user_id = ?
     LIMIT 1"
);
$stmt->bind_param('i', $uid);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upsert USER_PROFILE
    $stmt2 = $conn->prepare(
        "INSERT INTO USER_PROFILE (user_id, first_name, last_name, phone_number)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE first_name=VALUES(first_name), last_name=VALUES(last_name), phone_number=VALUES(phone_number)"
    );
    $stmt2->bind_param('isss', $uid, $_POST['first_name'], $_POST['last_name'], $_POST['phone']);
    $stmt2->execute();

    // Update address
    $stmt3 = $conn->prepare(
        "UPDATE ADDRESS SET street=?, city=?, state=?, zip_code=? WHERE user_id=?"
    );
    $stmt3->bind_param('ssssi', $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip'], $uid);
    $stmt3->execute();

    // Change password if provided
    if (!empty($_POST['new_password'])) {
        $hash  = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt4 = $conn->prepare("UPDATE USER SET password_hash=? WHERE user_id=?");
        $stmt4->bind_param('si', $hash, $uid);
        $stmt4->execute();
    }

    $message = 'Profile updated.';

    // Reload updated data
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="../assets/style.css"></head>
<body>
<h1>My Profile</h1>
<nav>
    <a href="dashboard.php">&larr; Dashboard</a> |
    <a href="../auth/logout.php">Logout</a>
</nav>

<?php if ($message): ?>
    <p style="color:green"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="POST">
    <h2>Personal Info</h2>
    <input name="first_name" placeholder="First name" value="<?php echo htmlspecialchars($data['first_name'] ?? ''); ?>"><br>
    <input name="last_name"  placeholder="Last name"  value="<?php echo htmlspecialchars($data['last_name']  ?? ''); ?>"><br>
    <input name="phone" placeholder="Phone number" value="<?php echo htmlspecialchars($data['phone_number'] ?? ''); ?>"><br>

    <h2>Address</h2>
    <input name="street" placeholder="Street" value="<?php echo htmlspecialchars($data['street'] ?? ''); ?>" required><br>
    <input name="city"   placeholder="City"   value="<?php echo htmlspecialchars($data['city']   ?? ''); ?>" required><br>
    <input name="state"  placeholder="State"  value="<?php echo htmlspecialchars($data['state']  ?? ''); ?>" required><br>
    <input name="zip"    placeholder="ZIP"    value="<?php echo htmlspecialchars($data['zip_code'] ?? ''); ?>" required><br>

    <h2>Change Password</h2>
    <input name="new_password" type="password" placeholder="New password (leave blank to keep current)"><br>

    <button>Save Changes</button>
</form>
</body>
</html>
