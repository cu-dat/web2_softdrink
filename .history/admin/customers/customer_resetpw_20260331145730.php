<?php
include "../config/database.php";
require_once 'includes/functions.php';
requireSuperAdmin();

// kiểm tra id
if (!isset($_GET['id'])) {
    die("Thiếu ID");
}

$id = $_GET['id'];

// password mặc định
$default_password = "123456";

// mã hóa
$hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

// update DB
$sql = "UPDATE customers SET password=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hashed_password, $id);

// debug
if ($stmt->execute()) {
    echo "✅ Reset thành công!";
} else {
    echo "❌ Lỗi: " . $stmt->error;
    exit();
}

// chuyển trang
header("Location: customer_list.php");
exit();
?>