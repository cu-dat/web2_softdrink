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

    $pid = $item['product_id'];
    $qty = (int)$item['quantity'];

    // ✅ UPDATE INVENTORY (chuẩn mới)
    $stmt = $conn->prepare("
        INSERT INTO inventory (product_id, quantity)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ");

    $stmt->bind_param("ii", $pid, $qty);
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
