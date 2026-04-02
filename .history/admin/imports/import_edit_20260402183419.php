<?php
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

$id = $_GET['id'];
$check = $conn->query("
    SELECT status FROM imports WHERE id = $id
")->fetch_assoc();

if (!$check || $check['status'] != 'draft') {
    setFlashMessage('error', 'Phiếu đã duyệt, không thể sửa!');
    header("Location: import.php");
    exit();
}

$items = $conn->query("
    SELECT d.*, p.name 
    FROM import_details d
    JOIN products p ON d.product_id = p.id
    WHERE import_id = $id
");
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
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning">
            <h5>✏️ Sửa phiếu nhập</h5>
        </div>

        <form method="POST">

            <table class="table table-bordered">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá nhập</th>
                </tr>

                <?php while ($row = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>

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

            <button class="btn btn-success ">💾 Lưu thay đổi</button>
            <a href="import.php" class="btn btn-secondary">⬅ Quay lại</a>

        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>