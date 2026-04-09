<?php
require_once '../config/database.php';

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

if ($product_id <= 0) {
    echo '<div class="alert alert-danger">Sản phẩm không hợp lệ!</div>';
    exit();
}

// Xây dựng điều kiện thời gian
$where_condition = "";
if (!empty($from) && !empty($to)) {
    $from_esc = $conn->real_escape_string($from);
    $to_esc = $conn->real_escape_string($to);
    $where_condition = "AND DATE(o.created_at) BETWEEN '$from_esc' AND '$to_esc'";
} elseif (!empty($from)) {
    $from_esc = $conn->real_escape_string($from);
    $where_condition = "AND DATE(o.created_at) >= '$from_esc'";
} elseif (!empty($to)) {
    $to_esc = $conn->real_escape_string($to);
    $where_condition = "AND DATE(o.created_at) <= '$to_esc'";
}

// Lấy thông tin sản phẩm
$product = $conn->query("SELECT name FROM products WHERE id = $product_id")->fetch_assoc();
if (!$product) {
    echo '<div class="alert alert-danger">Không tìm thấy sản phẩm!</div>';
    exit();
}

// Lấy danh sách phiếu NHẬP chi tiết
$imports_sql = "
SELECT 
    i.id AS import_id,
    i.created_at,
    i.import_date,
    idt.quantity,
    idt.import_price,
    (idt.quantity * idt.import_price) AS total_amount
FROM import_details idt
JOIN imports i ON idt.import_id = i.id
WHERE idt.product_id = $product_id
    AND i.status = 'completed'
    $where_condition
ORDER BY i.created_at DESC
";

// Thay đổi điều kiện cho imports (dùng i.created_at)
$imports_where = str_replace('o.created_at', 'i.created_at', $where_condition);
$imports_sql = str_replace($where_condition, $imports_where, $imports_sql);

$imports = $conn->query($imports_sql);

if (!$imports) {
    echo '<div class="alert alert-danger">Lỗi truy vấn nhập: ' . $conn->error . '</div>';
    exit();
}

// Lấy danh sách phiếu XUẤT chi tiết (bỏ customer_name và customer_phone)
$exports_sql = "
SELECT 
    o.id AS order_id,
    o.created_at,
    od.quantity,
    od.price AS sell_price,
    (od.quantity * od.price) AS total_amount,
    o.cus
FROM order_details od
JOIN orders o ON od.order_id = o.id
WHERE od.product_id = $product_id
    AND o.status = 'completed'
    $where_condition
ORDER BY o.created_at DESC
";

$exports = $conn->query($exports_sql);

if (!$exports) {
    echo '<div class="alert alert-danger">Lỗi truy vấn xuất: ' . $conn->error . '</div>';
    exit();
}

// Tính tổng
$total_import_qty = 0;
$total_import_amount = 0;
$total_export_qty = 0;
$total_export_amount = 0;
?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">📥 Phiếu NHẬP - <?= htmlspecialchars($product['name']) ?></h6>
            </div>
            <div class="card-body p-0">
                <?php if ($imports && $imports->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã phiếu</th>
                                    <th>Ngày nhập</th>
                                    <th>Số lượng</th>
                                    <th>Giá nhập</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($imp = $imports->fetch_assoc()): 
                                    $total_import_qty += $imp['quantity'];
                                    $total_import_amount += $imp['total_amount'];
                                ?>
                                    <tr>
                                        <td>#<?= $imp['import_id'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($imp['import_date'] ?? $imp['created_at'])) ?></td>
                                        <td class="text-end"><?= number_format($imp['quantity']) ?></td>
                                        <td class="text-end"><?= number_format($imp['import_price'], 0, ',', '.') ?> ₫</td>
                                        <td class="text-end"><?= number_format($imp['total_amount'], 0, ',', '.') ?> ₫</td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-end">Tổng cộng:</th>
                                    <th class="text-end"><?= number_format($total_import_qty) ?></th>
                                    <th></th>
                                    <th class="text-end"><?= number_format($total_import_amount, 0, ',', '.') ?> ₫</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info m-3">Không có phiếu nhập nào trong khoảng thời gian này</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">📤 Phiếu XUẤT - <?= htmlspecialchars($product['name']) ?></h6>
            </div>
            <div class="card-body p-0">
                <?php if ($exports && $exports->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày xuất</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($exp = $exports->fetch_assoc()): 
                                    $total_export_qty += $exp['quantity'];
                                    $total_export_amount += $exp['total_amount'];
                                ?>
                                    <tr>
                                        <td>#<?= $exp['order_id'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($exp['created_at'])) ?></td>
                                        <td class="text-end"><?= number_format($exp['quantity']) ?></td>
                                        <td class="text-end"><?= number_format($exp['sell_price'], 0, ',', '.') ?> ₫</td>
                                        <td class="text-end"><?= number_format($exp['total_amount'], 0, ',', '.') ?> ₫</td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-end">Tổng cộng:</th>
                                    <th class="text-end"><?= number_format($total_export_qty) ?></th>
                                    <th></th>
                                    <th class="text-end"><?= number_format($total_export_amount, 0, ',', '.') ?> ₫</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info m-3">Không có phiếu xuất nào trong khoảng thời gian này</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Hiển thị tổng kết nhanh -->
<div class="alert alert-info">
    <strong>📊 Tổng kết <?= htmlspecialchars($product['name']) ?>:</strong><br>
    📥 Tổng nhập: <?= number_format($total_import_qty) ?> sản phẩm - <?= number_format($total_import_amount, 0, ',', '.') ?> ₫<br>
    📤 Tổng xuất: <?= number_format($total_export_qty) ?> sản phẩm - <?= number_format($total_export_amount, 0, ',', '.') ?> ₫
</div>