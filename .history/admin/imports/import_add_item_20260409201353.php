<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$import_id = $_GET['id'];

// check tồn tại + status
$check = $conn->query("
    SELECT status, import_date FROM imports WHERE id = $import_id
")->fetch_assoc();

// ❌ không tồn tại hoặc không phải draft → đá về
if (!$check || $check['status'] != 'draft') {
    setFlashMessage('error', 'Phiếu đã duyệt hoặc không tồn tại!');
    header("Location: import.php");
    exit();
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Hàm chuyển đổi số từ dạng có dấu chấm sang số nguyên
function convertToNumber($value) {
    // Xóa bỏ dấu chấm phân cách hàng ngàn
    $value = str_replace('.', '', $value);
    // Chuyển về số
    return (float)$value;
}

// Xử lý cập nhật ngày nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_date'])) {
    $import_date = $_POST['import_date'];
    $update_stmt = $conn->prepare("UPDATE imports SET import_date = ? WHERE id = ?");
    $update_stmt->bind_param("si", $import_date, $import_id);
    $update_stmt->execute();
    
    // Refresh lại thông tin
    header("Location: import_add_item.php?id=$import_id");
    exit();
}

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $pid = $_POST['product_id'];
    $qty = $_POST['quantity'];
    $price = convertToNumber($_POST['price']); // Chuyển đổi giá nhập

    $stmt = $conn->prepare("
        INSERT INTO import_details (import_id, product_id, quantity, import_price)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiid", $import_id, $pid, $qty, $price);
    $stmt->execute();

    // Sau khi thêm, chuyển hướng về cùng trang để refresh danh sách
    header("Location: import_add_item.php?id=$import_id");
    exit();
}

// Lấy danh sách sản phẩm đã thêm vào phiếu
$details = $conn->query("
    SELECT d.*, p.name 
    FROM import_details d
    JOIN products p ON d.product_id = p.id
    WHERE d.import_id = $import_id
");
$total_price = 0;
$details_list = [];
if ($details && $details->num_rows > 0) {
    while ($row = $details->fetch_assoc()) {
        $details_list[] = $row;
        $total_price += $row['quantity'] * $row['import_price'];
    }
}

// Lấy lại thông tin import_date mới nhất
$import_info = $conn->query("SELECT import_date FROM imports WHERE id = $import_id")->fetch_assoc();
$current_import_date = $import_info['import_date'] ?? date('Y-m-d');
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5>➕ Thêm sản phẩm vào phiếu nhập #<?= $import_id ?></h5>
        </div>
        <div class="card-body">
            <!-- Form chọn ngày nhập -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="POST" class="row g-2">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">📅 Ngày nhập hàng:</label>
                            <input type="date" name="import_date" class="form-control" 
                                   value="<?= htmlspecialchars($current_import_date) ?>" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" name="update_date" class="btn btn-info w-100">Cập nhật ngày</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info no-remove mb-0">
                        <strong>📌 Mã phiếu:</strong> #<?= $import_id ?> <br>
                        <strong>📅 Ngày nhập:</strong> <?= date('d/m/Y', strtotime($current_import_date)) ?>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Form thêm sản phẩm với JavaScript tự động định dạng -->
            <form method="POST" class="row g-2 mb-4" id="addProductForm">
                <div class="col-md-4">
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Chọn sản phẩm --</option>
                        <?php
                        $products = $conn->query("SELECT * FROM products ORDER BY name");
                        while ($p = $products->fetch_assoc()):
                        ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="quantity" class="form-control" placeholder="Số lượng" min="1" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="price" class="form-control price-input" placeholder="Giá nhập (VD: 1.000.000)" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Thêm</button>
                </div>
            </form>

            <!-- Hiển thị danh sách sản phẩm đã thêm -->
            <?php if (!empty($details_list)): ?>
                <h5 class="mt-3">📋 Danh sách sản phẩm trong phiếu</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá nhập</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details_list as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><?= number_format($item['quantity']) ?></td>
                                    <td><?= number_format($item['import_price'], 0, ',', '.') ?> ₫</td>
                                    <td><?= number_format($item['quantity'] * $item['import_price'], 0, ',', '.') ?> ₫</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <th colspan="3" class="text-end">Tổng cộng:</th>
                                <th><?= number_format($total_price, 0, ',', '.') ?> ₫</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info no-remove mt-3">Chưa có sản phẩm nào trong phiếu. Hãy thêm sản phẩm.</div>
            <?php endif; ?>

            <div class="mt-3">
                <a href="import_finish.php?id=<?= $import_id ?>" class="btn btn-success">✔ Hoàn tất (lưu nháp)</a>
                <a href="import.php" class="btn btn-secondary">⬅ Quay về</a>
            </div>
        </div>
    </div>
</div>

<script>
// Tự động định dạng số khi người dùng nhập
document.querySelectorAll('.price-input').forEach(function(input) {
    input.addEventListener('input', function(e) {
        // Lấy giá trị và loại bỏ tất cả các ký tự không phải số
        let value = this.value.replace(/[^\d]/g, '');
        
        // Nếu có giá trị
        if (value) {
            // Chuyển thành số và định dạng với dấu chấm
            let number = parseInt(value, 10);
            this.value = number.toLocaleString('vi-VN');
        }
    });
    
    // Xử lý khi form submit - chuyển định dạng về số thường
    input.closest('form').addEventListener('submit', function() {
        let priceInput = document.querySelector('.price-input');
        if (priceInput) {
            // Loại bỏ dấu chấm trước khi submit
            priceInput.value = priceInput.value.replace(/\./g, '');
        }
    });
});
</script>

<style>
/* Tùy chỉnh hiển thị cho input giá */
.price-input {
    text-align: right;
    font-weight: 500;
}
</style>

<?php require_once '../includes/footer.php'; ?>