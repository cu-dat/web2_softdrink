<?php
require_once '../config/database.php';

$id = intval($_GET['id']);
$import = $conn->query("SELECT * FROM imports WHERE id = $id")->fetch_assoc();

// KHÔNG CHO SỬA NẾU HOÀN THÀNH
if ($import['status'] == 1) {
    die("Phiếu đã hoàn thành!");
}

// search sản phẩm
$keyword = $_GET['keyword'] ?? '';

$sql = "SELECT * FROM products WHERE status = 1";

if ($keyword) {
    $sql .= " AND name LIKE '%$keyword%'";
}

$products = $conn->query($sql);

// chi tiết
$details = $conn->query("
SELECT d.*, p.name 
FROM import_details d
JOIN products p ON d.product_id = p.id
WHERE d.import_id = $id
");
?>

<div class="container mt-4">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            📦 Phiếu: <?= $import['import_code'] ?>
        </div>

        <div class="card-body">

            <!-- SEARCH PRODUCT -->
            <form method="GET" class="row mb-3">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control"
                        placeholder="Tìm sản phẩm..." value="<?= $keyword ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary">Tìm</button>
                </div>
            </form>

            <!-- ADD PRODUCT -->
            <form method="POST" action="import_add_item.php" class="row mb-3">
                <input type="hidden" name="import_id" value="<?= $id ?>">

                <div class="col-md-4">
                    <select name="product_id" class="form-select">
                        <?php while($p = $products->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="number" name="quantity" class="form-control" placeholder="Số lượng">
                </div>

                <div class="col-md-3">
                    <input type="number" name="price" class="form-control" placeholder="Giá nhập">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success w-100">+ Thêm</button>
                </div>
            </form>

            <!-- TABLE -->
            <table class="table table-bordered text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Tên</th>
                        <th>SL</th>
                        <th>Giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                $total = 0;
                while($d = $details->fetch_assoc()):
                    $amount = $d['quantity'] * $d['import_price'];
                    $total += $amount;
                ?>
                    <tr>
                        <td><?= $d['name'] ?></td>
                        <td><?= $d['quantity'] ?></td>
                        <td><?= number_format($d['import_price']) ?></td>
                        <td class="text-danger"><?= number_format($amount) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <h5 class="text-end">Tổng: <?= number_format($total) ?> đ</h5>

            <!-- COMPLETE -->
            <form method="POST" action="import_complete.php" class="text-end">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button class="btn btn-success">✅ Hoàn thành</button>
            </form>

        </div>
    </div>
</div>