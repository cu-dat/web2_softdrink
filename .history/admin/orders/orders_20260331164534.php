<?php
$pageTitle = 'Orders';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Filter by status
$statusFilter = sanitize($_GET['status'] ?? '');
$where = '';
if ($statusFilter) {
    $where = "WHERE o.status = '" . $conn->real_escape_string($statusFilter) . "'";
}

$orders = $conn->query("
    SELECT o.*, c.full_name as customer_name, c.phone as customer_phone
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id = c.id 
    $where
    ORDER BY o.created_at DESC
");
?>

<div class="main-content">
    <div class="top-header">
        <h1>📦 Orders</h1>
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

        <!-- Filters -->
        <div style="margin-bottom:20px;">
            <a href="orders.php" class="btn btn-sm <?php echo !$statusFilter ? 'btn-primary' : ''; ?>" style="<?php echo $statusFilter ? 'background:#ddd;' : ''; ?>">All</a>
            <a href="orders.php?status=pending" class="btn btn-sm <?php echo $statusFilter === 'pending' ? 'btn-warning' : ''; ?>" style="<?php echo $statusFilter !== 'pending' ? 'background:#ddd;' : ''; ?>">Pending</a>
            <a href="orders.php?status=processing" class="btn btn-sm <?php echo $statusFilter === 'processing' ? 'btn-info' : ''; ?>" style="<?php echo $statusFilter !== 'processing' ? 'background:#ddd;' : ''; ?>color:<?php echo $statusFilter === 'processing' ? '#fff' : '#333'; ?>">Processing</a>
            <a href="orders.php?status=completed" class="btn btn-sm <?php echo $statusFilter === 'completed' ? 'btn-success' : ''; ?>" style="<?php echo $statusFilter !== 'completed' ? 'background:#ddd;' : ''; ?>">Completed</a>
            <a href="orders.php?status=cancelled" class="btn btn-sm <?php echo $statusFilter === 'cancelled' ? 'btn-danger' : ''; ?>" style="<?php echo $statusFilter !== 'cancelled' ? 'background:#ddd;' : ''; ?>">Cancelled</a>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2>Order List (<?php echo $orders->num_rows; ?>)</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $row['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['customer_name'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_phone'] ?? '-'); ?></td>
                        <td><strong><?php echo formatCurrency($row['total_amount']); ?></strong></td>
                        <td><?php echo getStatusBadge($row['status']); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="order_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">View</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

<?php require_once 'includes/footer.php'; ?>