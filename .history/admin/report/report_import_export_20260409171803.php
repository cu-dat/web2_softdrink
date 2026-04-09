<?php
$pageTitle = 'Báo cáo nhập xuất';
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$from = isset($_GET['from']) ? $_GET['from'] : '';
$to   = isset($_GET['to']) ? $_GET['to'] : '';
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

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
    $where_open_import = "";
    $where_open_export = "";
}

// Truy vấn tổng hợp
$sql = "
SELECT 
    p.id AS product_id,
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
                    <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="form-control" placeholder="Từ ngày">
                </div>
                <div class="col-md-3">
                    <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="form-control" placeholder="Đến ngày">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success w-100">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="report_import_export.php" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>

            <!-- Bảng tổng hợp -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover text-center align-middle" id="summaryTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Tồn đầu</th>
                            <th>Nhập</th>
                            <th>Xuất</th>
                            <th>Tồn cuối</th>
                            <th>Chi tiết</th>
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
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="showDetail(<?= $row['product_id'] ?>, '<?= htmlspecialchars($row['name']) ?>')">
                                        📋 Xem chi tiết
                                    </button>
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
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Modal hiển thị chi tiết -->
            <div class="modal fade" id="detailModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="modalTitle">Chi tiết phiếu nhập/xuất</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="modalBody">
                            <div class="text-center">Đang tải...</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showDetail(productId, productName) {
    // Lấy giá trị lọc từ form
    const fromDate = document.querySelector('input[name="from"]').value;
    const toDate = document.querySelector('input[name="to"]').value;
    
    // Hiển thị modal
    document.getElementById('modalTitle').innerHTML = `Chi tiết: ${productName}`;
    document.getElementById('modalBody').innerHTML = '<div class="text-center">Đang tải...</div>';
    
    // Tải dữ liệu chi tiết
    fetch(`report_detail_ajax.php?product_id=${productId}&from=${fromDate}&to=${toDate}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('modalBody').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('modalBody').innerHTML = '<div class="alert alert-danger">Lỗi tải dữ liệu!</div>';
        });
    
    // Hiển thị modal (cần Bootstrap JS)
    var myModal = new bootstrap.Modal(document.getElementById('detailModal'));
    myModal.show();
}
</script>

<?php require_once '../includes/footer.php'; ?>