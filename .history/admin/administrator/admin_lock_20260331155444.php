<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

requireSuperAdmin($conn);

$id = $_GET['id'] ?? 0;

// ❌ validate id
if (!$id || !is_numeric($id)) {
    header("Location: admin_list.php?msg=error");
    exit();
}

// ❌ không khóa chính mình
if ($id == $_SESSION['admin_id']) {
    header("Location: admin_list.php?msg=self");
    exit();
}

// lấy user
$stmt = $conn->prepare("SELECT id, username, status FROM admin_users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    header("Location: admin_list.php?msg=notfound");
    exit();
}

// toggle status
$newStatus = ($admin['status'] == 1) ? 0 : 1;

$stmt = $conn->prepare("UPDATE admin_users SET status=? WHERE id=?");
$stmt->bind_param("ii", $newStatus, $id);

// ✅ xử lý kết quả
if ($stmt->execute()) {

    if ($newStatus == 0) {
        header("Location: admin_list.php?msg=locked");
    } else {
        header("Location: admin_list.php?msg=unlocked");
    }

} else {
    header("Location: admin_list.php?msg=error");
}

exit();