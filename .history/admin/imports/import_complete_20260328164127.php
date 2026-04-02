<?php
require_once '../config/database.php';

$id = intval($_POST['id']);

$details = $conn->query("SELECT * FROM import_details WHERE import_id = $id");

while ($row = $details->fetch_assoc()) {

    $stmt = $conn->prepare("
    UPDATE products 
    SET stock_quantity = stock_quantity + ? 
    WHERE id = ?
    ");

    $stmt->bind_param("ii", $row['quantity'], $row['product_id']);
    $stmt->execute();
}

$conn->query("UPDATE imports SET status = 1 WHERE id = $id");

header("Location: imports.php");