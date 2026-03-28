<?php
$pageTitle = 'Bảng điều khiển';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

// Lấy thống kê
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalCategories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) as total FROM orders WHERE status = 'completed'")->fetch_assoc()['total'];
$totalCustomers = $conn->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];
$pendingOrders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'];

// Đơn hàng gần đây
$recentOrders = $conn->query("
    SELECT o.*, c.full_name as customer_name 
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id = c.id 
    ORDER BY o.created_at DESC LIMIT 5
");

// Sản phẩm sắp hết hàng
$lowStock = $conn->query("SELECT * FROM products WHERE stock_quantity <= 20 ORDER BY stock_quantity ASC LIMIT 5");
?>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📊 Bảng điều khiển</h2>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?>">
            <?php echo $flash['message']; ?>
        </div>
    <?php endif; ?>

    <!-- THỐNG KÊ -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card text-bg-primary shadow">
                <div class="card-body">
                    <h5>Tổng sản phẩm</h5>
                    <h2><?php echo $totalProducts; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-success shadow">
                <div class="card-body">
                    <h5>Tổng doanh thu</h5>
                    <h2><?php echo formatCurrency($totalRevenue); ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-warning shadow">
                <div class="card-body">
                    <h5>Tổng đơn hàng</h5>
                    <h2><?php echo $totalOrders; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-bg-danger shadow">
                <div class="card-body">
                    <h5>Đơn hàng đang chờ</h5>
                    <h2><?php echo $pendingOrders; ?></h2>
                </div>
            </div>
        </div>
    </div>
        
    <!-- ĐƠN HÀNG GẦN ĐÂY -->
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">Đơn hàng gần đây</h5>
            <a href="/web2_softdrink/admin/orders/order.php" class="btn btn-primary btn-sm">Xem tất cả</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Số tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($order = $recentOrders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($order['customer_name'] ?? 'Không xác định'); ?>
                        </td>
                        <td>
                            <?php echo formatCurrency($order['total_amount']); ?>
                        </td>
                        <td>
                            <?php echo getOrderStatusBadge($order['status']); ?>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SẢN PHẨM SẮP HẾT HÀNG -->
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">⚠️ Sản phẩm sắp hết hàng</h5>
            <a href="product.php" class="btn btn-warning btn-sm">
                Xem tất cả
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Tồn kho</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($product = $lowStock->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </td>
                        <td>
                            <span class="badge bg-danger">
                                <?php echo $product['stock_quantity']; ?> còn lại
                            </span>
                        </td>
                        <td>
                            <?php echo formatCurrency($product['price']); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>