<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// (Bạn đã check status ở đây rồi ✔)

// 👉 Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($_POST['quantity'] as $detail_id => $qty) {

        $price = $_POST['price'][$detail_id];

        $stmt = $conn->prepare("
            UPDATE import_details 
            SET quantity = ?, import_price = ?
            WHERE id = ?
        ");

        $stmt->bind_param("idi", $qty, $price, $detail_id);
        $stmt->execute();
    }

    setFlashMessage('success', 'Cập nhật phiếu thành công!');
    header("Location: import.php");
    exit();
}

// 👉 lấy data
$items = $conn->query("
    SELECT d.id, d.product_id, d.quantity, d.import_price, p.name 
    FROM import_details d
    JOIN products p ON d.product_id = p.id
    WHERE d.import_id = $id
");

// 👉 include UI SAU CÙNG
require_once '../includes/navbar.php';
require_once '../includes/header.php';

$products = $conn->query("SELECT id, name FROM products");
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning">
            <h5>✏️ Sửa phiếu nhập</h5>
        </div>

        <form method="POST">
            <?php if ($items->num_rows == 0): ?>
                <div class="alert alert-warning no-remove d-flex justify-content-between align-items-center">
                    <span>Phiếu chưa có sản phẩm!</span>
                    <a href="import_add_item.php?id=<?= $id ?>" class="btn btn-sm btn-primary">
                        ➕ Thêm sản phẩm
                    </a>
                </div>
            <?php endif; ?>
            <table class="table table-bordered">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá nhập</th>
                </tr>

                <?php while ($row = $items->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <select name="product_id[<?= $row['id'] ?>]" class="form-select">

                                <?php
                                $products->data_seek(0); // reset con trỏ
                                while ($p = $products->fetch_assoc()):
                                ?>
                                    <option value="<?= $p['id'] ?>"
                                        <?= $p['id'] == $row['product_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['name']) ?>
                                    </option>
                                <?php endwhile; ?>

                            </select>
                        </td>

                        <td>
                            <input type="number"
                                name="quantity[<?= $row['id'] ?>]"
                                value="<?= $row['quantity'] ?>"
                                class="form-control"
                                min="1">
                        </td>

                        <td>
                            <input type="number"
                                name="price[<?= $row['id'] ?>]"
                                value="<?= $row['import_price'] ?>"
                                class="form-control"
                                min="1">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <button class="btn btn-success mt-3">💾 Lưu thay đổi</button>
            <a href="import.php" class="btn btn-secondary mt-3">⬅ Quay lại</a>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>