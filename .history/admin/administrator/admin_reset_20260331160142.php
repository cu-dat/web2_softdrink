<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

requireSuperAdmin($conn);

$id = $_GET['id'] ?? 0;

// ❌ validate
if (!$id || !is_numeric($id)) {
    header("Location: admin_list.php?msg=error");
    exit();
}

// ❌ không reset chính mình
if ($id == $_SESSION['admin_id']) {
    header("Location: admin_list.php?msg=self");
    exit();
}

// password mặc định
$newPassword = "123456";
$hashed = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("
    UPDATE admin_users 
    SET password=?, must_change_password=1 
    WHERE id=?
");

$stmt->bind_param("si", $hashed, $id);

if ($stmt->execute()) {
    header("Location: admin_list.php?msg=reset");
} else {
    header("Location: admin_list.php?msg=error");
}

exit();