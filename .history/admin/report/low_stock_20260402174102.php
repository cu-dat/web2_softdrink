<?php
$pageTitle = 'Sản phẩm sắp hết';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$threshold = $_GET['threshold'] ?? 20;

$sql = "
SELECT p.*, COALESCE(i.quantity,0) as stock
FROM products p
LEFT JOIN inventory i ON p.id = i.product_id
WHERE COALESCE(i.quantity,0) <= $threshold
ORDER BY stock ASC
";

$result = $conn->query($sql);
?>

<div class="container mt-4">

    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-danger text-white d-flex justify-content-between">
            <h5>⚠️ Sản phẩm sắp hết</h5>
        </div>

        <div class="card-body">

            <!-- FILTER -->
            <form method="GET" class="row g-2 mb-3">

                <div class="col-md-3">
                    <input
                        type="number"
                        name="threshold"
                        value="<?= $threshold ?>"
                        class="form-control"
                        placeholder="Ngưỡng cảnh báo">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-danger w-100">Lọc</button>
                </div>

                <div class="col-md-2">
                    <a href="low_stock.php" class="btn btn-secondary w-100">Reset</a>
                </div>

            </form>

            <!-- TABLE -->
            <table class="table table-bordered table-hover text-center align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Tồn kho</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="<?= $row['stock'] <= 5 ? 'table-danger' : 'table-warning' ?>">
                            <td class="fw-bold"><?= $row['stock'] ?></td>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td class="fw-bold"><?= $row['stock'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>