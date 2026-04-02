<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== KIỂM TRA ĐĂNG NHẬP =====
function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin($conn) {
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit();
    }

    // 🔥 check bị khóa
    checkAdminActive($conn);
}

// ===== CHECK ROLE =====
function isSuperAdmin()
{
    return isset($_SESSION['admin_role'])
        && $_SESSION['admin_role'] === 'super_admin';
}

function isAdmin()
{
    return isset($_SESSION['admin_role'])
        && in_array($_SESSION['admin_role'], ['admin', 'super_admin']);
}

// ===== REQUIRE SUPER ADMIN =====
function requireSuperAdmin()
{
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit();
    }

    if (!isSuperAdmin()) {
        die("Bạn không có quyền truy cập!");
    }
}

function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatCurrency($amount)
{
    return number_format($amount, 0, ',', '.') . ' ₫';
}

function formatDate($date)
{
    return date('d/m/Y H:i', strtotime($date));
}

function getUserStatusBadge($status)
{
    return $status == 1
        ? '<span class="badge badge-success">Hoạt động</span>'
        : '<span class="badge badge-danger">Đã khóa</span>';
}

function getOrderStatusBadge($status)
{
    $badges = [
        'pending'    => '<span class="badge badge-warning">Chờ xử lý</span>',
        'processing' => '<span class="badge badge-info">Đang xử lý</span>',
        'completed'  => '<span class="badge badge-success">Hoàn thành</span>',
        'cancelled'  => '<span class="badge badge-danger">Đã hủy</span>',
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">???</span>';
}

function getRoleBadge($role)
{
    $badges = [
        'super_admin' => '<span class="badge badge-primary">Super Admin</span>',
        'admin'       => '<span class="badge badge-info">Admin</span>',
        'staff'       => '<span class="badge badge-secondary">Nhân viên</span>',
    ];
    return $badges[$role] ?? '';
}

function setFlashMessage($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function generateRandomPassword($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

function logAdminAction($conn, $action, $detail = '')
{
    $admin_id = $_SESSION['admin_id'] ?? 0;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, detail, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $admin_id, $action, $detail, $ip);
    $stmt->execute();
    $stmt->close();
}
function checkAdminActive($conn)
{
    if (!isset($_SESSION['admin_id'])) {
        return;
    }

    $stmt = $conn->prepare("SELECT status FROM admin_users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    // ❌ nếu bị khóa hoặc không tồn tại
    if (!$admin || (int)$admin['status'] === 0) {
        session_destroy();
        header("Location: login.php?error=locked");
        exit();
    }
}
