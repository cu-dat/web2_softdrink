<?php
session_start();
require_once("../admin/config/database.php");

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(!$email || !$password){
    header("Location: ../index.php?page=login&error=empty");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header("Location: ../index.php?page=login&error=notfound");
    exit;
}

$user = $result->fetch_assoc();

// login google
if($user['provider'] == 'google'){
    header("Location: ../index.php?page=login&error=google");
    exit;
}

// check password
if(!password_verify($password, $user['password'])){
    header("Location: ../index.php?page=login&error=wrong");
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