<?php
$pageTitle = 'Báo cáo nhập xuất';
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$from = isset($_GET['from']) ? $_GET['from'] : '';
$to   = isset($_GET['to']) ? $_GET['to'] : '';

// Xây dựng điều kiện an toàn
$where_import = "";
$where_export = "";
$where_open_import = "";
$where_open_export = "";

if (!empty($from) && !empty($to)) {
    $from_esc = $conn->real_escape_string($from);
    $to_esc = $conn->real_escape_string($to);
    $where_import = "AND DATE(i.created_at) BETWEEN '$from_esc' AND '$to_esc'";
    $where_export = "AND DATE(o.created_at) BETWEEN '$from_esc' AND '$to_esc'";
    $where_open_import = "AND DATE(i.created_at) < '$from_esc'";
    $where_open_export = "AND DATE(o.created_at) < '$from_esc'";
} elseif (!empty($from)) {
    $from_esc = $conn->real_escape_string($from);
    $where_import = "AND DATE(i.created_at) >= '$from_esc'";
    $where_export = "AND DATE(o.created_at) >= '$from_esc'";
    $where_open_import = "AND DATE(i.created_at) < '$from_esc'";
    $where_open_export = "AND DATE(o.created_at) < '$from_esc'";
} elseif (!empty($to)) {
    $to_esc = $conn->real_escape_string($to);
    $where_import = "AND DATE(i.created_at) <= '$to_esc'";
    $where_export = "AND DATE(o.created_at) <= '$to_esc'";
    // Không có tồn đầu kỳ nếu chỉ có 'to'
    $where_open_import = "";
    $where_open_export = "";
}

$sql = "
SELECT 
    p.name,
    COALESCE((
        SELECT SUM(idt.quantity)
        FROM import_details idt
        JOIN imports i ON idt.import_id = i.id
        WHERE idt.product_id = p.id
        AND i.status = 'completed'
        $where_import
    ), 0) AS total_import,
    COALESCE((
        SELECT SUM(od.quantity)
        FROM order_details od
        JOIN orders o ON od.order_id = o.id
        WHERE od.product_id = p.id
        AND o.status = 'completed'
        $where_export
    ), 0) AS total_export,
    COALESCE((
        SELECT SUM(idt.quantity)
        FROM import_details idt
        JOIN imports i ON idt.import_id = i.id
        WHERE idt.product_id = p.id
        AND i.status = 'completed'
        $where_open_import
    ), 0) - COALESCE((
        SELECT SUM(od.quantity)
        FROM order_details od
        JOIN orders o ON od.order_id = o.id
        WHERE od.product_id = p.id
        AND o.status = 'completed'
        $where_open_export
    ), 0) AS opening_stock
FROM products p
ORDER BY p.name ASC
";

$result = $conn->query($sql) or die("Lỗi truy vấn: " . $conn->error);
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white d-flex justify-content-between">
            <h5>📊 Báo cáo nhập - xuất - tồn</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="form-control">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success w-100">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="report_import_export.php" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>

            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Tồn đầu</th>
                        <th>Nhập</th>
                        <th>Xuất</th>
                        <th>Tồn cuối</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_open = 0;
                    $total_import = 0;
                    $total_export = 0;
                    $total_close = 0;
                    while ($row = $result->fetch_assoc()):
                        $opening = (int)$row['opening_stock'];
                        $import = (int)$row['total_import'];
                        $export = (int)$row['total_export'];
                        $closing = $opening + $import - $export;

                        $total_open += $opening;
                        $total_import += $import;
                        $total_export += $export;
                        $total_close += $closing;
                    ?>
                        <tr>
                            <td class="text-start"><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= number_format($opening) ?></td>
                            <td class="text-success fw-bold"><?= number_format($import) ?></td>
                            <td class="text-danger fw-bold"><?= number_format($export) ?></td>
                            <td class="fw-bold <?= $closing < 0 ? 'text-danger' : 'text-primary' ?>">
                                <?= number_format($closing) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td class="text-end">Tổng cộng:</td>
                        <td><?= number_format($total_open) ?></td>
                        <td><?= number_format($total_import) ?></td>
                        <td><?= number_format($total_export) ?></td>
                        <td><?= number_format($total_close) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>