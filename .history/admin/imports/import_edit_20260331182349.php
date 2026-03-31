<?php
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

$id = $_GET['id'];

$items = $conn->query("
    SELECT d.*, p.name 
    FROM import_details d
    JOIN products p ON d.product_id = p.id
    WHERE import_id = $id
");
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning">
            <h5>✏️ Sửa phiếu nhập</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Sản phẩm</th>
                    <th>SL</th>
                    <th>Giá</th>
                </tr>

                <?php while($row = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= formatCurrency($row['import_price']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>