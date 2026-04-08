<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin($conn);

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Kiểm tra đơn hàng
$order = $conn->query("SELECT status FROM orders WHERE id = $order_id")->fetch_assoc();
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

// 2. Lấy chi tiết đơn
$items = $conn->query("SELECT product_id, quantity FROM order_details WHERE order_id = $order_id");

// 3. Kiểm tra tồn kho trước khi xử lý
while ($item = $items->fetch_assoc()) {
    $pid = $item['product_id'];
    $qty = (int)$item['quantity'];

    $stockRow = $conn->query("SELECT stock FROM inventory WHERE product_id = $pid")->fetch_assoc();
    $current_stock = $stockRow['stock'] ?? 0;

    if ($current_stock < $qty) {
        setFlashMessage('error', "Không đủ tồn kho cho sản phẩm ID $pid!");
        header("Location: order.php");
        exit();
    }
}

// Reset con trỏ để dùng lại
$items->data_seek(0);

// 4. Trừ tồn kho (CHỈ MỘT LẦN, dùng hàm updateInventory để đồng bộ)
while ($item = $items->fetch_assoc()) {
    $pid = $item['product_id'];
    $qty = (int)$item['quantity'];
    updateInventory($conn, $pid, -$qty);   // giảm tồn
}

// 5. Cập nhật trạng thái đơn hàng
$conn->query("UPDATE orders SET status = 'completed' WHERE id = $order_id");

setFlashMessage('success', 'Đơn hàng đã hoàn tất!');
header("Location: order.php");
exit();