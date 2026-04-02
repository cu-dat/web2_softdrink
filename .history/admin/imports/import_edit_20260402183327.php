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
<form method="POST">

<table class="table table-bordered">
    <tr>
        <th>Sản phẩm</th>
        <th>Số lượng</th>
        <th>Giá nhập</th>
    </tr>

    <?php while($row = $items->fetch_assoc()): ?>
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

<button class="btn btn-success">💾 Lưu thay đổi</button>
<a href="import.php" class="btn btn-secondary">⬅ Quay lại</a>

</form>
<?php require_once '../includes/footer.php'; ?>