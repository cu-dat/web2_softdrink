<?php
include "../config/database.php";
require_once '../includes/functions.php';
requireAdmin($conn); // Chỉ admin mới được xóa

$id = (int)$_GET['id'];

// Không cho xóa admin
$check = $conn->query("SELECT role FROM users WHERE id = $id")->fetch_assoc();
if ($check['role'] === 'admin') {
    die("Không thể xóa tài khoản admin!");
}

// Dùng prepared statement
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: customer_list.php?toast=delete&name=" . urlencode($name))
?>