<?php
include "../config/database.php";
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/functions.php';

// Kiểm tra đăng nhập admin
requireAdmin($conn);

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xây dựng câu lệnh SQL
$sql = "SELECT * FROM users";

if ($search !== '') {
    $search_esc = $conn->real_escape_string($search);
    $sql .= " WHERE full_name LIKE '%$search_esc%' 
              OR email LIKE '%$search_esc%' 
              OR phone LIKE '%$search_esc%'
              OR id LIKE '%$search_esc%'";
}

// Sắp xếp theo ID từ nhỏ đến lớn (ASC)
$sql .= " ORDER BY id ASC";

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

            <!-- Form tìm kiếm - sửa lại giao diện -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search">🔍</i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Tìm kiếm theo ID, tên, email hoặc số điện thoại..." 
                                       value="<?= htmlspecialchars($search) ?>">
                                <?php if ($search !== ''): ?>
                                    <a href="customer_list.php" class="btn btn-outline-secondary" type="button">
                                        ✖️ Xóa
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            🔍 Tìm kiếm
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hiển thị kết quả tìm kiếm -->
            <?php if ($search !== ''): ?>
                <div class="alert alert-info mb-3">
                    <strong>🔎 Kết quả tìm kiếm:</strong> "<?= htmlspecialchars($search) ?>" 
                    - Tìm thấy <strong><?= $result->num_rows ?></strong> người dùng
                    <a href="customer_list.php" class="float-end text-decoration-none">✖️ Xóa tìm kiếm</a>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>
                                <a href="?sort=id&order=asc" class="text-dark text-decoration-none">
                                    ID ⬆️
                                </a>
                            </th>
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
                                <td colspan="8" class="text-center text-muted py-5">
                                    📭 Không tìm thấy người dùng nào
                                    <?php if ($search !== ''): ?>
                                        <br>
                                        <a href="customer_list.php" class="btn btn-sm btn-outline-primary mt-2">Xóa tìm kiếm</a>
                                    <?php endif; ?>
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
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="customer_edit.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-warning btn-sm"
                                               title="Sửa">
                                                ✏️ Sửa
                                            </a>

                                            <!-- ❌ không cho xóa admin -->
                                            <?php if ($row['role'] !== 'admin'): ?>
                                                <a href="customer_delete.php?id=<?= $row['id'] ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Xóa người dùng này?')"
                                                   title="Xóa">
                                                    🗑️ Xóa
                                                </a>
                                            <?php endif; ?>

                                            <!-- ❌ không cho khóa/reset admin -->
                                            <?php if ($row['role'] !== 'admin'): ?>
                                                <?php if ($row['status'] == 1): ?>
                                                    <a href="customer_lock.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-secondary btn-sm"
                                                       onclick="return confirm('Khóa tài khoản này?')"
                                                       title="Khóa">
                                                        🔒 Khóa
                                                    </a>
                                                <?php else: ?>
                                                    <a href="customer_lock.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-info btn-sm"
                                                       onclick="return confirm('Mở khóa tài khoản này?')"
                                                       title="Mở khóa">
                                                        🔓 Mở
                                                    </a>
                                                <?php endif; ?>

                                                <a href="customer_resetpw.php?id=<?= $row['id'] ?>"
                                                   class="btn btn-dark btn-sm"
                                                   onclick="return confirm('Reset mật khẩu về 123456?')"
                                                   title="Reset mật khẩu">
                                                    🔄 Reset MK
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Hiển thị tổng số và thống kê -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="text-muted">
                        <small>📊 Tổng số: <strong><?= $result->num_rows ?></strong> người dùng</small>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <?php
                    // Thống kê số lượng admin và customer
                    $stats = $conn->query("SELECT 
                        SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admin,
                        SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as total_customer
                    FROM users");
                    $stats_row = $stats->fetch_assoc();
                    ?>
                    <small class="text-muted">
                        👑 Admin: <?= $stats_row['total_admin'] ?? 0 ?> | 
                        👤 Khách hàng: <?= $stats_row['total_customer'] ?? 0 ?>
                    </small>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
.btn-group-sm > .btn {
    margin: 0 2px;
}
.table th a:hover {
    text-decoration: underline !important;
}
.input-group-text i {
    font-size: 14px;
}
</style>

<?php require_once '../includes/footer.php'; ?>