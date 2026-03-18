<?php
$pageTitle = 'Products';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$products = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC
");
?>

<div class="main-content">
    <div class="top-header">
        <h1>🍹 Products</h1>
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
                <h2>All Products (<?php echo $products->num_rows; ?>)</h2>
                <a href="product_add.php" class="btn btn-primary">+ Add Product</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if ($row['image']): ?>
                                <img src="uploads/<?php echo $row['image']; ?>" class="product-img" alt="">
                            <?php else: ?>
                                <span style="font-size:30px;">🥤</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name'] ?? 'No Category'); ?></td>
                        <td><?php echo formatCurrency($row['price']); ?></td>
                        <td>
                            <?php if ($row['stock_quantity'] <= 20): ?>
                                <span class="badge badge-danger"><?php echo $row['stock_quantity']; ?></span>
                            <?php else: ?>
                                <span class="badge badge-success"><?php echo $row['stock_quantity']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $row['status'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?>
                        </td>
                        <td>
                            <a href="product_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <button onclick="confirmDelete('product_delete.php?id=<?php echo $row['id']; ?>')" class="btn btn-danger btn-sm">Delete</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

<?php require_once 'includes/footer.php'; ?>