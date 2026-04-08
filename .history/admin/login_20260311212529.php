<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, full_name FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: index.php");
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Soft Drink Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <h2>🥤 SoftDrink Admin</h2>
        <p class="subtitle">Sign in to manage your store</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required 
                       value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:12px;">Sign In</button>
        </form>
        <p style="text-align:center; margin-top:20px; color:#888; font-size:13px;">
            Default: admin / admin123
        </p>
    </div>
</div>
</body>
</html>