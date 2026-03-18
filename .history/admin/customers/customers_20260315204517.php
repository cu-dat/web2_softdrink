<?php
$pageTitle = 'Customers';

require_once '../includes/header.php';
require_once '../includes/navbar.php';

$customers = $conn->query("
    SELECT c.*, COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount), 0) as total_spent
    FROM customers c 
    LEFT JOIN orders o ON c.id = o.customer_id 
    GROUP BY c.id 
    ORDER BY c.created_at DESC
");
?>

<style>

/* layout */

.main-content{
    padding:30px;
}

/* header */

.top-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.top-header h1{
    font-weight:600;
}

.admin-info{
    display:flex;
    align-items:center;
    gap:15px;
}

/* logout button */

.btn-logout{
    background:#dc3545;
    color:white;
    padding:6px 14px;
    border-radius:6px;
    text-decoration:none;
}

.btn-logout:hover{
    background:#bb2d3b;
}

/* table container */

.table-container{
    background:white;
    border-radius:10px;
    padding:20px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

/* table header */

.table-header{
    margin-bottom:15px;
}

.table-header h2{
    font-size:20px;
    font-weight:600;
}

/* table */

table{
    width:100%;
    border-collapse:collapse;
}

table thead{
    background:#343a40;
    color:white;
}

table th{
    padding:12px;
    text-align:left;
}

table td{
    padding:12px;
    border-bottom:1px solid #eee;
}

/* hover */

table tbody tr:hover{
    background:#f8f9fa;
}

/* badge */

.badge{
    padding:5px 10px;
    border-radius:6px;
    font-size:13px;
}

.badge-info{
    background:#0dcaf0;
    color:white;
}

</style>

<div class="main-content">

    <div class="top-header">
        <h1>👥 Customers</h1>

        <div class="admin-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="../logout.php" class="btn-logout">Logout</a>
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

                        <td>
                            <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                        </td>

                        <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>

                        <td><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></td>

                        <td>
                            <span class="badge badge-info">
                                <?php echo $row['order_count']; ?>
                            </span>
                        </td>

                        <td>
                            <strong>
                                <?php echo formatCurrency($row['total_spent']); ?>
                            </strong>
                        </td>

                        <td>
                            <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php require_once '../includes/footer.php'; ?>