<?php
include "../config/database.php";
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: customer_list.php");
    exit();
}

$result = $conn->query("SELECT * FROM users WHERE id = $id");
$row = $result->fetch_assoc();
if (!$row) {
    header("Location: customer_list.php");
    exit();
}

// Kiểm tra xem có thông báo lỗi không
$error_message = isset($_GET['error']) ? $_GET['error'] : '';
?>

<div class="container mt-5">
    <div class="card shadow">

        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">✏️ Sửa người dùng</h5>
        </div>

        <div class="card-body">

            <!-- Hiển thị toast alert nếu có lỗi -->
            <?php if ($error_message): ?>
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                    <div class="toast-header bg-danger text-white">
                        <strong class="me-auto">⚠️ Lỗi</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <form action="customer_update.php" method="POST" id="editForm">

                <input type="hidden" name="id" value="<?= $row['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text" name="full_name" 
                           class="form-control"
                           value="<?= htmlspecialchars($row['full_name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" 
                           class="form-control"
                           value="<?= htmlspecialchars($row['email']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" 
                           class="form-control"
                           value="<?= htmlspecialchars($row['phone'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" 
                           class="form-control"
                           value="<?= htmlspecialchars($row['address'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" id="roleSelect">
                        <option value="customer" <?= $row['role'] == 'customer' ? 'selected' : '' ?>>Khách hàng</option>
                        <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <?php if ($row['role'] == 'admin'): ?>
                        <small class="text-danger d-block mt-1">
                            ⚠️ Tài khoản Admin không thể đổi thành Khách hàng!
                        </small>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= ($row['status'] == 1) ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= ($row['status'] == 0) ? 'selected' : '' ?>>Bị khóa</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="customer_list.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    // Kiểm tra khi submit form
    document.getElementById('editForm').addEventListener('submit', function(e) {
        const roleSelect = document.getElementById('roleSelect');
        const currentRole = '<?= $row['role'] ?>';
        const newRole = roleSelect.value;
        
        // Nếu tài khoản đang là admin và cố gắng đổi thành customer
        if (currentRole === 'admin' && newRole === 'customer') {
            e.preventDefault(); // Ngăn không cho submit
            
            // Hiển thị toast alert
            showToast('Không thể đổi role Admin thành Khách hàng!', 'danger');
            
            // Reset lại select về admin
            roleSelect.value = 'admin';
        }
    });
    
    // Hàm hiển thị toast
    function showToast(message, type = 'danger') {
        // Tạo toast element
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '11';
        toastContainer.innerHTML = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">⚠️ Lỗi</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        document.body.appendChild(toastContainer);
        
        // Tự động xóa sau 3 giây
        setTimeout(() => {
            toastContainer.remove();
        }, 3000);
    }
</script>

<style>
    .toast {
        min-width: 300px;
    }
</style>

<?php require_once '../includes/footer.php'; ?>