<?php
include "../config/database.php";
require_once '../includes/header.php';
require_once '../includes/navbar.php';
$result = $conn->query("SELECT * FROM customers");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Danh sách khách hàng</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef1f5;
            margin: 0;
        }

        /* Container chính */
        .container {
            max-width: 1100px;
            margin: 30px auto;
            background: white;
            padding: 20px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        h2 {
            margin: 0;
            color: #333;
        }

        /* Button thêm */
        .btn-add {
            padding: 8px 14px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-add:hover {
            background: #218838;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        th {
            background: #0d6efd;
            color: white;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        tr:hover {
            background: #e9ecef;
        }

        /* Status */
        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-locked {
            color: red;
            font-weight: bold;
        }

        /* Action buttons */
        .action a {
            text-decoration: none;
            padding: 5px 10px;
            margin: 2px;
            border-radius: 4px;
            color: white;
            font-size: 13px;
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

    <div class="container">

        <div class="header">
            <h2>📋 Danh sách khách hàng</h2>
            <a href="add.php" class="btn-add">+ Thêm</a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Role</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['full_name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['role'] ?></td>

                    <td>
                        <?php if ($row['status'] == 1) { ?>
                            <span class="status-active">Hoạt động</span>
                        <?php } else { ?>
                            <span class="status-locked">Đã khóa</span>
                        <?php } ?>
                    </td>

                    <td class="action">
                        <a href="customer_edit.php?id=<?= $row['id'] ?>" class="edit">Sửa</a>
                        <a href="delete.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Xóa?')">Xóa</a>

                        <?php if ($row['status'] == 1) { ?>
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
<?php require_once '../includes/footer.php'; ?>