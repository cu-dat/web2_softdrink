<?php
$pageTitle = 'Edit Product';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$id = intval($_GET['id'] ?? 0);
if ($id === 0) { header("Location: products.php"); exit(); }

$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
if (!$product) { header("Location: products.php"); exit(); }

$categories = $conn->query("SELECT * FROM categories WHERE status = 1 ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock_quantity']);
    $description = sanitize($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0;
    $image = $product['image'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            // Delete old image
            if ($image && file_exists('uploads/' . $image)) unlink('uploads/' . $image);
            $image = 'product_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
        }
    }

    $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock_quantity=?, image=?, status=? WHERE id=?");
    $stmt->bind_param("issdisii", $category_id, $name, $description, $price, $stock, $image, $status, $id);

    if ($stmt->execute()) {
        setFlashMessage('success', 'Product updated successfully!');
        header("Location: products.php");
        exit();
    } else {
        $error = 'Failed to update product.';
    }
    $stmt->close();
}
?>

<div class="main-content">
    <div class="top-header">
        <h1>✏️ Edit Product</h1>
        <div class="admin-info">
            <a href="products.php" class="btn btn-primary btn-sm">← Back to Products</a>
        </div>
    </div>
    
    <div class="page-content">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">-- Select Category --</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Price ($) *</label>
                    <input type="number" name="price" step="0.01" min="0" required value="<?php echo $product['price']; ?>">
                </div>
                
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock_quantity" min="0" required value="<?php echo $product['stock_quantity']; ?>">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Product Image</label>
                    <?php if ($product['image']): ?>
                        <div style="margin-bottom:10px;">
                            <img src="uploads/<?php echo $product['image']; ?>" style="width:100px;border-radius:8px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*">
                    <small style="color:#888;">Leave empty to keep current image</small>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="status" <?php echo $product['status'] ? 'checked' : ''; ?>> Active
                    </label>
                </div>
                
                <button type="submit" class="btn btn-success">💾 Update Product</button>
                <a href="products.php" class="btn btn-secondary" style="background:#6c757d;color:#fff;margin-left:10px;">Cancel</a>
            </form>
        </div>

<?php require_once '../includes/footer.php'; ?>