<?php
$pageTitle = "Tra cứu tồn kho";
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

$date = $_GET['date'] ?? date('Y-m-d');

$sql = "
SELECT 
    p.name,
    COALESCE((
        SELECT SUM(idt.quantity)
        FROM import_details idt
        JOIN imports i ON idt.import_id = i.id
        WHERE idt.product_id = p.id
        AND i.status = 'completed'
        AND DATE(i.created_at) <= '$date'
    ), 0) AS total_import,
    COALESCE((
        SELECT SUM(od.quantity)
        FROM order_details od
        JOIN orders o ON od.order_id = o.id
        WHERE od.product_id = p.id
        AND o.status = 'completed'
        AND DATE(o.created_at) <= '$date'
    ), 0) AS total_export
FROM products p
";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h5>📦 Tra cứu tồn kho</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="date" name="date" value="<?= $date ?>" class="form-control">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="stock_check.php" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>

            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Nhập</th>
                        <th>Xuất</th>
                        <th>Tồn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_stock = 0;
                    while ($row = $result->fetch_assoc()):
                        $total_import = $row['total_import'];
                        $total_export = $row['total_export'];
                        $stock = $total_import - $total_export;
                        $total_stock += $stock;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= number_format($total_import) ?></td>
                            <td><?= number_format($total_export) ?></td>
                            <td class="fw-bold <?= $stock <= 20 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($stock) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">📦 Tổng tồn kho:</td>
                        <td class="text-primary"><?= number_format($total_stock) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>