<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

requireSuperAdmin($conn);

$id = $_GET['id'] ?? 0;

// ❌ validate ID
if (!$id || !is_numeric($id)) {
    header("Location: admin_list.php?msg=error");
    exit();
}

// 🔍 lấy thông tin admin
$stmt = $conn->prepare("SELECT id, role FROM admin_users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// ❌ không tồn tại
if (!$admin) {
    header("Location: admin_list.php?msg=notfound");
    exit();
}

// ❌ không reset chính mình
if ($id == $_SESSION['admin_id']) {
    header("Location: admin_list.php?msg=self");
    exit();
}

// ❌ không cho reset super admin
if ($admin['role'] === 'super_admin') {
    header("Location: admin_list.php?msg=forbidden");
    exit();
}

// 🔐 tạo password mới (có thể random nếu muốn)
$newPassword = "123456";
$hashed = password_hash($newPassword, PASSWORD_BCRYPT);

// 🔁 update DB
$stmt = $conn->prepare("
    UPDATE admin_users 
    SET password=?, must_change_password=1 
    WHERE id=?
");

$stmt->bind_param("si", $hashed, $id);

// ✅ xử lý kết quả
if ($stmt->execute()) {
    header("Location: admin_list.php?msg=reset");
} else {
    header("Location: admin_list.php?msg=error");
}

exit();