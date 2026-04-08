<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

requireAdmin($conn);

// ===== FILTER =====
$from   = $_GET['from'] ?? '';
$to     = $_GET['to'] ?? '';
$status = $_GET['status'] ?? '';
$sort   = $_GET['sort'] ?? 'desc';

// ===== QUERY =====
$sql = "
SELECT o.*, u.full_name, u.address
FROM orders o
LEFT JOIN users u ON o.customer_id = u.id
WHERE 1
";

// lọc theo ngày
if ($from && $to) {
    $sql .= " AND DATE(o.created_at) BETWEEN '$from' AND '$to'";
}

// lọc theo trạng thái
if ($status) {
    $sql .= " AND o.status = '$status'";
}

// sắp xếp theo địa chỉ
if ($sort === 'address') {
    $sql .= " ORDER BY u.address ASC";
} else {
    $sql .= " ORDER BY o.created_at DESC";
}

$result = $conn->query($sql);
?>
<form method="GET" class="row mb-3">

    <div class="col-md-3">
        <input type="date" name="from" class="form-control" value="<?= $from ?>">
    </div>

    <div class="col-md-3">
        <input type="date" name="to" class="form-control" value="<?= $to ?>">
    </div>

    <div class="col-md-3">
        <select name="status" class="form-control">
            <option value="">-- Trạng thái --</option>
            <option value="pending">Chưa xử lý</option>
            <option value="confirmed">Đã xác nhận</option>
            <option value="completed">Đã giao</option>
            <option value="cancelled">Đã huỷ</option>
        </select>
    </div>

    <div class="col-md-3">
        <select name="sort" class="form-control">
            <option value="desc">Mới nhất</option>
            <option value="address">Theo địa chỉ</option>
        </select>
    </div>

    <div class="col-md-12 mt-2">
        <button class="btn btn-primary">Lọc</button>
    </div>

</form>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Khách</th>
            <th>Địa chỉ</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Ngày</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['full_name'] ?></td>
                <td><?= $row['address'] ?></td>
                <td><?= formatCurrency($row['total_amount']) ?></td>
                <td><?= getOrderStatusBadge($row['status']) ?></td>
                <td><?= formatDate($row['created_at']) ?></td>
                <td>
                    <a href="order_detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">
                        Xem
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>