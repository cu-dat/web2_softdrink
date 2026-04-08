<?php
include "../config/database.php";

if (!isset($_GET['id'])) {
    die("Thiếu ID");
}

$id = $_GET['id'];

$default_password = "123456";
$hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

$sql = "UPDATE customers SET password=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hashed_password, $id);

if ($stmt->execute()) {
    echo "Reset thành công";
} else {
    echo "Lỗi: " . $stmt->error;
}

header("Location: customer_list.php");
exit();
?>