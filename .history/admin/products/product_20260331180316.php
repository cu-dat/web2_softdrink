<?php
$pageTitle = 'Products';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// 1. Lấy category
$categories = $conn->query("SELECT * FROM categories WHERE status = 1");

// 2. Lấy category_id
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// 3. Query sản phẩm
$sql = "
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id
";

if ($category_id > 0) {
    $sql .= " WHERE p.category_id = $category_id";
}

$sql .= " ORDER BY p.created_at ASC";

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
            <form method="GET" class="row mb-3">

                <!-- Dropdown chọn loại -->
                <div class="col-md-4">
                    <select name="category_id" class="form-select">
                        <option value="0">-- Tất cả loại sản phẩm --</option>

                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['id']; ?>"
                                <?= ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Nút lọc -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                </div>

                <!-- Nút reset -->
                <div class="col-md-2">
                    <a href="product.php" class="btn btn-secondary">Reset</a>
                </div>

            </form>

            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Mã</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Đơn vị</th>
                        <th>Giá nhập</th>
                        <th>Giá bán</th>
                        <th>Lợi nhuận</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $products->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>

                            <td><?= htmlspecialchars($row['sku'] ?? '---'); ?></td>

                            <td>
                                <?php if ($row['image']): ?>
                                    <img src="uploads/<?= $row['image']; ?>"
                                        class="img-thumbnail"
                                        style="width:60px; height:60px; object-fit:cover;">
                                <?php else: ?>
                                    🥤
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['category_name'] ?? 'No Category'); ?></td>

                            <td><?= htmlspecialchars($row['unit'] ?? '---'); ?></td>

                            <?php
                            $cost = (float)$row['cost_price'];
                            $margin = (float)$row['profit_margin'];

                            $selling_price = $cost * (1 + $margin / 100);
                            $selling_price = round($selling_price);
                            ?>

                            <td class="text-success fw-bold">
                                <?= formatCurrency($cost); ?>
                            </td>

                            <td class="text-primary fw-bold">
                                <?= formatCurrency($selling_price); ?>
                            </td>

                            <td class="text-warning fw-bold">
                                <?= $margin; ?>%
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
                                    <span class="badge bg-success">Hiển thị</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Ẩn</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="product_toggle.php?id=<?= $row['id']; ?>"
                                    class="btn btn-info btn-sm">
                                    <?= $row['status'] ? 'Ẩn' : 'Hiện'; ?>
                                </a>

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