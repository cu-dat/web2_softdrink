<?php
require_once 'includes/functions.php';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

requireAdminLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($newPassword) || empty($confirmPassword)) {
        $error = "Vui lòng nhập đầy đủ.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Mật khẩu không khớp.";
    } else {

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
            UPDATE admin_users 
            SET password=?, must_change_password=0 
            WHERE id=?
        ");

        $stmt->bind_param("si", $hashed, $_SESSION['admin_id']);
        $stmt->execute();

        // 👉 sau khi đổi xong → về dashboard
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow">

                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">🔒 Đổi mật khẩu</h5>
                    </div>

                    <div class="card-body">

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nhập lại mật khẩu</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">⬅ Quay lại</a>
                                <button type="submit" class="btn btn-primary">💾 Đổi mật khẩu</button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>

</body>

</html>
<?php require_once 'includes/footer.php'; ?>