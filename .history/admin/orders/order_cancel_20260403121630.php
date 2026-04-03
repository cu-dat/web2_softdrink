<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

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
    header("Location: order.php");
    exit();
}

// Lấy chi tiết đơn
$items = $conn->query("SELECT product_id, quantity FROM order_details WHERE order_id = $order_id");
if (!$items || $items->num_rows == 0) {
    setFlashMessage('error', 'Đơn hàng không có sản phẩm!');
    header("Location: order.php");
    exit();
}

$conn->begin_transaction();
try {
    // Nếu đơn đã được xác nhận hoặc hoàn thành (đã trừ kho) thì cộng lại
    if ($current_status === 'confirmed' || $current_status === 'completed') {
        while ($item = $items->fetch_assoc()) {
            $pid = $item['product_id'];
            $qty = (int)$item['quantity'];
            
            // TRỰC TIẾP CẬP NHẬT inventory (cộng số lượng)
            // Kiểm tra xem có bản ghi inventory chưa
            $check = $conn->query("SELECT id FROM inventory WHERE product_id = $pid");
            if ($check && $check->num_rows > 0) {
                $conn->query("UPDATE inventory SET stock = stock + $qty WHERE product_id = $pid");
            } else {
                // Nếu chưa có thì thêm mới với stock = $qty
                $conn->query("INSERT INTO inventory (product_id, stock) VALUES ($pid, $qty)");
            }
        }
    }
    // Nếu đơn đang pending (chờ xử lý) thì không cần thao tác kho

    // Cập nhật trạng thái thành cancelled
    $conn->query("UPDATE orders SET status = 'cancelled' WHERE id = $order_id");
    $conn->commit();

    $msg = ($current_status === 'pending') 
        ? 'Đã hủy đơn hàng (chưa trừ kho).' 
        : 'Đã hủy đơn hàng và hoàn lại tồn kho.';
    setFlashMessage('success', $msg);
} catch (Exception $e) {
    $conn->rollback();
    setFlashMessage('error', 'Lỗi khi hủy đơn: ' . $e->getMessage());
}
header("Location: order.php");
exit();