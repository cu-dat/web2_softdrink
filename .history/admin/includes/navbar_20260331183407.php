<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="/web2_softdrink/admin/index.php">
            🥤 SoftDrink Admin
        </a>

        <!-- nút menu mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarMenu">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>"
                        href="/web2_softdrink/admin/index.php">
                        📊 Bảng điều khiển
                    </a>
                </li>

                <!-- Products -->
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($currentPage, ['product.php', 'product_add.php', 'product_edit.php']) ? 'active' : ''; ?>"
                        href="/web2_softdrink/admin/products/product.php">
                        🍹 Sản phẩm
                    </a>
                </li>

                <!-- Orders -->
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($currentPage, ['orders.php', 'order_detail.php']) ? 'active' : ''; ?>"
                        href="/web2_softdrink/admin/orders/orders.php">
                        📦 Đơn hàng
                    </a>
                </li>

                <!-- Customers -->
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'customer_list.php' ? 'active' : ''; ?>"
                        href="/web2_softdrink/admin/customers/customer_list.php">
                        👥 Khách hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage == 'import.php' ? 'active' : ''; ?>"
                        href="/web2_softdrink/admin/imports/import.php">
                        📦 Nhập hàng
                    </a>
                </li>
                <?php
require_once '../config/database.php';

$threshold = $_GET['threshold'] ?? 20;

$sql = "
SELECT * FROM products
WHERE stock_quantity <= $threshold
ORDER BY stock_quantity ASC
";

$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5>⚠️ Sản phẩm sắp hết</h5>
        </div>

        <div class="card-body">

            <form method="GET" class="mb-3">
                <input type="number" name="threshold" 
                    value="<?= $threshold ?>" 
                    class="form-control w-25 d-inline">
                <button class="btn btn-danger">Cảnh báo</button>
            </form>

            <table class="table table-bordered text-center">
                <tr>
                    <th>Tên</th>
                    <th>Tồn kho</th>
                </tr>

                <?php while($row = $result->fetch_assoc()): ?>
                <tr class="<?= $row['stock_quantity'] <= 5 ? 'table-danger' : 'table-warning' ?>">
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['stock_quantity'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>

        </div>
    </div>
</div>
            </ul>
            <!-- Logout -->
            <a href="/web2_softdrink/admin/logout.php" class="btn btn-danger">
                🚪 Đăng xuất
            </a>
        </div>
    </div>
</nav>