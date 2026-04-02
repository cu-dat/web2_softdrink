<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

requireSuperAdmin($conn);

$id = $_GET['id'];

$newPassword = "123456";
$hashed = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("
    UPDATE admin_users 
    SET password=?, must_change_password=1 
    WHERE id=?
");

$stmt->bind_param("si", $hashed, $id);
$stmt->execute();

header("Location: admin_list.php");