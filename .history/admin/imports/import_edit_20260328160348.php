<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
requireAdminLogin();

$id = intval($_GET['id']);

$import = $conn->query("SELECT * FROM imports WHERE id = $id")->fetch_assoc();

if (!$import) die("Không tồn tại");

// ❌ không cho sửa nếu hoàn thành
if ($import['status'] == 1) {
    die("Phiếu đã hoàn thành!");
}

// load sản phẩm
$products = $conn->query("SELECT * FROM products WHERE status = 1");

// load chi tiết
$details = $conn->query("
SELECT d.*, p.name 
FROM import_details d
JOIN products p ON d.product_id = p.id
WHERE d.import_id = $id
");
?>

<h3>Phiếu: <?= $import['import_code']; ?></h3>

<!-- THÊM SẢN PHẨM -->
<form method="POST" action="import_add_item.php">
    <input type="hidden" name="import_id" value="<?= $id ?>">

    <select name="product_id">
        <?php while($p = $products->fetch_assoc()): ?>
            <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
        <?php endwhile; ?>
    </select>

    <input type="number" name="quantity" placeholder="Số lượng" required>
    <input type="number" name="price" placeholder="Giá nhập" required>

    <button>Thêm</button>
</form>

<!-- DANH SÁCH -->
<table border="1">
<tr>
    <th>Tên</th>
    <th>SL</th>
    <th>Giá</th>
</tr>

<?php while($d = $details->fetch_assoc()): ?>
<tr>
    <td><?= $d['name'] ?></td>
    <td><?= $d['quantity'] ?></td>
    <td><?= $d['import_price'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<!-- HOÀN THÀNH -->
<form method="POST" action="import_complete.php">
    <input type="hidden" name="id" value="<?= $id ?>">
    <button>Hoàn thành</button>
</form>