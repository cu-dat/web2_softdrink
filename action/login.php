<?php
session_start();
require_once("../admin/config/database.php");

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(!$email || !$password){
    $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
    header("Location: ../index.php?page=login");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    $_SESSION['error'] = "Email không tồn tại";
    header("Location: ../index.php?page=login");
    exit;
}

$user = $result->fetch_assoc();

// ❌ CHECK STATUS (THÊM MỚI)
if(isset($user['status']) && $user['status'] == 0){
    $_SESSION['error'] = "Tài khoản đã bị khóa, không thể đăng nhập";
    header("Location: ../index.php?page=login");
    exit;
}

// login google
if($user['provider'] == 'google'){
    $_SESSION['error'] = "Tài khoản này đăng nhập bằng Google";
    header("Location: ../index.php?page=login");
    exit;
}

// check password
if(!password_verify($password, $user['password'])){
    $_SESSION['error'] = "Sai mật khẩu";
    header("Location: ../index.php?page=login");
    exit;
}

// ✅ LOGIN OK
session_regenerate_id(true);

$_SESSION['user'] = [
    'id' => $user['id'],
    'full_name' => $user['full_name'],
    'email' => $user['email']
];

header("Location: ../index.php");
exit;