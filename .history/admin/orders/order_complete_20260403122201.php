<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin($conn);

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$new_status = isset($_GET['new_status']) ? $_GET['new_status'] : 'completed';

if (!in_array($new_status, ['confirmed', 'completed'])) {
    setFlashMessage('error', 'Trạng thái không hợp lệ!');
    header("Location: order.php");
    exit();
}

$order = $conn->query("SELECT status FROM orders WHERE id = $order_id")->fetch_assoc();
if (!$order) {
    setFlashMessage('error', 'Đơn hàng không tồn tại!');
    header("Location: order.php");
    exit();
}

$current_status = $order['status'];

if (in_array($current_status, ['completed', 'cancelled'])) {
    setFlashMessage('error', 'Đơn hàng đã hoàn tất hoặc đã hủy, không thể thay đổi!');
    header("Location: order_detail.php?id=$order_id");
    exit();
}

if ($current_status === 'pending' && in_array($new_status, ['confirmed', 'completed'])) {
    $items = $conn->query("SELECT product_id, quantity FROM order_details WHERE order_id = $order_id");
    if (!$items || $items->num_rows == 0) {
        setFlashMessage('error', 'Đơn hàng không có sản phẩm!');
        header("Location: order_detail.php?id=$order_id");
        exit();
    }

    // Kiểm tra tồn kho
    $insufficient = [];
    $itemsArray = [];
    while ($item = $items->fetch_assoc()) {
        $itemsArray[] = $item;
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

    $conn->begin_transaction();
    try {
        foreach ($itemsArray as $item) {
            $pid = $item['product_id'];
            $qty = (int)$item['quantity'];
            // Trực tiếp cập nhật inventory (trừ kho)
            $conn->query("UPDATE inventory SET stock = stock - $qty WHERE product_id = $pid");
            if ($conn->affected_rows == 0) {
                // Nếu chưa có bản ghi inventory (dù đã kiểm tra stock ở trên, phòng trường hợp lỗi)
                throw new Exception("Không tìm thấy inventory cho sản phẩm $pid");
            }
        }
        $conn->query("UPDATE orders SET status = '$new_status' WHERE id = $order_id");
        $conn->commit();
        setFlashMessage('success', "Đã chuyển trạng thái sang " . ($new_status == 'confirmed' ? 'Đã xác nhận' : 'Hoàn thành') . " và cập nhật tồn kho.");
    } catch (Exception $e) {
        $conn->rollback();
        setFlashMessage('error', 'Lỗi: ' . $e->getMessage());
    }
} elseif ($current_status === 'confirmed' && $new_status === 'completed') {
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = $order_id");
    setFlashMessage('success', 'Đã cập nhật trạng thái đơn hàng thành Hoàn thành.');
} else {
    setFlashMessage('error', 'Không thể chuyển trạng thái theo yêu cầu.');
}

header("Location: order_detail.php?id=$order_id");
exit();