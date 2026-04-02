<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
requireAdmin($conn);

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {

    // 🔎 Kiểm tra sản phẩm đã từng bán chưa
    $check = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM order_details 
        WHERE product_id = ?
    ");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result()->fetch_assoc();
    $check->close();

    if ($result['total'] > 0) {
        // ❌ ĐÃ bán → chỉ ẨN

        $stmt = $conn->prepare("UPDATE products SET status = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            setFlashMessage('success', 'Sản phẩm đã được ẩn (đã từng bán)');
        } else {
            setFlashMessage('error', 'Không thể cập nhật trạng thái');
        }

        $stmt->close();
    } else {
        // ✅ CHƯA bán → xoá hẳn

        // Xoá ảnh
        $product = $conn->query("SELECT image FROM products WHERE id = $id")->fetch_assoc();
        if ($product && $product['image'] && file_exists('uploads/' . $product['image'])) {
            unlink('uploads/' . $product['image']);
        }
        // Xóa inventory trước (do khóa ngoại nếu có)
        $conn->query("DELETE FROM inventory WHERE product_id = $id");
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

header("Location: product.php");
exit();
