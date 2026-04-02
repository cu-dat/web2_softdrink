<?php
require_once '../config/database.php';

$date = $_GET['date'] ?? date('Y-m-d');

$sql = "
SELECT 
    p.id,
    p.name,

    -- tổng nhập
    COALESCE((
        SELECT SUM(d.quantity)
        FROM import_details d
        JOIN imports i ON d.import_id = i.id
        WHERE d.product_id = p.id
        AND i.status = 'completed'
        AND i.created_at <= '$date 23:59:59'
    ),0) AS total_import,

    -- tổng xuất
    COALESCE((
        SELECT SUM(od.quantity)
        FROM order_details od
        JOIN orders o ON od.order_id = o.id
        WHERE od.product_id = p.id
        AND o.status = 'completed'
        AND o.created_at <= '$date 23:59:59'
    ),0) AS total_export

FROM products p
";

$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5>📦 Tra cứu tồn kho</h5>
        </div>

        <div class="card-body">

            <form method="GET" class="mb-3">
                <input type="date" name="date" value="<?= $date ?>" class="form-control w-25 d-inline">
                <button class="btn btn-primary">Xem</button>
            </form>

            <table class="table table-bordered text-center">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Nhập</th>
                    <th>Xuất</th>
                    <th>Tồn</th>
                </tr>

                <?php while($row = $result->fetch_assoc()): 
                    $stock = $row['total_import'] - $row['total_export'];
                ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['total_import'] ?></td>
                    <td><?= $row['total_export'] ?></td>
                    <td class="fw-bold"><?= $stock ?></td>
                </tr>
                <?php endwhile; ?>
            </table>

        </div>
    </div>
</div>