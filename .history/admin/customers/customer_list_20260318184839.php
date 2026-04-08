<?php
include "../config/database.php";
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$result = $conn->query("SELECT * FROM customers");
?>

<div class="container mt-4">
    <div class="card shadow">

        <!-- Header -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📋 Danh sách khách hàng</h5>
            <a href="customer_add.php" class="btn btn-success btn-sm">+ Thêm</a>
        </div>

        <!-- Table -->
        <div class="card-body">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['full_name'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['role'] ?></td>

                            <td>
                                <?php if ($row['status'] == 1) { ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Đã khóa</span>
                                <?php } ?>
                            </td>

                            <td>
                                <a href="customer_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="customer_delete.php?id=<?= $row['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Xóa?')">Xóa</a>

                                <?php if ($row['status'] == 1) { ?>
                                    <a href="customer_lock.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Khóa</a>
                                <?php } else { ?>
                                    <a href="customer_lock.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Mở</a>
                                <?php } ?>
                                <a href="customer_resetpw.php?id=<?= $row['id'] ?>"
                                    class="btn btn-dark btn-sm"
                                    onclick="return confirm('Reset mật khẩu về 123456?')">
                                    Reset MK
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>