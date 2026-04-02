<?php
$pageTitle = 'Categories';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$categories = $conn->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.created_at DESC
");
?>

<div class="main-content">
    <div class="top-header">
        <h1>📁 Categories</h1>
        <div class="admin-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>
    
    <div class="page-content">
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <div class="table-header">
                <h2>All Categories (<?php echo $categories->num_rows; ?>)</h2>
                <a href="category_add.php" class="btn btn-primary">+ Add Category</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $categories->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['description'] ?? '-'); ?></td>
                        <td><span class="badge badge-info"><?php echo $row['product_count']; ?></span></td>
                        <td>
                            <?php echo $row['status'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?>
                        </td>
                        <td>
                            <a href="category_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <button onclick="confirmDelete('category_delete.php?id=<?php echo $row['id']; ?>')" class="btn btn-danger btn-sm">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

<?php require_once 'includes/footer.php'; ?>