<?php
session_start();
require_once("../admin/config/database.php");

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(!$name || !$email || !$password){
    header("Location: ../index.php?page=register&error=empty");
    exit;
}

// check email
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();

if($stmt->get_result()->num_rows > 0){
    header("Location: ../index.php?page=register&error=exist");
    exit;
}

// hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// insert
$stmt = $conn->prepare("
    INSERT INTO customers(full_name,email,password,provider)
    VALUES(?,?,?,'local')
");

$stmt->bind_param("sss", $name, $email, $hash);
$stmt->execute();

header("Location: ../index.php?page=login&success=1");
exit;