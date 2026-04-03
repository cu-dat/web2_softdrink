<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

// ===== FILTER =====
$from   = $_GET['from'] ?? '';
$to     = $_GET['to'] ?? '';
$status = $_GET['status'] ?? '';
$sort   = $_GET['sort'] ?? 'desc'; // mặc định mới nhất (created_at DESC)

// ===== QUERY TÍNH TỔNG TIỀN ĐỘNG TỪ GIÁ HIỆN TẠI =====
$sql = "
SELECT 
    o.*, 
    u.full_name, 
    u.address,
    COALESCE(SUM(d.quantity * p.price), 0) AS dynamic_total
FROM orders o
LEFT JOIN users u ON o.customer_id = u.id
LEFT JOIN order_details d ON o.id = d.order_id
LEFT JOIN products p ON d.product_id = p.id
WHERE 1
";

// lọc theo ngày (dựa trên o.created_at)
if ($from && $to) {
    $sql .= " AND DATE(o.created_at) BETWEEN '$from' AND '$to'";
}

// lọc theo status
if ($status) {
    $sql .= " AND o.status = '$status'";
}

$sql .= " GROUP BY o.id";

// sắp xếp
if ($sort === 'address') {
    $sql .= " ORDER BY u.address ASC";
} else {
    // mặc định: mới nhất trước (DESC)
    $sql .= " ORDER BY o.created_at ASC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5>📦 Quản lý đơn hàng (theo giá hiện tại)</h5>
        </div>

        <div class="card-body">
            <!-- FILTER FORM -->
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-2">
                    <input type="date" name="from" class="form-control" value="<?= $from ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to" class="form-control" value="<?= $to ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">-- Trạng thái --</option>
                        <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Chưa xử lý</option>
                        <option value="confirmed" <?= $status == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                        <option value="completed" <?= $status == 'completed' ? 'selected' : '' ?>>Đã giao</option>
                        <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>Đã huỷ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="desc" <?= $sort == 'desc' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="address" <?= $sort == 'address' ? 'selected' : '' ?>>Theo địa chỉ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="orders.php" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>

            <!-- Bảng danh sách đơn hàng -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Khách</th>
                            <th>Địa chỉ</th>
                            <th>Tổng tiền (theo giá hiện tại)</th>
                            <th>Trạng thái</th>
                            <th>Ngày</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['full_name'] ?? 'Khách lẻ') ?></td>
                                    <td class="text-start"><?= htmlspecialchars($row['address'] ?? 'Chưa cập nhật') ?></td>
                                    <td class="text-danger fw-bold">
                                        <?= number_format($row['dynamic_total'], 0, ',', '.') ?> ₫
                                    </td>
                                    <td><?= getOrderStatusBadge($row['status']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <a href="order_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">👁 Xem</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-muted">Không có đơn hàng nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php require_once '../includes/footer.php'; ?>