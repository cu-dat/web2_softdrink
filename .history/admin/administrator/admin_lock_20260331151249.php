<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

requireSuperAdmin();

$id = $_GET['id'];

// không xóa chính mình
if ($id == $_SESSION['admin_id']) {
    die("Không thể xóa chính bạn!");
}

$stmt = $conn->prepare("DELETE FROM admin_users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin_list.php");