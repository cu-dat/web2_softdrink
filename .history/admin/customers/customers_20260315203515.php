<?php
$pageTitle = 'Customers';
require_once '';
require_once '/../admin/includes/sidebar.php';

$customers = $conn->query("
    SELECT c.*, COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount), 0) as total_spent
    FROM customers c 
    LEFT JOIN orders o ON c.id = o.customer_id 
    GROUP BY c.id 
    ORDER BY c.created_at DESC
");
?>

<div class="main-content">
    <div class="top-header">
        <h1>👥 Customers</h1>
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
                <h2>All Customers (<?php echo $customers->num_rows; ?>)</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $customers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></td>
                        <td><span class="badge badge-info"><?php echo $row['order_count']; ?></span></td>
                        <td><strong><?php echo formatCurrency($row['total_spent']); ?></strong></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

<?php require_once 'includes/footer.php'; ?>