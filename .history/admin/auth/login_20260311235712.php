<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

if (isAdminLoggedIn()) {
    header("Location: ../index.php");
    exit();
}
$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    } else {
        // ===== SỬA: Bỏ role và status khỏi query =====
        $stmt = $conn->prepare("SELECT id, username, password, full_name FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            if (password_verify($password, $admin['password'])) {
                // ===== ĐĂNG NHẬP THÀNH CÔNG =====
                $_SESSION['admin_id']       = $admin['id'];
                $_SESSION['admin_name']     = $admin['full_name'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role']     = 'super_admin'; // Mặc định
                $_SESSION['login_time']     = time();

                header("Location: index.php");
                exit();
            } else {
                $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
            }
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <!-- Bootstrap CSS -->
<   link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - SoftDrink Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <div class="login-logo">
            <span class="icon">🥤</span>
            <h2>SoftDrink Admin Panel</h2>
            <p>Hệ thống quản trị cửa hàng nước giải khát</p>
        </div>

        <div class="admin-url-notice">
            🔐 Trang đăng nhập dành riêng cho <strong>Quản trị viên</strong>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>👤 Tên đăng nhập</label>
                <input type="text" name="username" placeholder="Nhập tên đăng nhập" required 
                       value="<?php echo htmlspecialchars($username); ?>" autofocus>
            </div>
            <div class="form-group">
                <label>🔒 Mật khẩu</label>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-size:15px;">
                Đăng nhập
            </button>
        </form>
        <p style="text-align:center; margin-top:20px; color:#aaa; font-size:12px;">
            Mặc định: admin / admin123
        </p>
    </div>
</div>
</body>
</html>