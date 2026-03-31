<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

$id = $_GET['id'] ?? 0;

// ===== LẤY ĐƠN =====
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

// ===== LẤY CHI TIẾT =====
$details = $conn->query("
    SELECT 
        d.quantity,
        d.price,        -- ✅ giá tại thời điểm mua
        d.subtotal,
        p.name,
        p.image
    FROM order_details d
    LEFT JOIN products p ON d.product_id = p.id
    WHERE d.order_id = $id
");

// ===== UPDATE STATUS =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newStatus = $_POST['status'];

    // ❌ không cho sửa nếu đã completed hoặc cancelled
    if (in_array($order['status'], ['completed', 'cancelled'])) {
        die("Không thể cập nhật đơn này!");
    }

    // ✅ update status
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $newStatus, $id);
    $stmt->execute();

    // ✅ nếu chuyển sang completed → trừ kho
    if ($newStatus === 'completed') {
        $conn->query("
            UPDATE products p
            JOIN order_details d ON p.id = d.product_id
            SET p.stock_quantity = p.stock_quantity - d.quantity
            WHERE d.order_id = $id
        ");
    }

    header("Location: order_detail.php?id=$id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <!-- HEADER -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <h5>📦 Đơn hàng #<?= $order['id'] ?></h5>
                <a href="orders.php" class="btn btn-light btn-sm">← Quay lại</a>
            </div>

            <div class="card-body">

                <!-- THÔNG TIN -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>Ngày đặt:</strong> <?= formatDate($order['created_at']) ?></p>
                        <p><strong>Trạng thái:</strong> <?= getOrderStatusBadge($order['status']) ?></p>
                    </div>
                </div>

                <!-- UPDATE STATUS -->
                <?php if (!in_array($order['status'], ['completed', 'cancelled'])): ?>
                    <form method="POST" class="mb-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <select name="status" class="form-select">
                                    <option value="pending">Chưa xử lý</option>
                                    <option value="confirmed">Đã xác nhận</option>
                                    <option value="completed">Đã giao</option>
                                    <option value="cancelled">Huỷ</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <button class="btn btn-success w-100">
                                    Cập nhật trạng thái
                                </button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        Đơn đã hoàn tất hoặc đã huỷ, không thể chỉnh sửa.
                    </div>
                <?php endif; ?>

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $total = 0;
                            while ($d = $details->fetch_assoc()):
                                $subtotal = $d['quantity'] * $d['price'];
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td><?= $d['name'] ?></td>
                                    <td><?= $d['quantity'] ?></td>
                                    <td><?= formatCurrency($d['price']) ?></td> <!-- lấy từ order_details -->
                                    <td><?= formatCurrency($d['subtotal']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="3">Tổng cộng</th>
                                <th class="text-danger"><?= formatCurrency($total) ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

            </div>
        </div>

    </div>

</body>

</html>
<?php require_once '../includes/footer.php'; ?>