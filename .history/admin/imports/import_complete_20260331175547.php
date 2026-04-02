<?php
require_once '../config/database.php';

$import_id = $_GET['id'];

// tránh double click
$check = $conn->query("SELECT status FROM imports WHERE id=$import_id")->fetch_assoc();
if ($check['status'] === 'completed') {
    die("Phiếu đã hoàn thành!");
}

// lấy item
$items = $conn->query("SELECT * FROM import_details WHERE import_id=$import_id");

while ($item = $items->fetch_assoc()) {

    $pid = $item['product_id'];
    $qty_new = $item['quantity'];
    $price_new = $item['import_price'];

    $p = $conn->query("
        SELECT stock_quantity, cost_price, profit_margin 
        FROM products WHERE id=$pid
    ")->fetch_assoc();

    $qty_old = $p['stock_quantity'];
    $price_old = $p['cost_price'];

    // ===== AVG =====
    if ($qty_old + $qty_new > 0) {
        $avg = (($qty_old * $price_old) + ($qty_new * $price_new)) / ($qty_old + $qty_new);
    } else {
        $avg = $price_new;
    }

    // ===== SELL =====
    $selling = $avg * (1 + $p['profit_margin'] / 100);

    $avg = round($avg);
    $selling = round($selling);

    $stmt = $conn->prepare("
        UPDATE products 
        SET cost_price=?, price=?, stock_quantity=stock_quantity+?
        WHERE id=?
    ");
    $stmt->bind_param("iiii",$avg,$selling,$qty_new,$pid);
    $stmt->execute();
}

// update status
$conn->query("UPDATE imports SET status='completed' WHERE id=$import_id");

header("Location: import.php");