<?php
$pageTitle = 'Add Product';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

$categories = $conn->query("SELECT * FROM categories WHERE status = 1 ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock_quantity']);
    $description = sanitize($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0;
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);
            $image = 'product_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, stock_quantity, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdisd", $category_id, $name, $description, $price, $stock, $image, $status);

    if ($stmt->execute()) {
        setFlashMessage('success', 'Product added successfully!');
        header("Location: products.php");
        exit();
    } else {
        $error = 'Failed to add product.';
    }
    $stmt->close();
}
?>

<div class="main-content">
    <div class="top-header">
        <h1>➕ Add New Product</h1>
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
                    <input type="text" name="name" required placeholder="e.g. Coca-Cola 330ml">
                </div>
                
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">-- Select Category --</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Price ($) *</label>
                    <input type="number" name="price" step="0.01" min="0" required placeholder="0.00">
                </div>
                
                <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" name="stock_quantity" min="0" required placeholder="0">
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Product description..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="status" checked> Active
                    </label>
                </div>
                
                <button type="submit" class="btn btn-success">💾 Save Product</button>
                <a href="products.php" class="btn btn-secondary" style="background:#6c757d;color:#fff;margin-left:10px;">Cancel</a>
            </form>
        </div>

<?php require_once 'includes/footer.php'; ?>