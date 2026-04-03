<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin($conn);

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$new_status = isset($_GET['new_status']) ? $_GET['new_status'] : 'completed'; // mặc định là completed

// Kiểm tra new_status hợp lệ
if (!in_array($new_status, ['confirmed', 'completed'])) {
    setFlashMessage('error', 'Trạng thái không hợp lệ!');
    header("Location: order.php");
    exit();
}

// Lấy thông tin đơn hàng
$order = $conn->query("SELECT status FROM orders WHERE id = $order_id")->fetch_assoc();
if (!$order) {
    setFlashMessage('error', 'Đơn hàng không tồn tại!');
    header("Location: order.php");
    exit();
}

$current_status = $order['status'];

// Không cho xử lý nếu đơn đã hoàn thành hoặc hủy
if (in_array($current_status, ['completed', 'cancelled'])) {
    setFlashMessage('error', 'Đơn hàng đã hoàn tất hoặc đã hủy, không thể thay đổi!');
    header("Location: order.php");
    exit();
}

// Nếu đơn đang pending và muốn chuyển sang confirmed/completed => cần trừ kho
if ($current_status === 'pending' && in_array($new_status, ['confirmed', 'completed'])) {
    // Lấy chi tiết đơn
    $items = $conn->query("SELECT product_id, quantity FROM order_details WHERE order_id = $order_id");
    if (!$items || $items->num_rows == 0) {
        setFlashMessage('error', 'Đơn hàng không có sản phẩm!');
        header("Location: order.php");
        exit();
    }

    // Kiểm tra tồn kho
    $insufficient = [];
    while ($item = $items->fetch_assoc()) {
        $pid = $item['product_id'];
        $qty = (int)$item['quantity'];
        $stockRow = $conn->query("SELECT stock FROM inventory WHERE product_id = $pid")->fetch_assoc();
        $current_stock = $stockRow['stock'] ?? 0;
        if ($current_stock < $qty) {
            $insufficient[] = "ID $pid (cần $qty, còn $current_stock)";
        }
    }
    if (!empty($insufficient)) {
        setFlashMessage('error', 'Không đủ tồn kho: ' . implode(', ', $insufficient));
        header("Location: order_detail.php?id=$order_id");
        exit();
    }

    // Bắt đầu transaction
    $conn->begin_transaction();
    try {
        // Trừ kho
        $items->data_seek(0);
        while ($item = $items->fetch_assoc()) {
            $pid = $item['product_id'];
            $qty = (int)$item['quantity'];
            updateInventory($conn, $pid, -$qty);
        }
        // Cập nhật trạng thái
        $conn->query("UPDATE orders SET status = '$new_status' WHERE id = $order_id");
        $conn->commit();
        setFlashMessage('success', "Đã chuyển trạng thái sang " . ($new_status == 'confirmed' ? 'Đã xác nhận' : 'Hoàn thành') . " và cập nhật tồn kho.");
    } catch (Exception $e) {
        $conn->rollback();
        setFlashMessage('error', 'Lỗi: ' . $e->getMessage());
    }
} 
// Nếu đơn đang confirmed và muốn lên completed (không cần trừ kho)
elseif ($current_status === 'confirmed' && $new_status === 'completed') {
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = $order_id");
    setFlashMessage('success', 'Đã cập nhật trạng thái đơn hàng thành Hoàn thành.');
}
// Các trường hợp khác (ví dụ từ pending lên confirmed/completed đã xử lý, còn lại không hợp lệ)
else {
    setFlashMessage('error', 'Không thể chuyển trạng thái theo yêu cầu.');
}

header("Location: order_detail.php?id=$order_id");
exit();