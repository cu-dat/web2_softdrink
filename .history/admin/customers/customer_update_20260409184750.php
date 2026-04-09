<?php
include "../config/database.php";
require_once '../includes/functions.php';

// Kiểm tra xem có dữ liệu POST không
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: customer_list.php");
    exit();
}

// Lấy dữ liệu từ form
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$role = $_POST['role'] ?? 'customer';
$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

// Kiểm tra id hợp lệ
if ($id <= 0) {
    header("Location: customer_list.php");
    exit();
}

// Lấy thông tin user hiện tại
$current_user = $conn->query("SELECT role FROM users WHERE id = $id")->fetch_assoc();
if (!$current_user) {
    header("Location: customer_list.php");
    exit();
}

// ⚠️ BẢO VỆ: Không cho phép đổi role Admin thành Customer
if ($current_user['role'] === 'admin' && $role !== 'admin') {
    // Chuyển hướng về edit với thông báo lỗi
    header("Location: customer_edit.php?id=$id&error=" . urlencode("Không thể đổi role Admin thành Khách hàng!"));
    exit();
}

// Nếu đang sửa chính tài khoản admin đang đăng nhập, không cho tự hạ quyền
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $id && $role !== 'admin') {
    header("Location: customer_edit.php?id=$id&error=" . urlencode("Bạn không thể tự hạ quyền của chính mình!"));
    exit();
}

// Câu lệnh SQL cập nhật
$sql = "UPDATE users 
        SET full_name=?, email=?, phone=?, address=?, role=?, status=? 
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssii", $full_name, $email, $phone, $address, $role, $status, $id);

if ($stmt->execute()) {
    header("Location: customer_list.php");
    exit();
} else {
    header("Location: customer_edit.php?id=$id&error=" . urlencode("Lỗi cập nhật: " . $stmt->error));
    exit();
}

$stmt->close();
$conn->close();
?>