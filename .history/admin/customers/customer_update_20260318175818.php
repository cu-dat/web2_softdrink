<?php
include "../config/database.php";

$id        = $_POST['id'];
$full_name = $_POST['full_name'];
$email     = $_POST['email'];
$role      = $_POST['role'];

$sql = "UPDATE customers 
        SET full_name=?, email=?, role=? 
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $full_name, $email, $role, $id);

$stmt->execute();

header("Location: list.php");
?>