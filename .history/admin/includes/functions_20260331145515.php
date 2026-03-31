<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== KIỂM TRA ĐĂNG NHẬP =====
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' ₫';
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function getUserStatusBadge($status) {
    return $status == 1 
        ? '<span class="badge badge-success">Hoạt động</span>' 
        : '<span class="badge badge-danger">Đã khóa</span>';
}

function getOrderStatusBadge($status) {
    $badges = [
        'pending'    => '<span class="badge badge-warning">Chờ xử lý</span>',
        'processing' => '<span class="badge badge-info">Đang xử lý</span>',
        'completed'  => '<span class="badge badge-success">Hoàn thành</span>',
        'cancelled'  => '<span class="badge badge-danger">Đã hủy</span>',
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">???</span>';
}

function getRoleBadge($role) {
    $badges = [
        'super_admin' => '<span class="badge badge-primary">Super Admin</span>',
        'admin'       => '<span class="badge badge-info">Admin</span>',
        'staff'       => '<span class="badge badge-secondary">Nhân viên</span>',
    ];
    return $badges[$role] ?? '';
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function generateRandomPassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

function logAdminAction($conn, $action, $detail = '') {
    $admin_id = $_SESSION['admin_id'] ?? 0;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, detail, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $admin_id, $action, $detail, $ip);
    $stmt->execute();
    $stmt->close();
}
?>