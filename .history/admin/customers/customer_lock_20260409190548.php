<?php
include "../config/database.php";

$id = $_GET['id'];

$sql = "UPDATE users
        SET status = IF(status=1,0,1) 
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

// Thêm kiểm tra trước khi khóa
$check = $conn->query("SELECT role FROM users WHERE id = $id")->fetch_assoc();
if ($check['role'] === 'admin') {
    die("Không thể khóa tài khoản admin!");
}
header("Location: customer_list.php?toast=$action&name=" . urlencode($name));
?>