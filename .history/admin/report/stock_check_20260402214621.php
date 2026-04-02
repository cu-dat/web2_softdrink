<?php
$pageTitle = "Tra cứu tồn kho";
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

// Chế độ xem: 'inventory' (tồn hiện tại) hoặc 'date' (theo ngày)
$mode = $_GET['mode'] ?? 'inventory';
$date = $_GET['date'] ?? date('Y-m-d');

if ($mode === 'inventory') {
    // Lấy tồn kho hiện tại từ bảng inventory
    $sql = "
        SELECT 
            p.name,
            COALESCE(inv.stock, 0) AS stock
        FROM products p
        LEFT JOIN inventory inv ON p.id = inv.product_id
        ORDER BY p.name ASC
    ";
    $result = $conn->query($sql);
    $total_stock = 0;
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $total_stock += $row['stock'];
        $rows[] = $row;
    }
} else {
    // Chế độ theo ngày: tính động từ nhập/xuất
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
        ORDER BY p.name ASC
    ";
    $result = $conn->query($sql);
    $total_stock = 0;
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $stock = $row['total_import'] - $row['total_export'];
        $total_stock += $stock;
        $rows[] = [
            'name' => $row['name'],
            'stock' => $stock,
            'import' => $row['total_import'],
            'export' => $row['total_export']
        ];
    }
}
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h5>📦 Tra cứu tồn kho</h5>
        </div>
        <div class="card-body">

            <!-- Chuyển đổi chế độ -->
            <form method="GET" class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Chế độ xem:</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="inventory" id="modeInventory"
                                <?= $mode === 'inventory' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="modeInventory">
                                📦 Tồn hiện tại (inventory)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="date" id="modeDate"
                                <?= $mode === 'date' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="form-check-label" for="modeDate">
                                📅 Tồn theo ngày
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Ô chọn ngày (chỉ hiện khi chế độ = date) -->
                <div class="col-md-3" id="datePickerDiv" style="<?= $mode === 'date' ? '' : 'display:none;' ?>">
                    <input type="date" name="date" value="<?= $date ?>" class="form-control">
                </div>

                <div class="col-md-2" id="filterButton" style="<?= $mode === 'date' ? '' : 'display:none;' ?>">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>

                <div class="col-md-2">
                    <a href="stock_check.php?mode=<?= $mode ?>" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>

            <!-- Bảng kết quả -->
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Sản phẩm</th>
                        <?php if ($mode === 'date'): ?>
                            <th>Nhập</th>
                            <th>Xuất</th>
                        <?php endif; ?>
                        <th>Tồn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="<?= $mode === 'date' ? 4 : 2 ?>" class="text-muted">Không có sản phẩm</td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td class="text-start"><?= htmlspecialchars($row['name']) ?></td>
                                <?php if ($mode === 'date'): ?>
                                    <td><?= number_format($row['import']) ?></td>
                                    <td><?= number_format($row['export']) ?></td>
                                <?php endif; ?>
                                <td class="fw-bold <?= $row['stock'] <= 20 ? 'text-danger' : 'text-success' ?>">
                                    <?= number_format($row['stock']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td colspan="<?= $mode === 'date' ? 3 : 1 ?>" class="text-end">📦 Tổng tồn kho:</td>
                        <td class="text-primary"><?= number_format($total_stock) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    // Khi chọn radio, tự động submit form
    const radios = document.querySelectorAll('input[name="mode"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Hiển thị/ẩn ô ngày và nút lọc khi chế độ thay đổi (dùng cho trường hợp submit bằng tay)
    function toggleDateInput() {
        const mode = document.querySelector('input[name="mode"]:checked').value;
        const dateDiv = document.getElementById('datePickerDiv');
        const filterBtn = document.getElementById('filterButton');
        if (mode === 'date') {
            dateDiv.style.display = 'block';
            filterBtn.style.display = 'block';
        } else {
            dateDiv.style.display = 'none';
            filterBtn.style.display = 'none';
        }
    }
    // Gán sự kiện change cho radio (đã có onchange trong HTML, nhưng dùng thêm để an toàn)
    document.querySelectorAll('input[name="mode"]').forEach(r => r.addEventListener('change', toggleDateInput));
    toggleDateInput(); // chạy lần đầu
</script>

<?php require_once '../includes/footer.php'; ?>