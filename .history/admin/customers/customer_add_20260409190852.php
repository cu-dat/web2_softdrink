<?php 
include "../config/database.php";
require_once '../includes/functions.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Lấy thông báo từ URL (nếu có)
$toast_message = isset($_GET['message']) ? urldecode($_GET['message']) : '';
$toast_type = isset($_GET['type']) ? $_GET['type'] : '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm khách hàng</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f6f9;">

<div class="container mt-5">
    <div class="card shadow">
        
        <!-- Header -->
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">➕ Thêm người dùng</h4>
        </div>

        <!-- Body -->
        <div class="card-body">

            <!-- Toast Container - góc phải màn hình -->
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

            <form action="customer_insert.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Họ tên *</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="customer">Khách hàng</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Button -->
                <div class="d-flex justify-content-between">
                    <a href="customer_list.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-success">💾 Lưu</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Hàm hiển thị toast
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('liveToast');
    const toastBody = document.getElementById('toastMessage');
    
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

// Hiển thị toast khi load trang (nếu có thông báo từ URL)
window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    const type = urlParams.get('type');
    
    if (message && type) {
        showToast(message, type);
        
        // Xóa parameter trên URL sau khi hiển thị toast
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
});
</script>

<style>
.toast {
    min-width: 320px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

</body>
</html>

<?php require_once '../includes/footer.php'; ?>