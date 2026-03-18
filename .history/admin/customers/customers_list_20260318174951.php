<?php
include "../config/database.php";

$result = $conn->query("SELECT * FROM customers");
?>

<h2>Danh sách khách hàng</h2>

<a href="add.php">+ Thêm khách hàng</a><br><br>

<table border="1">
<tr>
    <th>ID</th>
    <th>Tên</th>
    <th>Email</th>
    <th>Role</th>
    <th>Trạng thái</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['full_name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['role'] ?></td>
    <td>
        <?= $row['status'] == 1 ? "Hoạt động" : "Đã khóa" ?>
    </td>
    <td>
        <a href="edit.php?id=<?= $row['id'] ?>">Sửa</a> |
        <a href="delete.php?id=<?= $row['id'] ?>">Xóa</a> |
        <a href="lock.php?id=<?= $row['id'] ?>">
            <?= $row['status'] == 1 ? "Khóa" : "Mở khóa" ?>
        </a>
    </td>
</tr>
<?php } ?>
</table>