<?php
$pageTitle = 'Edit Category';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$id = intval($_GET['id'] ?? 0);
if ($id === 0) { header("Location: categories.php"); exit(); }

$category = $conn->query("SELECT * FROM categories WHERE id = $id")->fetch_assoc();
if (!$category) { header("Location: categories.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE categories SET name=?, description=?, status=? WHERE id=?");
    $stmt->bind_param("ssii", $name, $description, $status, $id);

    if ($stmt->execute()) {
        setFlashMessage('success', 'Category updated successfully!');
        header("Location: categories.php");
        exit();
    } else {
        $error = 'Failed to update category.';
    }
    $stmt->close();
}
?>

<div class="main-content">
    <div class="top-header">
        <h1>✏️ Edit Category</h1>
        <div class="admin-info">
            <a href="categories.php" class="btn btn-primary btn-sm">← Back to Categories</a>
        </div>
    </div>
    
    <div class="page-content">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Category Name *</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($category['name']); ?>">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?php echo htmlspecialchars($category['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="status" <?php echo $category['status'] ? 'checked' : ''; ?>> Active
                    </label>
                </div>
                <button type="submit" class="btn btn-success">💾 Update Category</button>
                <a href="categories.php" class="btn" style="background:#6c757d;color:#fff;margin-left:10px;">Cancel</a>
            </form>
        </div>

<?php require_once 'includes/footer.php'; ?>