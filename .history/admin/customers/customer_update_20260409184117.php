<?php
include "../config/database.php";

$id        = $_POST['id'];
$full_name = $_POST['full_name'];
$email     = $_POST['email'];
$role      = $_POST['role'];

$sql = "UPDATE users 
        SET full_name=?, email=?, role=? 
        WHERE id=?";

$stmt = $conn->prepare($sql);
// Sửa lại
$sql = "UPDATE users 
        SET full_name=?, email=?, phone=?, address=?, role=?, status=? 
        WHERE id=?";

$stmt->execute();

header("Location: customer_list.php");
?>