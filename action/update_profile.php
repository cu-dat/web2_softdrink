<?php
session_start();
require_once("../admin/config/database.php");

if(!isset($_SESSION['user'])){
    header("Location: ../index.php?page=login");
    exit;
}

$id = $_SESSION['user']['id'];

$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

$stmt = $conn->prepare("
    UPDATE users
    SET full_name=?, phone=?, address=? 
    WHERE id=?
");

$stmt->bind_param("sssi", $name, $phone, $address, $id);
$stmt->execute();

// update lại session
$_SESSION['user']['full_name'] = $name;

header("Location: ../index.php?page=profile&success=1");
exit;