<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Get statistics
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalCategories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) as total FROM orders WHERE status = 'completed'")->fetch_assoc()['total'];
$totalCustomers = $conn->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];
$pendingOrders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'];

// Recent orders
$recentOrders = $conn->query("
    SELECT o.*, c.full_name as customer_name 
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id = c.id 
    ORDER BY o.created_at DESC LIMIT 5
");

// Low stock products
$lowStock = $conn->query("SELECT * FROM products WHERE stock_quantity <= 20 ORDER BY stock_quantity ASC LIMIT 5");
?>

<div class="main-content">
    <div class="top-header">
        <h1>📊 Dashboard</h1>
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

        <!-- Stats Cards -->
        <div class="dashboard-cards">
            <div class="card blue">
                <div class="card-icon">🍹</div>
                <h3>Total Products</h3>
                <div class="card-value"><?php echo $totalProducts; ?></div>
            </div>
            <div class="card green">
                <div class="card-icon">💰</div>
                <h3>Total Revenue</h3>
                <div class="card-value"><?php echo formatCurrency($totalRevenue); ?></div>
            </div>
            <div class="card orange">
                <div class="card-icon">📦</div>
                <h3>Total Orders</h3>
                <div class="card-value"><?php echo $totalOrders; ?></div>
            </div>
            <div class="card red">
                <div class="card-icon">⏳</div>
                <h3>Pending Orders</h3>
                <div class="card-value"><?php echo $pendingOrders; ?></div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="table-container" style="margin-bottom: 30px;">
            <div class="table-header">
                <h2>Recent Orders</h2>
                <a href="orders.php" class="btn btn-primary btn-sm">View All</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $recentOrders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name'] ?? 'Unknown'); ?></td>
                        <td><?php echo formatCurrency($order['total_amount']); ?></td>
                        <td><?php echo getStatusBadge($order['status']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Low Stock -->
        <div class="table-container">
            <div class="table-header">
                <h2>⚠️ Low Stock Products</h2>
                <a href="products.php" class="btn btn-warning btn-sm">View All</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Stock</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $lowStock->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><span class="badge badge-danger"><?php echo $product['stock_quantity']; ?> left</span></td>
                        <td><?php echo formatCurrency($product['price']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

<?php require_once 'includes/footer.php'; ?>