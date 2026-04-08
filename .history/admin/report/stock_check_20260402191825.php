<?php
$pageTitle = "Tra cứu tồn kho";
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

// 👉 date giờ không còn ý nghĩa nếu dùng inventory
$date = $_GET['date'] ?? date('Y-m-d');

// 👉 Query chuẩn
$sql = "
SELECT 
    p.name,
    COALESCE(i.stock, 0) as stock
FROM products p
LEFT JOIN inventory i ON p.id = i.product_id
ORDER BY p.name ASC
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
            <!-- 👉 Nếu dùng inventory thì filter date không cần -->
            <form method="GET" class="row g-2 mb-3">

                <div class="col-md-3">
                    <input type="date" name="date" value="<?= $date ?>" class="form-control" disabled>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-secondary w-100" disabled>Không áp dụng</button>
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
                        <th>Tồn kho</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>

                        <td class="fw-bold <?= $row['stock'] <= 10 ? 'text-danger' : 'text-success' ?>">
                            <?= $row['stock'] ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>