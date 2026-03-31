<?php
require_once '../includes/functions.php';
require_once 'config/database.php';

requireSuperAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("
        INSERT INTO admin_users (username, password, full_name, email)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssss", $username, $password, $full_name, $email);
    $stmt->execute();

    header("Location: admin_list.php");
}
?>
<div class="container mt-5">

    <div class="card shadow border-0">

        <div class="card-header bg-success text-white">
            <h5 class="mb-0">➕ Thêm Administrator</h5>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Họ tên</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="admin_list.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button class="btn btn-success">💾 Lưu</button>
                </div>

            </form>

        </div>

    </div>

</div>