<?php
session_start();
require_once("../admin/config/database.php");

// ===== LẤY DATA =====
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// ===== VALIDATE =====
if ($name === '' || $email === '' || $password === '') {
    $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
    $_SESSION['old'] = $_POST;
    header("Location: ../index.php?page=register");
    exit;
}

// ===== EMAIL FORMAT =====
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Email không hợp lệ";
    $_SESSION['old'] = $_POST;
    header("Location: ../index.php?page=register");
    exit;
}

// ===== CHECK EMAIL =====
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = "Email đã tồn tại";
    $_SESSION['old'] = $_POST;
    header("Location: ../index.php?page=register");
    exit;
}

// ===== HASH PASSWORD =====
$hash = password_hash($password, PASSWORD_DEFAULT);

// ===== INSERT =====
$stmt = $conn->prepare("
    INSERT INTO users(full_name, email, password, provider)
    VALUES(?, ?, ?, 'local')
");

$stmt->bind_param("sss", $name, $email, $hash);

if ($stmt->execute()) {
    $_SESSION['success'] = "Đăng ký thành công!";
    header("Location: ../index.php?page=login");
} else {
    $_SESSION['error'] = "Lỗi hệ thống!";
    header("Location: ../index.php?page=register");
}

exit;