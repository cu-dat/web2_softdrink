<?php
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

$import_id = $_GET['id'];

$products = $conn->query("SELECT * FROM products");
$check = $conn->query("
    SELECT status FROM imports WHERE id = $import_id
")->fetch_assoc();

if ($check['status'] != 'draft') {
    echo "<div class='alert alert-danger'>Phiếu đã duyệt, không thể sửa!</div>";
    
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($check['status'] != 'draft') {
        die("Phiếu đã duyệt, không thể thêm sản phẩm!");
    }
    $pid = $_POST['product_id'];
    $qty = $_POST['quantity'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("
        INSERT INTO import_details (import_id, product_id, quantity, import_price)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("iiid", $import_id, $pid, $qty, $price);
    $stmt->execute();

    // 👉 CHUYỂN VỀ TRANG IMPORT
    header("Location: import_add_item.php?id=$import_id");
    exit();
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
                        <?php while ($p = $products->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= $p['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="number" name="quantity"
                        class="form-control"
                        placeholder="Số lượng"
                        min="1"
                        required>
                </div>

                <div class="col-md-3">
                    <input type="number" name="price"
                        class="form-control"
                        placeholder="Giá nhập (VD: 10000)"
                        min="1"
                        required>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Thêm</button>
                </div>

            </form>

            <a href="import_finish.php?id=<?= $import_id ?>"
                class="btn btn-success mt-3">
                ✔ Hoàn tất (lưu nháp)
            </a>
            <a href="import.php" class="btn btn-secondary mt-3">
                ⬅ Quay về
            </a>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>