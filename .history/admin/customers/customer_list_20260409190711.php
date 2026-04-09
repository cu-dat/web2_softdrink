<?php
include "../config/database.php";
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/functions.php';

// Kiểm tra đăng nhập admin
requireAdmin($conn);

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Lấy action và message từ URL (dùng cho toast)
$action = isset($_GET['action']) ? $_GET['action'] : '';
$message = isset($_GET['message']) ? $_GET['message'] : '';

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

            <!-- Toast Container -->
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                    <div class="toast-header" id="toastHeader">
                        <strong class="me-auto" id="toastTitle">Thông báo</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body" id="toastMessage">
                        Nội dung thông báo
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
                                               class="btn btn-warning btn-sm btn-action"
                                               data-action="edit"
                                               data-name="<?= htmlspecialchars($row['full_name']) ?>"
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

<!-- Bootstrap JS (nếu chưa có) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Hiển thị toast
function showToast(title, message, type = 'success') {
    const toastEl = document.getElementById('liveToast');
    const toastHeader = document.getElementById('toastHeader');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    
    // Đặt màu sắc theo loại thông báo
    if (type === 'success') {
        toastHeader.className = 'toast-header bg-success text-white';
    } else if (type === 'danger') {
        toastHeader.className = 'toast-header bg-danger text-white';
    } else if (type === 'warning') {
        toastHeader.className = 'toast-header bg-warning text-dark';
    } else {
        toastHeader.className = 'toast-header bg-info text-white';
    }
    
    toastTitle.innerHTML = title;
    toastMessage.innerHTML = message;
    
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 3000
    });
    toast.show();
}

// Xác nhận và thực hiện hành động
function confirmAction(action, userId, userName) {
    let confirmMessage = '';
    let actionTitle = '';
    let successMessage = '';
    let actionUrl = '';
    let type = 'success';
    
    switch(action) {
        case 'delete':
            confirmMessage = `Bạn có chắc chắn muốn xóa người dùng "${userName}"?`;
            actionTitle = '🗑️ Xóa người dùng';
            successMessage = `Đã xóa người dùng "${userName}" thành công!`;
            actionUrl = `customer_delete.php?id=${userId}&action=delete`;
            type = 'danger';
            break;
        case 'lock':
            confirmMessage = `Bạn có chắc chắn muốn KHÓA tài khoản "${userName}"?`;
            actionTitle = '🔒 Khóa tài khoản';
            successMessage = `Đã khóa tài khoản "${userName}" thành công!`;
            actionUrl = `customer_lock.php?id=${userId}&action=lock`;
            type = 'warning';
            break;
        case 'unlock':
            confirmMessage = `Bạn có chắc chắn muốn MỞ KHÓA tài khoản "${userName}"?`;
            actionTitle = '🔓 Mở khóa tài khoản';
            successMessage = `Đã mở khóa tài khoản "${userName}" thành công!`;
            actionUrl = `customer_lock.php?id=${userId}&action=unlock`;
            type = 'success';
            break;
        case 'reset':
            confirmMessage = `Bạn có chắc chắn muốn reset mật khẩu của "${userName}" về 123456?`;
            actionTitle = '🔄 Reset mật khẩu';
            successMessage = `Đã reset mật khẩu của "${userName}" về 123456 thành công!`;
            actionUrl = `customer_resetpw.php?id=${userId}&action=reset`;
            type = 'info';
            break;
    }
    
    if (confirm(confirmMessage)) {
        // Gửi request bằng fetch
        fetch(actionUrl)
            .then(() => {
                showToast(actionTitle, successMessage, type);
                // Reload lại trang sau 1 giây để cập nhật danh sách
                setTimeout(() => {
                    location.reload();
                }, 1000);
            })
            .catch(error => {
                showToast('Lỗi', 'Có lỗi xảy ra, vui lòng thử lại!', 'danger');
            });
    }
}

// Toast cho action sửa (khi chuyển trang)
document.querySelectorAll('.btn-action').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const action = this.getAttribute('data-action');
        const name = this.getAttribute('data-name');
        if (action === 'edit') {
            showToast('✏️ Sửa người dùng', `Đang chuyển đến trang sửa của "${name}"...`, 'info');
        }
    });
});

// Kiểm tra URL parameter để hiển thị toast khi quay về từ trang edit
window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const action = urlParams.get('action');
    const message = urlParams.get('message');
    
    if (action === 'edit_success') {
        showToast('✏️ Sửa thành công', message || 'Cập nhật thông tin người dùng thành công!', 'success');
        // Xóa parameter trên URL để không hiện lại khi refresh
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
    min-width: 300px;
}
</style>

<?php require_once '../includes/footer.php'; ?>