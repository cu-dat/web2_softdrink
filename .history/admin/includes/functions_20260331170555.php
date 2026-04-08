<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =======================
// AUTH
// =======================

// ✅ check login
function isLoggedIn()
{
    return isset($_SESSION['user']);
}

// ✅ lấy user hiện tại
function currentUser()
{
    return $_SESSION['user'] ?? null;
}

// ✅ require login
function requireLogin($conn)
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }

    checkUserActive($conn);
}

// =======================
// ROLE
// =======================

function isAdmin()
{
    return isset($_SESSION['user']['role']) &&
        in_array($_SESSION['user']['role'], ['admin', 'super_admin']);
}

function isSuperAdmin()
{
    return isset($_SESSION['user']['role']) &&
        $_SESSION['user']['role'] === 'super_admin';
}

// =======================
// REQUIRE
// =======================

function requireAdmin($conn)
{
    requireLogin($conn);

    if (!isAdmin()) {
        header("Location: login.php?msg=forbidden");
        exit();
    }
}

function requireSuperAdmin($conn)
{
    requireLogin($conn);

    if (!isSuperAdmin()) {
        die("⛔ Chỉ Super Admin!");
    }
}

// =======================
// CHECK ACTIVE (FIX USERS)
// =======================

function checkUserActive($conn)
{
    if (!isset($_SESSION['user']['id'])) {
        return;
    }

    $stmt = $conn->prepare("SELECT status FROM users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // ❌ bị khóa hoặc không tồn tại
    if (!$user || (int)$user['status'] === 0) {
        session_destroy();
        header("Location: login.php?error=locked");
        exit();
    }
}

// =======================
// LOGOUT
// =======================

function logout()
{
    session_destroy();
    header("Location: login.php");
    exit();
}

// =======================
// HELPER UI
// =======================

function getOrderStatusBadge($status)
{
    $badges = [
        'pending'    => '<span class="badge bg-warning">Chờ xử lý</span>',
        'processing' => '<span class="badge bg-info">Đang xử lý</span>',
        'completed'  => '<span class="badge bg-success">Hoàn thành</span>',
        'cancelled'  => '<span class="badge bg-danger">Đã hủy</span>',
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">???</span>';
}

function getUserStatusBadge($status)
{
    return $status == 1
        ? '<span class="badge bg-success">Hoạt động</span>'
        : '<span class="badge bg-danger">Đã khóa</span>';
}

function getRoleBadge($role)
{
    switch ($role) {
        case 'super_admin':
            return '<span class="badge bg-dark">Super Admin</span>';
        case 'admin':
            return '<span class="badge bg-danger">Admin</span>';
        case 'staff':
            return '<span class="badge bg-primary">Staff</span>';
        default:
            return '<span class="badge bg-secondary">Customer</span>';
    }
}

// =======================
// FORMAT
// =======================

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

// =======================
// FLASH MESSAGE
// =======================

function setFlashMessage($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
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

// =======================
// RANDOM PASSWORD
// =======================

function generateRandomPassword($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';

    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return $password;
}

// =======================
// LOG ADMIN ACTION
// =======================

function logAdminAction($conn, $action, $detail = '')
{
    $user_id = $_SESSION['user']['id'] ?? 0;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $stmt = $conn->prepare("
        INSERT INTO admin_logs (admin_id, action, detail, ip_address) 
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("isss", $user_id, $action, $detail, $ip);
    $stmt->execute();
    $stmt->close();
}