<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$import_id = $_GET['id'];

// check tồn tại + status
$check = $conn->query("
    SELECT status FROM imports WHERE id = $import_id
")->fetch_assoc();

// ❌ không tồn tại hoặc không phải draft → đá về
if (!$check || $check['status'] != 'draft') {
    setFlashMessage('error', 'Phiếu đã duyệt hoặc không tồn tại!');
    header("Location: import.php");
    exit();
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = $_POST['product_id'];
    $qty = $_POST['quantity'];
    $price = $_POST['price'];

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
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5>➕ Thêm sản phẩm vào phiếu nhập #<?= $import_id ?></h5>
        </div>
        <div class="card-body">
            <!-- Form thêm sản phẩm -->
            <form method="POST" class="row g-2 mb-4">
                <div class="col-md-4">
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Chọn sản phẩm --</option>
                        <?php
                        $products = $conn->query("SELECT * FROM products");
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
                    <input type="number" name="price" class="form-control" placeholder="Giá nhập (VD: 10000)" min="0" step="100" required>
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
                                    <td><?= $item['quantity'] ?></td>
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
                <div class="alert alert-info mt-3">Chưa có sản phẩm nào trong phiếu. Hãy thêm sản phẩm.</div>
            <?php endif; ?>

            <div class="mt-3">
                <a href="import_finish.php?id=<?= $import_id ?>" class="btn btn-success">✔ Hoàn tất (lưu nháp)</a>
                <a href="import.php" class="btn btn-secondary">⬅ Quay về</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>