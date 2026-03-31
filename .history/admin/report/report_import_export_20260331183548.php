<?php
require_once '../config/database.php';

$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

$sql = "
SELECT 
    p.name,

    COALESCE(SUM(d.quantity),0) AS total_import,
    COALESCE(SUM(od.quantity),0) AS total_export

FROM products p

LEFT JOIN import_details d ON p.id = d.product_id
LEFT JOIN imports i ON d.import_id = i.id

LEFT JOIN order_details od ON p.id = od.product_id
LEFT JOIN orders o ON od.order_id = o.id

WHERE 1
";

if ($from && $to) {
    $sql .= " 
    AND i.created_at BETWEEN '$from' AND '$to'
    AND o.created_at BETWEEN '$from' AND '$to'
    ";
}

$sql .= " GROUP BY p.id";

$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5>📊 Báo cáo nhập - xuất</h5>
        </div>

        <div class="card-body">

            <form method="GET" class="row mb-3">
                <div class="col-md-3">
                    <input type="date" name="from" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="date" name="to" class="form-control">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary">Lọc</button>
                </div>
            </form>

            <table class="table table-bordered text-center">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Nhập</th>
                    <th>Xuất</th>
                </tr>

                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['total_import'] ?></td>
                    <td><?= $row['total_export'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>

        </div>
    </div>
</div>