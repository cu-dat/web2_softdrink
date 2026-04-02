<?php
$pageTitle = 'Products';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Lấy category_id từ URL
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Tạo câu SQL gốc
$sql = "
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
";

// Nếu có lọc thì thêm WHERE
if ($category_id > 0) {
    $sql .= " WHERE p.category_id = $category_id";
}

// Sắp xếp
$sql .= " ORDER BY p.created_at DESC";

// Chạy query
$products = $conn->query($sql);
?>

<div class="container mt-4">

    <!-- Flash message -->
    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?>">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>

    <div class="card shadow">

        <!-- Header -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🍹 Danh sách sản phẩm</h5>
            <a href="category_add.php" class="btn btn-info btn-sm">+ Thêm loại sản phẩm</a>
            <a href="product_add.php" class="btn btn-success btn-sm">+ Thêm</a>
        </div>

        <!-- Body -->
        <div class="card-body">

            <div class="mb-3">
                <strong>Tổng sản phẩm:</strong> <?php echo $products->num_rows; ?>
            </div>

            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $products->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>

                            <td>
                                <?php if ($row['image']): ?>
                                    <img src="uploads/<?= $row['image']; ?>"
                                        class="img-thumbnail"
                                        style="width:60px; height:60px; object-fit:cover;">
                                <?php else: ?>
                                    <span style="font-size:25px;">🥤</span>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['category_name'] ?? 'No Category'); ?></td>

                            <td class="text-success fw-bold">
                                <?= formatCurrency($row['price']); ?>
                            </td>

                            <td>
                                <?php if ($row['stock_quantity'] <= 20): ?>
                                    <span class="badge bg-danger"><?= $row['stock_quantity']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $row['stock_quantity']; ?></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($row['status']): ?>   
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="product_edit.php?id=<?= $row['id']; ?>"
                                    class="btn btn-warning btn-sm">Sửa</a>

                                <button onclick="confirmDelete('product_delete.php?id=<?= $row['id']; ?>')"
                                    class="btn btn-danger btn-sm">Xóa</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>