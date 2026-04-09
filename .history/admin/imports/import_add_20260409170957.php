<?php
require_once '../config/database.php';

// Lấy ngày nhập từ form hoặc dùng ngày hiện tại
$import_date = isset($_POST['import_date']) ? $_POST['import_date'] : date('Y-m-d');

$stmt = $conn->prepare("INSERT INTO imports (status, import_date) VALUES ('draft', ?)");
$stmt->bind_param("s", $import_date);
$stmt->execute();
$id = $conn->insert_id;

header("Location: import_add_item.php?id=$id");
exit();
?>