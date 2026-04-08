<?php
$pageTitle = 'Add Category';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO categories (name, description, status) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $description, $status);

    if ($stmt->execute()) {
        setFlashMessage('success', 'Category added successfully!');
        header("Location: categories.php");
        exit();
    } else {
        $error = 'Failed to add category.';
    }
    $stmt->close();
}
?>

<div class="main-content">
    <div class="top-header">
        <h1>➕ Add New Category</h1>
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
                    <input type="text" name="name" required placeholder="e.g. Carbonated Drinks">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Category description..."></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="status" checked> Active
                    </label>
                </div>
                <button type="submit" class="btn btn-success">💾 Save Category</button>
                <a href="categories.php" class="btn" style="background:#6c757d;color:#fff;margin-left:10px;">Cancel</a>
            </form>
        </div>

<?php require_once 'includes/footer.php'; ?>