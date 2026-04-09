<?php
$pageTitle = 'Add Product';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$categories = $conn->query("SELECT * FROM categories WHERE status = 1 ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $description = sanitize($_POST['description']); // chưa dùng
    $status = isset($_POST['status']) ? 1 : 0;
    $image = '';
    $sku = sanitize($_POST['sku']);
    $unit = sanitize($_POST['unit']);
    $profit_margin = floatval($_POST['profit_margin']);

    // Xử lý upload ảnh (giữ nguyên)
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    if (!is_dir('uploads')) mkdir('uploads', 0777, true);
    
    // Kiểm tra MIME type thực tế của file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);
    
    // Chỉ chấp nhận các MIME type là hình ảnh
    if (strpos($mime_type, 'image/') === 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $image = 'product_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
    } else {
        echo "Chỉ chấp nhận file hình ảnh!";
    }
}

    $stmt = $conn->prepare("
        INSERT INTO products 
        (sku, name, category_id, unit, profit_margin, image, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssisdsi", $sku, $name, $category_id, $unit, $profit_margin, $image, $status);

    if ($stmt->execute()) {
        $product_id = $conn->insert_id;  // ← ĐÃ SỬA
        $conn->query("INSERT INTO inventory (product_id, stock) VALUES ($product_id, 0)");
        setFlashMessage('Thành công', 'Sản phẩm đã được thêm thành công!');
        header("Location: product.php");
        exit();
    } else {
        $error = 'Không thể thêm sản phẩm được!';
    }
    $stmt->close();
}
?>

<div class="container mt-4">

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <div class="card shadow">

        <!-- Header -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">➕ Thêm sản phẩm</h5>
            <a href="product.php" class="btn btn-light btn-sm">← Quay lại</a>
        </div>

        <!-- Body -->
        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="row">

                    <!-- LEFT -->
                    <div class="col-md-6">

                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm *</label>
                            <input type="text" name="name" class="form-control" required placeholder="Coca-Cola 330ml">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Danh mục *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat['id']; ?>">
                                        <?= htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mã sản phẩm (SKU)</label>
                            <input type="text" name="sku" class="form-control" placeholder="VD: SP001">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Đơn vị tính</label>
                            <input type="text" name="unit" class="form-control" placeholder="Chai / Lon / Thùng">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tỉ lệ lợi nhuận (%)</label>
                            <input type="number" name="profit_margin" step="0.01" min="0" class="form-control" placeholder="20">
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="col-md-6">

                        <div class="mb-3">
                            <label class="form-label">Ảnh sản phẩm</label>
                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="5"></textarea>
                        </div>

                        <div class="form-check mt-3">
                            <input type="checkbox" name="status" class="form-check-input" checked>
                            <label class="form-check-label">Hiển thị (Active)</label>
                        </div>
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="mt-4 d-flex justify-content-between">
                    <a href="product.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-success">💾 Lưu sản phẩm</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>