<?php
include "../config/database.php";

$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$role = $_POST['role'] ?? 'customer';

// Kiểm tra dữ liệu bắt buộc
if (empty($full_name)) {
    header("Location: customer_add.php?message=" . urlencode("Vui lòng nhập họ tên!") . "&type=warning");
    exit();
}

// Kiểm tra email đã tồn tại chưa
if (!empty($email)) {
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        header("Location: customer_add.php?message=" . urlencode("Email đã tồn tại!") . "&type=danger");
        exit();
    }
}

// Mật khẩu mặc định
$default_password = "123456";
$password = password_hash($default_password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (full_name, email, phone, address, password, role)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $full_name, $email, $phone, $address, $password, $role);

if ($stmt->execute()) {
    // Thành công
    header("Location: customer_list.php?toast=add_success&name=" . urlencode($full_name));
    exit();
} else {
    // Thất bại
    header("Location: customer_add.php?message=" . urlencode("Thêm người dùng thất bại: " . $conn->error) . "&type=danger");
    exit();
}

$stmt->close();
$conn->close();
?>