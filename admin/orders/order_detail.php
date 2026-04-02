<?php
$pageTitle = 'Order Detail';
require_once 'includes/header.php';
require_once 'includes/navbar.php';

$id = intval($_GET['id'] ?? 0);
if ($id === 0) { header("Location: orders.php"); exit(); }

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = sanitize($_POST['status']);
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $id);
    $stmt->execute();
    $stmt->close();
    setFlashMessage('success', 'Order status updated!');
    header("Location: order_detail.php?id=$id");
    exit();
}

$order = $conn->query("
    SELECT o.*, c.full_name, c.email, c.phone, c.address 
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id = c.id 
    WHERE o.id = $id
")->fetch_assoc();

if (!$order) { header("Location: orders.php"); exit(); }

$items = $conn->query("
    SELECT od.*, p.name as product_name 
    FROM order_details od 
    LEFT JOIN products p ON od.product_id = p.id 
    WHERE od.order_id = $id
");
?>

<div class="main-content">
    <div class="top-header">
        <h1>📋 Order #<?php echo $id; ?></h1>
        <div class="admin-info">
            <a href="orders.php" class="btn btn-primary btn-sm">← Back to Orders</a>
        </div>
    </div>
    
    <div class="page-content">
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <!-- Order Info -->
        <div class="table-container" style="margin-bottom:20px;">
            <h2 style="margin-bottom:15px;">Order Information</h2>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <p><strong>Order #:</strong> <?php echo $order['id']; ?></p>
                    <p><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                    <p><strong>Status:</strong> <?php echo getStatusBadge($order['status']); ?></p>
                    <p><strong>Total:</strong> <strong style="font-size:18px;color:#27ae60;"><?php echo formatCurrency($order['total_amount']); ?></strong></p>
                </div>
                <div>
                    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['full_name'] ?? 'Unknown'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? '-'); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'] ?? '-'); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address'] ?? '-'); ?></p>
                </div>
            </div>
            <?php if ($order['note']): ?>
                <p style="margin-top:10px;"><strong>Note:</strong> <?php echo htmlspecialchars($order['note']); ?></p>
            <?php endif; ?>

            <!-- Update Status -->
            <form method="POST" style="margin-top:20px; display:flex; align-items:center; gap:10px;">
                <label><strong>Update Status:</strong></label>
                <select name="status" style="padding:8px 12px; border:1px solid #ddd; border-radius:6px;">
                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
            </form>
        </div>

        <!-- Order Items -->
        <div class="table-container">
            <h2 style="margin-bottom:15px;">Order Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($item['product_name'] ?? 'Deleted Product'); ?></td>
                        <td><?php echo formatCurrency($item['price']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><strong><?php echo formatCurrency($item['subtotal']); ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;"><strong>Total:</strong></td>
                        <td><strong style="font-size:18px; color:#27ae60;"><?php echo formatCurrency($order['total_amount']); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

<?php require_once 'includes/footer.php'; ?>