<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin($conn);

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ===== 1. CHECK ORDER =====
$order = $conn->query("
    SELECT status FROM orders WHERE id = $order_id
")->fetch_assoc();

if (!$order) {
    setFlashMessage('error', 'Đơn hàng không tồn tại!');
    header("Location: order.php");
    exit();
}

if ($order['status'] != 'draft') {
    setFlashMessage('error', 'Đơn đã hoàn tất!');
    header("Location: order.php");
    exit();
}

// ===== 2. LẤY CHI TIẾT ĐƠN =====
$items = $conn->query("
    SELECT product_id, quantity 
    FROM order_details 
    WHERE order_id = $order_id
");

// ===== 3. CHECK TỒN KHO =====
while ($item = $items->fetch_assoc()) {

    $pid = $item['product_id'];
    $qty = (int)$item['quantity'];

    $stockRow = $conn->query("
        SELECT stock FROM inventory WHERE product_id = $pid
    ")->fetch_assoc();

    $current_stock = $stockRow['stock'] ?? 0;

    if ($current_stock < $qty) {
        setFlashMessage('error', 'Không đủ tồn kho để bán!');
        header("Location: order.php");
        exit();
    }
}

// ===== RESET con trỏ =====
$items->data_seek(0);

// ===== 4. TRỪ TỒN =====
while ($item = $items->fetch_assoc()) {

    $pid = $item['product_id'];
    $qty = (int)$item['quantity'];

    $stmt = $conn->prepare("
        UPDATE inventory 
        SET stock = stock - ?
        WHERE product_id = ?
    ");

    $stmt->bind_param("ii", $qty, $pid);
    $stmt->execute();
}

// ===== 5. UPDATE STATUS =====
$conn->query("
    UPDATE orders 
    SET status = 'completed' 
    WHERE id = $order_id
");

// ===== 6. DONE =====
setFlashMessage('success', 'Đơn hàng đã hoàn tất!');
header("Location: order.php");
exit(); 