<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ===== LẤY THÔNG TIN ĐƠN HÀNG =====
$stmt = $conn->prepare("
    SELECT o.*, u.full_name, u.address
    FROM orders o
    LEFT JOIN users u ON o.customer_id = u.id
    WHERE o.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Đơn hàng không tồn tại!");
}

// ===== LẤY CHI TIẾT SẢN PHẨM (LẤY GIÁ HIỆN TẠI TỪ PRODUCTS) =====
$detailStmt = $conn->prepare("
    SELECT 
        d.quantity,
        p.price AS price,   -- ✅ lấy giá hiện tại từ bảng products
        p.name,
        p.image
    FROM order_details d
    LEFT JOIN products p ON d.product_id = p.id
    WHERE d.order_id = ?
");
$detailStmt->bind_param("i", $id);
$detailStmt->execute();
$details = $detailStmt->get_result();

// ===== XỬ LÝ CẬP NHẬT TRẠNG THÁI =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];
    $currentStatus = $order['status'];

    if (in_array($currentStatus, ['completed', 'cancelled'])) {
        die("Không thể cập nhật đơn hàng đã hoàn tất hoặc đã huỷ!");
    }

    $allowedStatus = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($newStatus, $allowedStatus)) {
        die("Trạng thái không hợp lệ!");
    }

    $conn->begin_transaction();

    try {
        $updateStmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $updateStmt->bind_param("si", $newStatus, $id);
        $updateStmt->execute();

        if ($newStatus === 'completed' && $currentStatus !== 'completed') {
            // Kiểm tra tồn kho
            $checkStmt = $conn->prepare("
                SELECT inv.product_id, inv.stock, d.quantity
                FROM order_details d
                JOIN inventory inv ON d.product_id = inv.product_id
                WHERE d.order_id = ?
            ");
            $checkStmt->bind_param("i", $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $insufficient = [];
            while ($row = $checkResult->fetch_assoc()) {
                if ($row['stock'] < $row['quantity']) {
                    $insufficient[] = $row['product_id'];
                }
            }
            if (!empty($insufficient)) {
                throw new Exception("Một số sản phẩm không đủ tồn kho để hoàn thành đơn hàng!");
            }

            $updateStockStmt = $conn->prepare("
                UPDATE inventory inv
                JOIN order_details d ON inv.product_id = d.product_id
                SET inv.stock = inv.stock - d.quantity
                WHERE d.order_id = ?
            ");
            $updateStockStmt->bind_param("i", $id);
            $updateStockStmt->execute();
        }

        $conn->commit();
        header("Location: order_detail.php?id=$id");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Lỗi: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $order['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📦 Đơn hàng #<?= $order['id'] ?></h5>
            <a href="orders.php" class="btn btn-light btn-sm">← Quay lại</a>
        </div>

        <div class="card-body">
            <!-- Thông tin chung -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>👤 Khách hàng:</strong> <?= htmlspecialchars($order['full_name'] ?? 'Khách lẻ') ?></p>
                    <p><strong>📍 Địa chỉ:</strong> <?= htmlspecialchars($order['address'] ?? 'Chưa cập nhật') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>📅 Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                    <p><strong>🔄 Trạng thái:</strong>
                        <?php
                        $statusClass = [
                            'pending' => 'warning',
                            'confirmed' => 'info',
                            'completed' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $statusText = [
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Đã xác nhận',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã huỷ'
                        ];
                        ?>
                        <span class="badge bg-<?= $statusClass[$order['status']] ?>">
                            <?= $statusText[$order['status']] ?>
                        </span>
                    </p>
                </div>
            </div>

            <!-- Form cập nhật trạng thái -->
            <?php if (!in_array($order['status'], ['completed', 'cancelled'])): ?>
                <form method="POST" class="mb-4 p-3 border rounded bg-white">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cập nhật trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                <option value="confirmed" <?= $order['status'] == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Đã giao hàng</option>
                                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Huỷ đơn</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success w-100">Cập nhật</button>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-secondary">⚠️ Đơn hàng đã hoàn tất hoặc bị huỷ, không thể chỉnh sửa trạng thái.</div>
            <?php endif; ?>

            <!-- Bảng chi tiết sản phẩm với giá hiện tại -->
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th style="width: 100px">Số lượng</th>
                            <th style="width: 150px">Đơn giá (hiện tại)</th>
                            <th style="width: 180px">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        while ($item = $details->fetch_assoc()):
                            // Sử dụng giá hiện tại từ products
                            $price = $item['price'] ?? 0;
                            $subtotal = $item['quantity'] * $price;
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($price, 0, ',', '.') ?> ₫</td>
                                <td><?= number_format($subtotal, 0, ',', '.') ?> ₫</td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($details->num_rows == 0): ?>
                            <tr><td colspan="4" class="text-muted">Không có sản phẩm nào trong đơn hàng.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Tổng cộng:</th>
                            <th class="text-danger fs-5"><?= number_format($total, 0, ',', '.') ?> ₫</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>