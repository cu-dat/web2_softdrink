<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== KIỂM TRA ĐĂNG NHẬP =====
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin($conn)
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }

    checkUserActive($conn);
}

// ===== CHECK ROLE =====
function isAdmin()
{
    return isset($_SESSION['user_role']) 
        && $_SESSION['user_role'] === 'admin';
}

// ===== REQUIRE ADMIN =====
function requireAdmin($conn)
{
    requireLogin($conn);

    if (!isAdmin()) {
        header("Location: login.php?msg=forbidden");
        exit();
    }
}

// ===== CHECK ACCOUNT ACTIVE =====
function checkUserActive($conn)
{
    if (!isset($_SESSION['user_id'])) {
        return;
    }

    $stmt = $conn->prepare("SELECT status FROM customers WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // ❌ nếu bị khóa hoặc không tồn tại
    if (!$user || (int)$user['status'] === 0) {
        session_destroy();
        header("Location: login.php?error=locked");
        exit();
    }
}

// ===== HELPER =====
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
        ? '<span class="badge bg-success">Hoạt động</span>'
        : '<span class="badge bg-danger">Đã khóa</span>';
}

function getRoleBadge($role)
{
    return $role === 'admin'
        ? '<span class="badge bg-danger">Admin</span>'
        : '<span class="badge bg-secondary">Customer</span>';
}

// ===== FLASH MESSAGE =====
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

// ===== RANDOM PASSWORD =====
function generateRandomPassword($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

// ===== LOG ACTION =====
function logAdminAction($conn, $action, $detail = '')
{
    $user_id = $_SESSION['user_id'] ?? 0;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $stmt = $conn->prepare("
        INSERT INTO admin_logs (admin_id, action, detail, ip_address) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $user_id, $action, $detail, $ip);
    $stmt->execute();
    $stmt->close();
}

