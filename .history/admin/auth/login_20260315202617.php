<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

if (isAdminLoggedIn()) {
    header("Location: ");
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - SoftDrink Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-4">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3>🥤 SoftDrink Admin</h3>
                            <p class="text-muted">Hệ thống quản trị cửa hàng nước giải khát</p>
                        </div>

                        <div class="alert alert-warning text-center">
                            🔐 Trang đăng nhập dành riêng cho <strong>Quản trị viên</strong>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">👤 Tên đăng nhập</label>
                                <input
                                    type="text"
                                    name="username"
                                    class="form-control"
                                    placeholder="Nhập tên đăng nhập"
                                    value="<?php echo htmlspecialchars($username); ?>"
                                    required
                                    autofocus>

                            </div>
                            <div class="mb-3">
                                <label class="form-label">🔒 Mật khẩu</label>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Nhập mật khẩu"
                                    required>
                            </div>
                            <button class="btn btn-primary w-100 py-2">
                                Đăng nhập
                            </button>
                        </form>
                        <p class="text-center mt-3 text-muted small">
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>