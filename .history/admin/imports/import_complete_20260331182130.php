<?php
require_once '../config/database.php';

$import_id = $_GET['id'] ?? 0;

// ===== CHECK PHIẾU =====
$check = $conn->query("SELECT status FROM imports WHERE id = $import_id")->fetch_assoc();

if (!$check) {
    die("Phiếu không tồn tại!");
}

if ($check['status'] === 'completed') {
    die("Phiếu đã hoàn thành!");
}

// ===== HÀM TÍNH AVG COST =====
function calculateAvgCost($qty_old, $cost_old, $qty_new, $cost_new)
{
    if ($qty_old <= 0) {
        return $cost_new;
    }

    $avg = (
        ($qty_old * $cost_old) + ($qty_new * $cost_new)
    ) / ($qty_old + $qty_new);

    return round($avg);
}

// ===== LẤY DANH SÁCH NHẬP =====
$items = $conn->query("
    SELECT * FROM import_details 
    WHERE import_id = $import_id
");

while ($item = $items->fetch_assoc()) {

    $pid       = $item['product_id'];
    $qty_new   = (int)$item['quantity'];
    $cost_new  = (float)$item['import_price'];

    // ===== LẤY SẢN PHẨM =====
    $product = $conn->query("
        SELECT stock_quantity, cost_price, profit_margin 
        FROM products 
        WHERE id = $pid
    ")->fetch_assoc();

    if (!$product) continue;

    $qty_old  = (int)$product['stock_quantity'];
    $cost_old = (float)$product['cost_price'];
    $margin   = (float)$product['profit_margin'];

    // ===== 1. TÍNH GIÁ NHẬP BÌNH QUÂN =====
    $avg_cost = calculateAvgCost($qty_old, $cost_old, $qty_new, $cost_new);

    // ===== 2. TÍNH GIÁ BÁN =====
    $selling_price = round($avg_cost * (1 + $margin / 100));

    // ===== 3. UPDATE PRODUCT =====
    $stmt = $conn->prepare("
        UPDATE products 
        SET 
            cost_price = ?, 
            price = ?, 
            stock_quantity = stock_quantity + ?
        WHERE id = ?
    ");

    $stmt->bind_param("iiii", $avg_cost, $selling_price, $qty_new, $pid);
    $stmt->execute();
}

// ===== UPDATE TRẠNG THÁI PHIẾU =====
$conn->query("
    UPDATE imports 
    SET status = 'completed' 
    WHERE id = $import_id
");

header("Location: import.php");
exit();