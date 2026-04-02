<?php
session_start();
require_once("../admin/config/database.php");

$email = $_POST['email'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if(!$email || !$new_password){
    $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
    header("Location: ../index.php?page=forgot_password");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    $_SESSION['error'] = "Email không tồn tại";
    header("Location: ../index.php?page=forgot_password");
    exit;
}

// hash password
$hash = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
$stmt->bind_param("ss", $hash, $email);
$stmt->execute();

$_SESSION['success'] = "Đổi mật khẩu thành công!";

header("Location: ../index.php?page=login");
exit;