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

// Kiểm tra ngày hợp lệ
$date_error = '';
if ($mode === 'date') {
    $date_obj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
        $date_error = 'Ngày không hợp lệ!';
        $date = date('Y-m-d');
    }
}

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
    // Chế độ theo ngày: tính động từ nhập/xuất (đã fix SQL injection)
    $date_esc = $conn->real_escape_string($date);
    $sql = "
        SELECT 
            p.name,
            COALESCE((
                SELECT SUM(idt.quantity)
                FROM import_details idt
                JOIN imports i ON idt.import_id = i.id
                WHERE idt.product_id = p.id
                AND i.status = 'completed'
                AND DATE(i.created_at) <= '$date_esc'
            ), 0) AS total_import,
            COALESCE((
                SELECT SUM(od.quantity)
                FROM order_details od
                JOIN orders o ON od.order_id = o.id
                WHERE od.product_id = p.id
                AND o.status = 'completed'
                AND DATE(o.created_at) <= '$date_esc'
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

            <!-- Hiển thị lỗi ngày nếu có -->
            <?php if ($date_error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>⚠️ Lỗi!</strong> <?= $date_error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Chuyển đổi chế độ -->
            <form method="GET" class="row g-3 mb-4 align-items-end" id="stockForm">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Chế độ xem:</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="inventory" id="modeInventory"
                                <?= $mode === 'inventory' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="modeInventory">
                                📦 Tồn hiện tại
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="date" id="modeDate"
                                <?= $mode === 'date' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="modeDate">
                                📅 Tồn theo ngày
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Ô chọn ngày (chỉ hiện khi chế độ = date) -->
                <div class="col-md-3" id="datePickerDiv" style="<?= $mode === 'date' ? '' : 'display:none;' ?>">
                    <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="form-control">
                </div>

                <div class="col-md-2" id="filterButton" style="<?= $mode === 'date' ? '' : 'display:none;' ?>">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>

                <div class="col-md-2">
                    <a href="stock_check.php?mode=<?= $mode ?>" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>

            <!-- Bảng kết quả -->
            <div class="table-responsive">
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
                                        <td class="text-success"><?= number_format($row['import']) ?></td>
                                        <td class="text-danger"><?= number_format($row['export']) ?></td>
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
</div>

<script>
    // Lấy các phần tử
    const form = document.getElementById('stockForm');
    const modeInventory = document.getElementById('modeInventory');
    const modeDate = document.getElementById('modeDate');
    const datePickerDiv = document.getElementById('datePickerDiv');
    const filterButton = document.getElementById('filterButton');
    
    // Hàm hiển thị/ẩn ô ngày và nút lọc
    function toggleDateInput() {
        if (modeDate.checked) {
            datePickerDiv.style.display = 'block';
            filterButton.style.display = 'block';
        } else {
            datePickerDiv.style.display = 'none';
            filterButton.style.display = 'none';
        }
    }
    
    // Hàm submit form
    function submitForm() {
        form.submit();
    }
    
    // Sự kiện khi radio thay đổi
    modeInventory.addEventListener('change', function() {
        toggleDateInput();
        submitForm();
    });
    
    modeDate.addEventListener('change', function() {
        toggleDateInput();
        submitForm();
    });
    
    // Khởi tạo trạng thái ban đầu
    toggleDateInput();
</script>

<?php require_once '../includes/footer.php'; ?>