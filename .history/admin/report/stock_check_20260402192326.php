<?php
$pageTitle = "Tra cứu tồn kho";
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

// 👉 ĐẶT Ở ĐÂY
$date = $_GET['date'] ?? date('Y-m-d');

$sql = "
SELECT p.name, COALESCE(i.quantity, 0) as stock
FROM products p
LEFT JOIN inventory i ON p.id = i.product_id
";

$result = $conn->query($sql);
?>

<div class="container mt-4">

    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h5>📦 Tra cứu tồn kho</h5>
        </div>

        <div class="card-body">

            <!-- FILTER -->
            <form method="GET" class="row g-2 mb-3">

                <div class="col-md-3">
                    <input type="date" name="date" value="<?= $date ?>" class="form-control">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Lọc</button>
                </div>

                <div class="col-md-2">
                    <a href="stock_check.php" class="btn btn-secondary w-100">Reset</a>
                </div>

            </form>

            <!-- TABLE -->
            <table class="table table-bordered table-hover text-center align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Nhập</th>
                        <th>Xuất</th>
                        <th>Tồn</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = $result->fetch_assoc()): 
                        $stock = $row['total_import'] - $row['total_export'];
                    ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['total_import'] ?></td>
                        <td><?= $row['total_export'] ?></td>
                        <td class="fw-bold <?= $stock <= 10 ? 'text-danger' : 'text-success' ?>">
                            <?= $stock ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>