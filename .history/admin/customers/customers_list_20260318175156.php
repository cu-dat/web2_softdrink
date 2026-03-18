<?php
include "../config/database.php";
$result = $conn->query("SELECT * FROM customers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Danh sách khách hàng</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }

        h2 {
            color: #333;
        }

        .btn-add {
            display: inline-block;
            padding: 10px 15px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .btn-add:hover {
            background: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        tr:hover {
            background: #e9ecef;
        }

        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-locked {
            color: red;
            font-weight: bold;
        }

        .action a {
            text-decoration: none;
            padding: 6px 10px;
            margin: 2px;
            border-radius: 4px;
            color: white;
        }

        .edit {
            background: #ffc107;
        }

        .delete {
            background: #dc3545;
        }

        .lock {
            background: #6c757d;
        }

        .unlock {
            background: #17a2b8;
        }
    </style>
</head>

<body>

<h2>📋 Danh sách khách hàng</h2>

<a href="add.php" class="btn-add">+ Thêm khách hàng</a>

<table>
<tr>
    <th>ID</th>
    <th>Tên</th>
    <th>Email</th>
    <th>Role</th>
    <th>Trạng thái</th>
    <th>Hành động</th>
</tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['full_name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['role'] ?></td>

    <td>
        <?php if($row['status'] == 1) { ?>
            <span class="status-active">Hoạt động</span>
        <?php } else { ?>
            <span class="status-locked">Đã khóa</span>
        <?php } ?>
    </td>

    <td class="action">
        <a href="edit.php?id=<?= $row['id'] ?>" class="edit">Sửa</a>
        <a href="delete.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Xóa?')">Xóa</a>

        <?php if($row['status'] == 1) { ?>
            <a href="lock.php?id=<?= $row['id'] ?>" class="lock">Khóa</a>
        <?php } else { ?>
            <a href="lock.php?id=<?= $row['id'] ?>" class="unlock">Mở</a>
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>