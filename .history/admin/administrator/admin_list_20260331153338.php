<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

requireAdminLogin();

// lấy danh sách admin
$result = $conn->query("SELECT * FROM admin_users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

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
                            <th>Trạng thái </th>
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
                                <td><?= $row['status'] == 1 ? 'Hoạt động' : 'Đã khóa' ?></td>

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
                                    <a href="admin_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️</a>

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
</body>

</html>
<?php require_once '../includes/footer.php'; ?>