<?php
$pageTitle = 'Edit Product';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$id = intval($_GET['id'] ?? 0);
if ($id === 0) {
    header("Location: products.php");
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
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock_quantity']);
    $description = sanitize($_POST['description']);
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    $image = $product['image'];
    $sku = sanitize($_POST['sku']);
    $unit = sanitize($_POST['unit']);
    $profit_margin = floatval($_POST['profit_margin']);

    // Xóa ảnh nếu checkbox được chọn
    if (isset($_POST['remove_image'])) {
        if ($image && file_exists('uploads/' . $image)) {
            unlink('uploads/' . $image);
        }
        $image = '';
    }
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);

            // Xóa ảnh cũ nếu có
            if ($image && file_exists('uploads/' . $image)) {
                unlink('uploads/' . $image);
            }

            $image = 'product_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
        }
    }

    $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock_quantity=?, image=?, status=? WHERE id=?");
    $stmt->bind_param("issdisii", $category_id, $name, $description, $price, $stock, $image, $status, $id);

    if ($stmt->execute()) {
        setFlashMessage('success', 'Product updated successfully!');
        header("Location: product.php");
        exit();
    } else {
        $error = 'Failed to update product.';
    }
    $stmt->close();
}
?>

<div class="container mt-4">

    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">✏️ Sửa sản phẩm</h5>
            <a href="product.php" class="btn btn-light btn-sm">← Quay lại</a>
        </div>

        <!-- BODY -->
        <div class="card-body">

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="row">

                    <!-- LEFT -->
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
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat['id']; ?>"
                                        <?= $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá *</label>
                            <input type="number" name="price" step="0.01" min="0"
                                class="form-control" value="<?= $product['price']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số lượng *</label>
                            <input type="number" name="stock_quantity"
                                class="form-control" value="<?= $product['stock_quantity']; ?>" required>
                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="col-md-6">

                        <div class="mb-3">
                            <label class="form-label">Ảnh sản phẩm</label>

                            <?php if ($product['image']): ?>
                                <div class="mb-2">
                                    <img src="uploads/<?= $product['image']; ?>"
                                        class="img-thumbnail"
                                        style="width:100px;">
                                </div>

                                <div class="form-check mb-2">
                                    <input type="checkbox" name="remove_image" class="form-check-input">
                                    <label class="form-check-label">Xóa ảnh hiện tại</label>
                                </div>
                            <?php endif; ?>

                            <input type="file" name="image" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="1"
                                    <?= $product['status'] == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label">Đang bán</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" value="0"
                                    <?= $product['status'] == 0 ? 'checked' : '' ?>>
                                <label class="form-check-label">Ngừng bán</label>
                            </div>
                        </div>
                        <input type="text" name="sku" class="form-control"
                            value="<?= htmlspecialchars($product['sku']); ?>">

                        <input type="text" name="unit" class="form-control"
                            value="<?= htmlspecialchars($product['unit']); ?>">

                        <input type="number" name="profit_margin" class="form-control"
                            value="<?= $product['profit_margin']; ?>">

                    </div>

                </div>

                <!-- BUTTON -->
                <div class="mt-4 d-flex justify-content-between">
                    <a href="product.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>