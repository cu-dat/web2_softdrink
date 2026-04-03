<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireAdmin($conn);

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin đơn
$order = $conn->query("SELECT status FROM orders WHERE id = $order_id")->fetch_assoc();
if (!$order) {
    setFlashMessage('error', 'Đơn hàng không tồn tại!');
    header("Location: order.php");
    exit();
}

$current_status = $order['status'];
if ($current_status === 'cancelled') {
    setFlashMessage('error', 'Đơn hàng đã bị hủy trước đó!');
    header("Location: orders.php");
    exit();
}

// Lấy chi tiết đơn
$items = $conn->query("SELECT product_id, quantity FROM order_details WHERE order_id = $order_id");

$conn->begin_transaction();
try {
    // Nếu đơn đã được xác nhận (đã trừ kho) thì phải cộng lại
    if ($current_status === 'confirmed' || $current_status === 'completed') {
        while ($item = $items->fetch_assoc()) {
            $pid = $item['product_id'];
            $qty = (int)$item['quantity'];
            updateInventory($conn, $pid, +$qty);  // cộng lại
        }
    }
    // Nếu đơn đang draft thì không cần thao tác kho

    // Cập nhật trạng thái thành cancelled
    $conn->query("UPDATE orders SET status = 'cancelled' WHERE id = $order_id");
    $conn->commit();
    setFlashMessage('success', 'Đã hủy đơn hàng' . ($current_status !== 'draft' ? ' và hoàn lại tồn kho.' : '.'));
} catch (Exception $e) {
    $conn->rollback();
    setFlashMessage('error', 'Lỗi khi hủy đơn: ' . $e->getMessage());
}
header("Location: order.php");
exit();
?>