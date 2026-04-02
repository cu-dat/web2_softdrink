<?php
session_start();
require_once("../admin/config/database.php");

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(!$name || !$email || !$password){
    $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
    header("Location: ../index.php?page=register");
    exit;
}

// check email
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
if(!$stmt){
    die("SQL ERROR: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $_SESSION['error'] = "Email đã tồn tại";
    header("Location: ../index.php?page=register");
    exit;
}

// hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// insert
$stmt = $conn->prepare("
    INSERT INTO users(full_name,email,password,provider)
    VALUES(?,?,?,'local')
");

if(!$stmt){
    die("SQL ERROR: " . $conn->error);
}

$stmt->bind_param("sss", $name, $email, $hash);
$stmt->execute();

$_SESSION['success'] = "Đăng ký thành công!";
header("Location: ../index.php?page=login");
exit;