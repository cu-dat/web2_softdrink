<?php
require_once '../config/database.php';

$import_id = $_GET['id'];

// ❌ tránh chạy lại
$check = $conn->query("SELECT status FROM imports WHERE id=$import_id")->fetch_assoc();
if ($check['status'] === 'completed') {
    die("Phiếu đã hoàn thành!");
}

// lấy danh sách sản phẩm nhập
$items = $conn->query("
    SELECT * FROM import_details WHERE import_id = $import_id
");

while ($item = $items->fetch_assoc()) {

    $pid       = $item['product_id'];
    $qty_new   = $item['quantity'];
    $price_new = $item['import_price'];

    // lấy dữ liệu cũ
    $product = $conn->query("
        SELECT stock_quantity, cost_price, profit_margin 
        FROM products 
        WHERE id = $pid
    ")->fetch_assoc();

    $qty_old   = (int)$product['stock_quantity'];
    $cost_old  = (float)$product['cost_price'];
    $margin    = (float)$product['profit_margin'];

    // ===== 1. TÍNH GIÁ NHẬP BÌNH QUÂN =====
    if ($qty_old > 0) {
        $avg_cost = (
            ($qty_old * $cost_old) + ($qty_new * $price_new)
        ) / ($qty_old + $qty_new);
    } else {
        // lần nhập đầu
        $avg_cost = $price_new;
    }

    // ===== 2. TÍNH GIÁ BÁN =====
    $selling_price = $avg_cost * (1 + $margin / 100);

    // làm tròn
    $avg_cost = round($avg_cost);
    $selling_price = round($selling_price);

    // ===== 3. UPDATE =====
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

// update trạng thái phiếu
$conn->query("UPDATE imports SET status='completed' WHERE id=$import_id");

header("Location: import.php");
exit();