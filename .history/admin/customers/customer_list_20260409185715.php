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

            <!-- Form tìm kiếm -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    🔍
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
                                            <!-- Nút Sửa -->
                                            <a href="javascript:void(0)" 
                                               onclick="confirmEdit(<?= $row['id'] ?>)" 
                                               class="btn btn-warning btn-sm"
                                               title="Sửa">
                                                ✏️ Sửa
                                            </a>

                                            <!-- Nút Xóa - chỉ hiện với customer -->
                                            <?php if ($row['role'] !== 'admin'): ?>
                                                <a href="javascript:void(0)" 
                                                   onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>')"
                                                   class="btn btn-danger btn-sm"
                                                   title="Xóa">
                                                    🗑️ Xóa
                                                </a>
                                            <?php endif; ?>

                                            <!-- Nút Khóa/Mở - chỉ hiện với customer -->
                                            <?php if ($row['role'] !== 'admin'): ?>
                                                <?php if ($row['status'] == 1): ?>
                                                    <a href="javascript:void(0)" 
                                                       onclick="confirmLock(<?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>')"
                                                       class="btn btn-secondary btn-sm"
                                                       title="Khóa">
                                                        🔒 Khóa
                                                    </a>
                                                <?php else: ?>
                                                    <a href="javascript:void(0)" 
                                                       onclick="confirmUnlock(<?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>')"
                                                       class="btn btn-info btn-sm"
                                                       title="Mở khóa">
                                                        🔓 Mở
                                                    </a>
                                                <?php endif; ?>

                                                <!-- Nút Reset MK -->
                                                <a href="javascript:void(0)" 
                                                   onclick="confirmReset(<?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>')"
                                                   class="btn btn-dark btn-sm"
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

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ========== SỬA ==========
function confirmEdit(userId) {
    Swal.fire({
        title: '✏️ Chuyển đến trang sửa?',
        text: 'Bạn sẽ được chuyển đến trang sửa thông tin người dùng.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '✅ Tiếp tục',
        cancelButtonText: '❌ Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `customer_edit.php?id=${userId}`;
        }
    });
}

// ========== XÓA ==========
function confirmDelete(userId, userName) {
    Swal.fire({
        title: '🗑️ Xóa người dùng?',
        html: `Bạn có chắc chắn muốn xóa <strong>${userName}</strong>?<br>Hành động này không thể hoàn tác!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '✅ Vâng, xóa!',
        cancelButtonText: '❌ Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`customer_delete.php?id=${userId}`)
                .then(() => {
                    Swal.fire({
                        title: '🗑️ Đã xóa!',
                        text: `Người dùng ${userName} đã được xóa thành công.`,
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                });
        }
    });
}

// ========== KHÓA ==========
function confirmLock(userId, userName) {
    Swal.fire({
        title: '🔒 Khóa tài khoản?',
        html: `Bạn có chắc chắn muốn <strong class="text-danger">KHÓA</strong> tài khoản của <strong>${userName}</strong>?<br>Người dùng sẽ không thể đăng nhập!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6c757d',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '🔒 Vâng, khóa!',
        cancelButtonText: '❌ Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`customer_lock.php?id=${userId}`)
                .then(() => {
                    Swal.fire({
                        title: '🔒 Đã khóa!',
                        text: `Tài khoản ${userName} đã bị khóa thành công.`,
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                });
        }
    });
}

// ========== MỞ KHÓA ==========
function confirmUnlock(userId, userName) {
    Swal.fire({
        title: '🔓 Mở khóa tài khoản?',
        html: `Bạn có chắc chắn muốn <strong class="text-success">MỞ KHÓA</strong> tài khoản của <strong>${userName}</strong>?<br>Người dùng sẽ có thể đăng nhập lại!`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '🔓 Vâng, mở khóa!',
        cancelButtonText: '❌ Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`customer_lock.php?id=${userId}`)
                .then(() => {
                    Swal.fire({
                        title: '🔓 Đã mở khóa!',
                        text: `Tài khoản ${userName} đã được mở khóa thành công.`,
                        icon: 'success',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                });
        }
    });
}

// ========== RESET MẬT KHẨU ==========
function confirmReset(userId, userName) {
    Swal.fire({
        title: '🔄 Reset mật khẩu?',
        html: `Bạn có chắc chắn muốn reset mật khẩu của <strong>${userName}</strong>?<br>Mật khẩu mới sẽ là: <strong class="text-primary">123456</strong>`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '🔄 Vâng, reset!',
        cancelButtonText: '❌ Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng chờ',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    fetch(`customer_resetpw.php?id=${userId}`)
                        .then(() => {
                            Swal.fire({
                                title: '🔄 Reset thành công!',
                                text: `Mật khẩu của ${userName} đã được reset về 123456.`,
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        });
                }
            });
        }
    });
}
</script>

<style>
.btn-group-sm > .btn {
    margin: 0 2px;
    transition: all 0.2s;
}
.btn-group-sm > .btn:hover {
    transform: translateY(-1px);
}
.table th a:hover {
    text-decoration: underline !important;
}
</style>

<?php require_once '../includes/footer.php'; ?>