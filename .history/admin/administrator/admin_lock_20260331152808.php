<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

requireSuperAdmin();

$id = $_GET['id'] ?? 0;

// không khóa chính mình
if ($id == $_SESSION['admin_id']) {
    die("Không thể khóa chính bạn!");
}

// toggle status
$stmt = $conn->prepare("
    UPDATE admin_users 
    SET status = IF(status=1, 0, 1)
    WHERE id=?
");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin_list.php");
exit;
?>