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

// Lấy action từ URL để hiển thị toast
$toast_action = isset($_GET['toast']) ? $_GET['toast'] : '';
$toast_name = isset($_GET['name']) ? urldecode($_GET['name']) : '';
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

            <!-- Toast góc phải màn hình -->
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
                <div id="liveToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                    <div class="d-flex">
                        <div class="toast-body" id="toastMessage">
                            <!-- Nội dung toast sẽ hiển thị ở đây -->
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>

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
                                            <a href="customer_edit.php?id=<?= $row['id'] ?>" 
                                               class="btn btn-warning btn-sm"
                                               title="Sửa">
                                                ✏️ Sửa
                                            </a>

                                            <!-- ❌ không cho xóa admin -->
                                            <?php if ($row['role'] !== 'admin'): ?>
                                                <a href="javascript:void(0)" 
                                                   onclick="confirmAction('delete', <?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>')"
                                                   class="btn btn-danger btn-sm"
                                                   title="Xóa">
                                                    🗑️ Xóa
                                                </a>
                                            <?php endif; ?>

                                            <!-- ❌ không cho khóa/reset admin -->
                                            <?php if ($row['role'] !== 'admin'): ?>
                                                <?php if ($row['status'] == 1): ?>
                                                    <a href="javascript:void(0)" 
                                                       onclick="confirmAction('lock', <?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>')"
                                                       class="btn btn-secondary btn-sm"
                                                       title="Khóa">
                                                        🔒 Khóa
                                                    </a>
                                                <?php else: ?>
                                                    <a href="javascript:void(0)" 
                                                       onclick="confirmAction('unlock', <?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>')"
                                                       class="btn btn-info btn-sm"
                                                       title="Mở khóa">
                                                        🔓 Mở
                                                    </a>
                                                <?php endif; ?>

                                                <a href="javascript:void(0)" 
                                                   onclick="confirmAction('reset', <?= $row['id'] ?>, '<?= htmlspecialchars($row['full_name']) ?>')"
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Hàm hiển thị toast góc phải
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('liveToast');
    const toastBody = document.getElementById('toastMessage');
    
    // Đặt màu nền theo loại thông báo
    let bgColor = '';
    let icon = '';
    
    switch(type) {
        case 'success':
            bgColor = '#28a745';
            icon = '✅ ';
            break;
        case 'danger':
            bgColor = '#dc3545';
            icon = '❌ ';
            break;
        case 'warning':
            bgColor = '#ffc107';
            icon = '⚠️ ';
            break;
        case 'info':
            bgColor = '#17a2b8';
            icon = 'ℹ️ ';
            break;
        default:
            bgColor = '#28a745';
            icon = '✅ ';
    }
    
    toastBody.innerHTML = icon + message;
    toastEl.style.backgroundColor = bgColor;
    toastEl.style.color = 'white';
    
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 3000
    });
    toast.show();
}

// Xác nhận và thực hiện hành động
function confirmAction(action, userId, userName) {
    let confirmMessage = '';
    let successMessage = '';
    let actionUrl = '';
    let type = 'success';
    
    switch(action) {
        case 'delete':
            confirmMessage = `Bạn có chắc chắn muốn xóa người dùng "${userName}"?`;
            successMessage = `Đã xóa người dùng "${userName}" thành công!`;
            actionUrl = `customer_delete.php?id=${userId}&toast=delete&name=${encodeURIComponent(userName)}`;
            type = 'danger';
            break;
        case 'lock':
            confirmMessage = `Bạn có chắc chắn muốn KHÓA tài khoản "${userName}"?`;
            successMessage = `Đã khóa tài khoản "${userName}" thành công!`;
            actionUrl = `customer_lock.php?id=${userId}&toast=lock&name=${encodeURIComponent(userName)}`;
            type = 'warning';
            break;
        case 'unlock':
            confirmMessage = `Bạn có chắc chắn muốn MỞ KHÓA tài khoản "${userName}"?`;
            successMessage = `Đã mở khóa tài khoản "${userName}" thành công!`;
            actionUrl = `customer_lock.php?id=${userId}&toast=unlock&name=${encodeURIComponent(userName)}`;
            type = 'success';
            break;
        case 'reset':
            confirmMessage = `Bạn có chắc chắn muốn reset mật khẩu của "${userName}" về 123456?`;
            successMessage = `Đã reset mật khẩu của "${userName}" về 123456 thành công!`;
            actionUrl = `customer_resetpw.php?id=${userId}&toast=reset&name=${encodeURIComponent(userName)}`;
            type = 'info';
            break;
    }
    
    if (confirm(confirmMessage)) {
        window.location.href = actionUrl;
    }
}

// Hiển thị toast khi load trang (nếu có action từ URL)
window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const toastAction = urlParams.get('toast');
    const userName = urlParams.get('name');
    
    if (toastAction && userName) {
        let message = '';
        switch(toastAction) {
            case 'delete':
                message = `🗑️ Đã xóa người dùng "${userName}" thành công!`;
                showToast(message, 'danger');
                break;
            case 'lock':
                message = `🔒 Đã khóa tài khoản "${userName}" thành công!`;
                showToast(message, 'warning');
                break;
            case 'unlock':
                message = `🔓 Đã mở khóa tài khoản "${userName}" thành công!`;
                showToast(message, 'success');
                break;
            case 'reset':
                message = `🔄 Đã reset mật khẩu của "${userName}" về 123456!`;
                showToast(message, 'info');
                break;
            case 'edit_success':
                message = `✏️ Đã sửa thông tin người dùng thành công!`;
                showToast(message, 'success');
                break;
        }
        
        // Xóa parameter trên URL sau khi hiển thị toast
        const newUrl = window.location.pathname + '?search=' + '<?= $search ?>';
        window.history.replaceState({}, document.title, newUrl);
    }
});
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
.input-group-text i {
    font-size: 14px;
}
.toast {
    min-width: 320px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

<?php require_once '../includes/footer.php'; ?>