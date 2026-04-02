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