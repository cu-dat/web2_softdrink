<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

requireSuperAdmin($conn);

$id = $_GET['id'] ?? 0;

// ❌ validate id
if (!$id || !is_numeric($id)) {
    setFlashMessage('danger', 'ID không hợp lệ!');
    header("Location: admin_list.php");
    exit();
}

// ❌ không khóa chính mình
if ($id == $_SESSION['admin_id']) {
    setFlashMessage('danger', 'Không thể khóa chính bạn!');
    header("Location: admin_list.php");
    exit();
}

// lấy user
$stmt = $conn->prepare("SELECT id, username, status FROM admin_users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    setFlashMessage('danger', 'Admin không tồn tại!');
    header("Location: admin_list.php");
    exit();
}

// toggle
$newStatus = $admin['status'] == 1 ? 0 : 1;

$stmt = $conn->prepare("UPDATE admin_users SET status=? WHERE id=?");
$stmt->bind_param("ii", $newStatus, $id);

if ($stmt->execute()) {
    if ($newStatus == 0) {
        header("Location: admin_list.php?msg=locked");
    } else {
        header("Location: admin_list.php?msg=unlocked");
    }
} else {
    header("Location: admin_list.php?msg=error");}

header("Location: admin_list.php?msg=locked");
exit();