<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '..'

requireAdmin($conn);

// ===== FILTER =====
$from   = $_GET['from'] ?? '';
$to     = $_GET['to'] ?? '';
$status = $_GET['status'] ?? '';
$sort   = $_GET['sort'] ?? 'asc';

// ===== QUERY =====
$sql = "
SELECT o.*, u.full_name, u.address
FROM orders o
LEFT JOIN users u ON o.customer_id = u.id
WHERE 1
";

// lọc ngày
if ($from && $to) {
    $sql .= " AND DATE(o.created_at) BETWEEN '$from' AND '$to'";
}

// lọc status
if ($status) {
    $sql .= " AND o.status = '$status'";
}

// sort
if ($sort === 'address') {
    $sql .= " ORDER BY u.address ASC";
} else {
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

        <!-- HEADER -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5>📦 Quản lý đơn hàng</h5>
            </div>

            <div class="card-body">

                <!-- FILTER -->
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
                            <option value="desc">Mới nhất</option>
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

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center">

                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Khách</th>
                                <th>Địa chỉ</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>

                                        <td><?= htmlspecialchars($row['full_name']) ?></td>

                                        <td class="text-start">
                                            <?= htmlspecialchars($row['address']) ?>
                                        </td>

                                        <td class="text-danger fw-bold">
                                            <?= formatCurrency($row['total_amount']) ?>
                                        </td>

                                        <td>
                                            <?= getOrderStatusBadge($row['status']) ?>
                                        </td>

                                        <td>
                                            <?= formatDate($row['created_at']) ?>
                                        </td>

                                        <td>
                                            <a href="order_detail.php?id=<?= $row['id'] ?>"
                                                class="btn btn-sm btn-info">
                                                👁 Xem
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-muted">Không có đơn hàng</td>
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