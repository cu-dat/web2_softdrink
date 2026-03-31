<?php
include "../config/database.php";

$full_name = $_POST['full_name'];
$email     = $_POST['email'];
$phone     = $_POST['phone'];
$address   = $_POST['address'];
$role      = $_POST['role'];

// mật khẩu mặc định
$default_password = "123456";

// mã hóa
$password = password_hash($default_password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (full_name, email, phone, address, password, role)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $full_name, $email, $phone, $address, $password, $role);

$stmt->execute();

header("Location: customer_list.php");
?>