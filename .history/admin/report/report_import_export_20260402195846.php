<?php
$pageTitle = 'Báo cáo nhập xuất';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

$sql = "
SELECT 
    p.name,

    COALESCE((
        SELECT SUM(idt.quantity)
        FROM import_details idt
        JOIN imports i ON idt.import_id = i.id
        WHERE idt.product_id = p.id
        " . ($from && $to ? "AND DATE(i.created_at) BETWEEN '$from' AND '$to'" : "") . "
    ), 0) AS total_import,

    COALESCE((
        SELECT SUM(od.quantity)
        FROM order_details od
        JOIN orders o ON od.order_id = o.id
        WHERE od.product_id = p.id
        " . ($from && $to ? "AND DATE(o.created_at) BETWEEN '$from' AND '$to'" : "") . "
    ), 0) AS total_export

FROM products p
ORDER BY p.name ASC
";

if ($from && $to) {
    $sql .= " AND DATE(i.created_at) BETWEEN '$from' AND '$to'";
}


$result = $conn->query($sql) or die($conn->error);
?>

<div class="container mt-4">

    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-success text-white d-flex justify-content-between">
            <h5>📊 Báo cáo nhập - xuất</h5>
        </div>

        <div class="card-body">

            <!-- FILTER -->
            <form method="GET" class="row g-2 mb-3">

                <div class="col-md-3">
                    <input type="date" name="from" value="<?= $from ?>" class="form-control">
                </div>

                <div class="col-md-3">
                    <input type="date" name="to" value="<?= $to ?>" class="form-control">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success w-100">Lọc</button>
                </div>

                <div class="col-md-2">
                    <a href="report_import_export.php" class="btn btn-secondary w-100">Reset</a>
                </div>

            </form>

            <!-- TABLE -->
            <table class="table table-bordered table-hover text-center align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Nhập</th>
                        <th>Xuất</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['name'] ?></td>
                            <td class="text-success fw-bold"><?= $row['total_import'] ?></td>
                            <td class="text-danger fw-bold"><?= $row['total_export'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>