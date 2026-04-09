<?php
include "../config/database.php";
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/functions.php';

// Kiểm tra đăng nhập admin
requireAdmin($conn);

// Xử lý tìm kiếm
$search = $_GET['search'] ?? '';
$where = "";

if ($search) {
    $search_esc = $conn->real_escape_string($search);
    $where = "WHERE full_name LIKE '%$search_esc%' OR email LIKE '%$search_esc%' OR phone LIKE '%$search_esc%'";
}

// Lấy danh sách users
$sql = "SELECT * FROM users $where ORDER BY FIELD(role, 'admin'), id ASC";
$result = $conn->query($sql);

// Lấy thông báo flash nếu có
$flash = getFlashMessage();
?>

<div class="container mt-4">
    <div class="card shadow">

        <!-- Header -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📋 Danh sách người dùng</h5>
            <a href="customer_add.php" class="btn btn-success btn-sm">+ Thêm</a>
        </div>

        <div class="card-body">

            <!-- Hiển thị flash message -->
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                    <?= htmlspecialchars($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form tìm kiếm -->
            <div class="row mb-3">
                <div class="col-md-4 ms-auto">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" 
                               placeholder="🔍 Tìm theo tên, email hoặc số điện thoại..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i> Tìm
                        </button>
                        <?php if ($search): ?>
                            <a href="customer_list.php" class="btn btn-outline-secondary ms-2">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Role</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($result->num_rows == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    📭 Không tìm thấy người dùng nào
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td><?= htmlspecialchars($row['email'] ?? '---') ?></td>
                                    <td><?= htmlspecialchars($row['phone'] ?? '---') ?></td>
                                    <td><?= htmlspecialchars($row['address'] ?? '---') ?></td>

                                    <!-- ROLE -->
                                    <td><?= getRoleBadge($row['role']) ?></td>

                                    <!-- STATUS -->
                                    <td><?= getUserStatusBadge($row['status']) ?></td>

                                    <td class="text-nowrap">
                                        <a href="customer_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>

                                        <!-- ❌ không cho xóa admin -->
                                        <?php if ($row['role'] !== 'admin'): ?>
                                            <a href="customer_delete.php?id=<?= $row['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Xóa người dùng này?')">
                                                Xóa
                                            </a>
                                        <?php endif; ?>

                                        <!-- ❌ không cho khóa/reset admin -->
                                        <?php if ($row['role'] !== 'admin'): ?>
                                            <?php if ($row['status'] == 1): ?>
                                                <a href="customer_lock.php?id=<?= $row['id'] ?>" 
                                                   class="btn btn-secondary btn-sm"
                                                   onclick="return confirm('Khóa tài khoản này?')">Khóa</a>
                                            <?php else: ?>
                                                <a href="customer_lock.php?id=<?= $row['id'] ?>" 
                                                   class="btn btn-info btn-sm"
                                                   onclick="return confirm('Mở khóa tài khoản này?')">Mở</a>
                                            <?php endif; ?>

                                            <a href="customer_resetpw.php?id=<?= $row['id'] ?>"
                                                class="btn btn-dark btn-sm"
                                                onclick="return confirm('Reset mật khẩu về 123456?')">
                                                Reset MK
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Hiển thị tổng số -->
            <div class="mt-3 text-muted">
                <small>📊 Tổng số: <?= $result->num_rows ?> người dùng</small>
            </div>

        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>