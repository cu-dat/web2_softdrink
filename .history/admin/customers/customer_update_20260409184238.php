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

// Không cho phép sửa role của admin thành customer
$check_admin = $conn->query("SELECT role FROM users WHERE id = $id")->fetch_assoc();
if ($check_admin['role'] === 'admin' && $role !== 'admin') {
    // Nếu muốn giữ nguyên role admin
    $role = 'admin';
}

// Câu lệnh SQL cập nhật
$sql = "UPDATE users 
        SET full_name=?, email=?, phone=?, address=?, role=?, status=? 
        WHERE id=?";

$stmt = $conn->prepare($sql);

// Bind đúng số lượng parameter (7 cái)
$stmt->bind_param("sssssii", $full_name, $email, $phone, $address, $role, $status, $id);

// Thực thi
if ($stmt->execute()) {
    // Thành công
    header("Location: customer_list.php");
    exit();
} else {
    // Lỗi
    echo "Lỗi cập nhật: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>