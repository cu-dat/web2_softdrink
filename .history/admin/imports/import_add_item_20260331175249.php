<?php
require_once '../config/database.php';

$import_id = $_POST['import_id'];
$product_id = $_POST['product_id'];
$qty = $_POST['quantity'];
$price = $_POST['price'];

$stmt = $conn->prepare("
INSERT INTO import_details (import_id, product_id, quantity, import_price)
VALUES (?, ?, ?, ?)
");

$stmt->bind_param("iiid", $import_id, $product_id, $qty, $price);
$stmt->execute();

header("Location: import_edit.php?id=" . $import_id);
