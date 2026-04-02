<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =======================
// AUTH
// =======================

function isLoggedIn()
{
    return isset($_SESSION['user']);
}

function currentUser()
{
    return $_SESSION['user'] ?? null;
}

function requireLogin($conn)
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }

    checkUserActive($conn);
}

// =======================
// ROLE (CHỈ ADMIN)
// =======================

function isAdmin()
{
    return isset($_SESSION['user']['role']) &&
        $_SESSION['user']['role'] === 'admin';
}

// =======================
// REQUIRE ADMIN
// =======================

function requireAdmin($conn)
{
    requireLogin($conn);

    if (!isAdmin()) {
        header("Location: login.php?msg=forbidden");
        exit();
    }
}

// =======================
// CHECK ACTIVE
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
// UI HELPER
// =======================

function getRoleBadge($role)
{
    return $role === 'admin'
        ? '<span class="badge bg-danger">Admin</span>'
        : '<span class="badge bg-secondary">Customer</span>';
}

function getUserStatusBadge($status)
{
    return $status == 1
        ? '<span class="badge bg-success">Hoạt động</span>'
        : '<span class="badge bg-danger">Đã khóa</span>';
}

function getOrderStatusBadge($status)
{
    $badges = [
        'pending'   => '<span class="badge bg-warning">Chờ xử lý</span>',
        'confirmed' => '<span class="badge bg-primary">Đã xác nhận</span>',
        'completed' => '<span class="badge bg-success">Hoàn thành</span>',
        'cancelled' => '<span class="badge bg-danger">Đã huỷ</span>',
    ];

    return $badges[$status] ?? '<span class="badge bg-secondary">???</span>';
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
function getLatestImportPrice($conn, $product_id)
{
  $stmt = $conn->prepare("
    SELECT idt.import_price 
    FROM import_details idt
    JOIN imports i ON idt.import_id = i.id
    WHERE idt.product_id = ?
    AND i.status = 'completed'
    ORDER BY i.created_at DESC
    LIMIT 1
");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    return $result ? $result['import_price'] : 0;
}
function calculateSellingPrice($import_price, $margin)
{
    return round($import_price * (1 + $margin / 100));
}
