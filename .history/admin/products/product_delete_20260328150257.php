<?php
require_once 'includes/functions.php';
require_once 'config/database.php';
requireLogin();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {

    // 🔎 1. Kiểm tra sản phẩm đã từng nhập hàng chưa
    $check = $conn->prepare("SELECT COUNT(*) as total FROM inventory WHERE product_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result()->fetch_assoc();
    $check->close();

    if ($result['total'] > 0) {
        // ❌ ĐÃ nhập hàng → KHÔNG XOÁ, chỉ ẨN

        $stmt = $conn->prepare("UPDATE products SET status = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            setFlashMessage('success', 'Sản phẩm đã được ẩn (đã từng nhập hàng)');
        } else {
            setFlashMessage('error', 'Không thể cập nhật trạng thái');
        }

        $stmt->close();

    } else {
        // ✅ CHƯA nhập hàng → XOÁ HẲN

        // Xoá ảnh
        $product = $conn->query("SELECT image FROM products WHERE id = $id")->fetch_assoc();
        if ($product && $product['image'] && file_exists('uploads/' . $product['image'])) {
            unlink('uploads/' . $product['image']);
        }

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            setFlashMessage('success', 'Đã xoá sản phẩm hoàn toàn');
        } else {
            setFlashMessage('error', 'Xoá sản phẩm thất bại');
        }

        $stmt->close();
    }
}

header("Location: product.php"); // nhớ sửa lại đúng file
exit();
?>