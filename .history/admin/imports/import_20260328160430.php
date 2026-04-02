<?php
require_once '../config/database.php';

$imports = $conn->query("SELECT * FROM imports ORDER BY id DESC");
?>

<h2>Danh sách phiếu nhập</h2>

<a href="import_add.php">+ Tạo phiếu</a>

<table border="1">
<tr>
    <th>Mã</th>
    <th>Nhà cung cấp</th>
    <th>Trạng thái</th>
    <th>Hành động</th>
</tr>

<?php while($i = $imports->fetch_assoc()): ?>
<tr>
    <td><?= $i['import_code'] ?></td>
    <td><?= $i['supplier_name'] ?></td>
    <td><?= $i['status'] ? 'Hoàn thành' : 'Nháp' ?></td>
    <td>
        <a href="import_edit.php?id=<?= $i['id'] ?>">Xem</a>
    </td>
</tr>
<?php endwhile; ?>
</table>