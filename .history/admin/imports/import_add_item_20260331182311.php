<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

$import_id = $_GET['id'];

$products = $conn->query("SELECT * FROM products");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pid = $_POST['product_id'];
    $qty = $_POST['quantity'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("
        INSERT INTO import_details (import_id, product_id, quantity, import_price)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("iiid", $import_id, $pid, $qty, $price);
    $stmt->execute();
}
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5>➕ Thêm sản phẩm</h5>
        </div>

        <div class="card-body">

            <form method="POST" class="row g-2">

                <div class="col-md-4">
                    <select name="product_id" class="form-select">
                        <?php while($p = $products->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= $p['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="number" name="quantity" class="form-control" placeholder="Số lượng" required>
                </div>

                <div class="col-md-3">
                    <input type="number" name="price" class="form-control" placeholder="Giá nhập" required>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Thêm</button>
                </div>
            </form>

        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>