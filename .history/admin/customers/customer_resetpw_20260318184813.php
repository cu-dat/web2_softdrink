<?php
include "../config/database.php";

if (!isset($_GET['id'])) {
    die("Thiếu ID");
}

$id = $_GET['id'];

// mật khẩu mặc định
$default_password = "123456";

// mã hóa
$hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

// update
$sql = "UPDATE customers SET password=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hashed_password, $id);
$stmt->execute();

// quay lại danh sách
header("Location: customers_list.php");
exit();
?>