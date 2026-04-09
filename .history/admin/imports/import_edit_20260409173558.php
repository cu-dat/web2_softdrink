<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra phiếu tồn tại và còn ở trạng thái draft
$check = $conn->query("SELECT status FROM imports WHERE id = $id")->fetch_assoc();
if (!$check || $check['status'] != 'draft') {
    setFlashMessage('error', 'Phiếu đã duyệt hoặc không tồn tại!');
    header("Location: import.php");
    exit();
}

function convertToNumber($value) {
    $value = str_replace('.', '', $value);
    return (float)$value;
}

// 👉 Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['quantity'] as $detail_id => $qty) {
        $price = convertToNumber($_POST['price'][$detail_id]); // Chuyển đổi giá
        $pid   = $_POST['product_id'][$detail_id];

        $stmt = $conn->prepare("
            UPDATE import_details 
            SET product_id = ?, quantity = ?, import_price = ?
            WHERE id = ?
        ");
        $stmt->bind_param("iidi", $pid, $qty, $price, $detail_id);
        $stmt->execute();
    }
    
    setFlashMessage('success', 'Cập nhật phiếu thành công!');
    header("Location: import.php");
    exit();
}

// 👉 lấy data
$items = $conn->query("
    SELECT d.id, d.product_id, d.quantity, d.import_price, p.name 
    FROM import_details d
    JOIN products p ON d.product_id = p.id
    WHERE d.import_id = $id
");

// 👉 include UI SAU CÙNG
require_once '../includes/navbar.php';
require_once '../includes/header.php';

$products = $conn->query("SELECT id, name FROM products ORDER BY name");
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning">
            <h5>✏️ Sửa phiếu nhập #<?= $id ?></h5>
        </div>

        <div class="card-body">
            <form method="POST" id="editForm">
                <?php if ($items->num_rows == 0): ?>
                    <div class="alert alert-warning no-remove d-flex justify-content-between align-items-center">
                        <span>Phiếu chưa có sản phẩm!</span>
                    </div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá nhập</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $items->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <select name="product_id[<?= $row['id'] ?>]" class="form-select" required>
                                            <?php
                                            $products->data_seek(0); // reset con trỏ
                                            while ($p = $products->fetch_assoc()):
                                            ?>
                                                <option value="<?= $p['id'] ?>"
                                                    <?= $p['id'] == $row['product_id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($p['name']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number"
                                            name="quantity[<?= $row['id'] ?>]"
                                            value="<?= $row['quantity'] ?>"
                                            class="form-control"
                                            min="1"
                                            required>
                                    </td>
                                    <td>
                                        <input type="text" 
                                            name="price[<?= $row['id'] ?>]"
                                            value="<?= number_format($row['import_price'], 0, ',', '.') ?>"
                                            class="form-control price-input"
                                            required>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button class="btn btn-success">💾 Lưu thay đổi</button>
                    <a href="import.php" class="btn btn-secondary">⬅ Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tự động định dạng số khi người dùng nhập cho tất cả các input có class price-input
document.querySelectorAll('.price-input').forEach(function(input) {
    // Xử lý khi người dùng nhập
    input.addEventListener('input', function(e) {
        // Lấy giá trị và loại bỏ tất cả các ký tự không phải số
        let value = this.value.replace(/[^\d]/g, '');
        
        // Nếu có giá trị
        if (value && value !== '') {
            // Chuyển thành số và định dạng với dấu chấm
            let number = parseInt(value, 10);
            if (!isNaN(number)) {
                this.value = number.toLocaleString('vi-VN');
            }
        }
    });
    
    // Xử lý khi input mất focus (blur) - nếu để trống thì đặt lại 0
    input.addEventListener('blur', function() {
        if (this.value === '' || this.value === '0') {
            this.value = '0';
        }
    });
});

// Xử lý khi form submit - chuyển định dạng về số thường
document.getElementById('editForm').addEventListener('submit', function() {
    document.querySelectorAll('.price-input').forEach(function(input) {
        // Loại bỏ dấu chấm trước khi submit
        input.value = input.value.replace(/\./g, '');
    });
});
</script>

<style>
/* Tùy chỉnh hiển thị cho input giá */
.price-input {
    text-align: right;
    font-weight: 500;
}

/* Tùy chỉnh input số lượng */
input[type="number"] {
    text-align: center;
}

/* Tùy chỉnh select */
.form-select {
    cursor: pointer;
}
</style>

<?php require_once '../includes/footer.php'; ?>