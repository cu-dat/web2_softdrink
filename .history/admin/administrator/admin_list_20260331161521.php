<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

requireAdminLogin($conn);

// lấy danh sách admin
$result = $conn->query("SELECT * FROM admin_users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <div id="liveToast" class="toast align-items-center text-white border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <div class="container mt-4">

        <div class="card shadow">

            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <h5>👤 Quản lý Administrator</h5>

                <?php if (isSuperAdmin()): ?>
                    <a href="admin_add.php" class="btn btn-success btn-sm">+ Thêm admin</a>
                <?php endif; ?>
            </div>

            <div class="card-body">

                <table class="table table-bordered table-hover text-center">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['username'] ?></td>
                                <td><?= $row['full_name'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['created_at'] ?></td>

                                <td>
                                    <a href="admin_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>

                                    <?php if (isSuperAdmin()): ?>

                                        <!-- Reset -->
                                        <a href="admin_reset.php?id=<?= $row['id'] ?>"
                                            class="btn btn-info btn-sm"
                                            onclick="return confirm('Reset mật khẩu?')">
                                            Reset MK
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($row['status'] == 1): ?>
                                        <a href="admin_lock.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Khóa tài khoản này?')">
                                            🔒 Khóa
                                        </a>
                                    <?php else: ?>
                                        <a href="admin_lock.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-success"
                                            onclick="return confirm('Mở khóa tài khoản này?')">
                                            🔓 Mở
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 1): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Đã khóa</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const params = new URLSearchParams(window.location.search);
            const msg = params.get('msg');

            if (msg) {
                const toastEl = document.getElementById('liveToast');
                const toastBody = document.getElementById('toastMessage');

                if (!toastEl) return;

                toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');

                if (msg === 'locked') {
                    toastEl.classList.add('bg-danger');
                    toastBody.innerText = '🔒 Đã khóa tài khoản!';
                } else if (msg === 'unlocked') {
                    toastEl.classList.add('bg-success');
                    toastBody.innerText = '🔓 Đã mở khóa tài khoản!';
                } else if (msg === 'reset') {
                    toastEl.classList.add('bg-info');
                    toastBody.innerText = '🔑 Đã reset mật khẩu về 123456!';
                } else if (msg === 'self') {
                    toastEl.classList.add('bg-warning');
                    toastBody.innerText = '⚠️ Không thể thao tác với super admin!';
                } else if (msg === 'forbidden') {
                    toastEl.classList.add('bg-warning');
                    toastBody.innerText = '⚠️ Bạn không có quyền thực hiện thao tác này!';
                } else {
                    toastEl.classList.add('bg-warning');
                    toastBody.innerText = '⚠️ Có lỗi xảy ra!';
                }

                const toast = new bootstrap.Toast(toastEl);
                toast.show();

                // xoá ?msg= trên URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }

        });
    </script>
</body>

</html>
<?php require_once '../includes/footer.php'; ?>