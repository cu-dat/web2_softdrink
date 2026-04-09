<?php
$pageTitle = 'Edit Product';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$id = intval($_GET['id'] ?? 0);
if ($id === 0) {
    header("Location: product.php");
    exit();
}

$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
if (!$product) {
    header("Location: product.php");
    exit();
}

$categories = $conn->query("SELECT * FROM categories WHERE status = 1 ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $description = sanitize($_POST['description']);
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    $image = $product['image'];
    $sku = sanitize($_POST['sku']);
    $unit = sanitize($_POST['unit']);
    $profit_margin = floatval($_POST['profit_margin']);
    
    // Lấy giá nhập hiện tại (không thay đổi khi sửa sản phẩm)
    $cost_price = (float)$product['cost_price'];
    
    // TÍNH LẠI GIÁ BÁN DỰA TRÊN LỢI NHUẬN MỚI
    $price = $cost_price + ($cost_price * $profit_margin / 100);

    // Xóa ảnh nếu checkbox được chọn
    if (isset($_POST['remove_image'])) {
        if ($image && file_exists('uploads/' . $image)) {
            unlink('uploads/' . $image);
        }
        $image = '';
    }
    
    // Xử lý upload ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);
        
        if (strpos($mime_type, 'image/') === 0) {
            if ($image && file_exists('uploads/' . $image)) {
                unlink('uploads/' . $image);
            }
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $image = 'product_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
        }
    }

    $stmt = $conn->prepare("
        UPDATE products 
        SET sku=?, category_id=?, name=?, description=?, price=?, unit=?, profit_margin=?, image=?, status=? 
        WHERE id=?
    ");
    $stmt->bind_param(
        "sissdsssii",
        $sku,
        $category_id,
        $name,
        $description,
        $price,
        $unit,
        $profit_margin,
        $image,
        $status,
        $id
    );
    
    if ($stmt->execute()) {
        setFlashMessage('success', 'Cập nhật sản phẩm thành công! Giá bán đã được tính lại theo lợi nhuận mới.');
        header("Location: product.php");
        exit();
    } else {
        $error = 'Không thể cập nhật sản phẩm.';
    }
    $stmt->close();
}

// Lấy tồn kho hiện tại
$stockQuery = $conn->query("SELECT COALESCE(stock, 0) as stock FROM inventory WHERE product_id = $id");
$stockRow = $stockQuery->fetch_assoc();
$currentStock = $stockRow ? (int)$stockRow['stock'] : 0;

// Tính giá bán hiện tại theo công thức (để hiển thị)
$current_cost = (float)$product['cost_price'];
$current_margin = (float)$product['profit_margin'];
$calculated_price = $current_cost + ($current_cost * $current_margin / 100);
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">✏️ Sửa sản phẩm</h5>
            <a href="product.php" class="btn btn-light btn-sm">← Quay lại</a>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Cột trái -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm *</label>
                            <input type="text" name="name" class="form-control"
                                value="<?= htmlspecialchars($product['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danh mục *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php 
                                $categories = $conn->query("SELECT * FROM categories WHERE status = 1 ORDER BY name");
                                while ($cat = $categories->fetch_assoc()): 
                                ?>
                                    <option value="<?= $cat['id']; ?>"
                                        <?= $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mã sản phẩm (SKU)</label>
                            <input type="text" name="sku" class="form-control"
                                value="<?= htmlspecialchars($product['sku'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đơn vị tính</label>
                            <select name="unit" class="form-select">
                                <option value="Chai" <?= $product['unit'] == 'Chai' ? 'selected' : '' ?>>Chai</option>
                                <option value="Lon" <?= $product['unit'] == 'Lon' ? 'selected' : '' ?>>Lon</option>
                                <option value="Hộp" <?= $product['unit'] == 'Hộp' ? 'selected' : '' ?>>Hộp</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tỉ lệ lợi nhuận (%) *</label>
                            <div class="input-group">
                                <input type="number" name="profit_margin" step="0.01" min="0" class="form-control"
                                    value="<?= $product['profit_margin'] ?? 0; ?>" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">
                                ⚡ Thay đổi % lợi nhuận sẽ tự động tính lại giá bán
                            </small>
                        </div>
                    </div>

                    <!-- Cột phải -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ảnh sản phẩm</label>
                            <?php if ($product['image']): ?>
                                <div class="mb-2">
                                    <img src="uploads/<?= $product['image']; ?>" class="img-thumbnail" style="width:100px;">
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="remove_image" class="form-check-input" id="remove_image">
                                    <label class="form-check-label" for="remove_image">Xóa ảnh hiện tại</label>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="1" id="status_active"
                                    <?= ($product['status'] == 1 && $currentStock > 0) ? 'checked' : '' ?>
                                    <?= ($currentStock == 0) ? 'disabled' : '' ?>>
                                <label class="form-check-label <?= ($currentStock == 0) ? 'text-muted' : '' ?>" for="status_active">
                                    Đang bán
                                </label>
                                <?php if ($currentStock == 0): ?>
                                    <small class="text-danger d-block">(Hết hàng, không thể bật trạng thái Đang bán)</small>
                                <?php endif; ?>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="0" id="status_inactive"
                                    <?= ($product['status'] == 0 || $currentStock == 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_inactive">
                                    Ngừng bán
                                </label>
                            </div>
                        </div>
                        
                        <!-- Hiển thị thông tin giá -->
                        <div class="alert alert-info">
                            <strong>📊 Thông tin giá (chỉ đọc):</strong><br>
                            🔹 Giá nhập (cost): <strong><?= formatCurrency($product['cost_price']); ?></strong><br>
                            🔹 Lợi nhuận: <strong><?= number_format($product['profit_margin'], 2); ?>%</strong><br>
                            🔹 Giá bán hiện tại: <strong class="text-success"><?= formatCurrency($product['price']); ?></strong><br>
                            🔹 Giá bán theo công thức: <strong class="text-primary"><?= formatCurrency($calculated_price); ?></strong><br>
                            <hr class="my-2">
                            🔸 Tồn kho: <strong><?= number_format($currentStock); ?></strong>
                        </div>
                        
                        <?php if (abs($product['price'] - $calculated_price) > 1): ?>
                            <div class="alert alert-warning">
                                ⚠️ <strong>Lưu ý:</strong> Giá bán hiện tại (<?= formatCurrency($product['price']); ?>) 
                                không khớp với công thức (<?= formatCurrency($calculated_price); ?>). 
                                Lưu sản phẩm sẽ tự động cập nhật giá bán đúng theo công thức.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="product.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preview khi thay đổi % lợi nhuận (tùy chọn)
document.querySelector('input[name="profit_margin"]').addEventListener('input', function() {
    let margin = parseFloat(this.value) || 0;
    let cost = <?= $current_cost ?>;
    let newPrice = cost + (cost * margin / 100);
    // Có thể hiển thị preview nếu muốn
    console.log('Giá bán mới sẽ là: ' + newPrice.toLocaleString('vi-VN') + ' ₫');
});
</script>

<?php require_once '../includes/footer.php'; ?>