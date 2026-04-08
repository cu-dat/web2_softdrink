<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
requireAdminLogin();

$imports = $conn->query("SELECT * FROM imports ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách phiếu nhập</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS CUSTOM -->
    <style>
        body {
            background: #f5f6fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .card-header {
            font-weight: 600;
            font-size: 16px;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background: #0d6efd !important;
            color: white;
            text-align: center;
        }

        .table td {
            vertical-align: middle;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .badge {
            font-size: 13px;
            padding: 6px 10px;
        }

        h5 {
            margin: 0;
        }
    </style>

</head>

<body>

<div class="container mt-4">

    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📦 Danh sách phiếu nhập</h5>
            <a href="import_add.php" class="btn btn-success btn-sm">+ Thêm</a>
        </div>

        <!-- BODY -->
        <div class="card-body">

            <table class="table table-bordered table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã phiếu</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = $imports->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['import_code']; ?></td>
                            <td><?= htmlspecialchars($row['supplier_name']); ?></td>
                            <td><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>

                            <td>
                                <?php if ($row['status']): ?>
                                    <span class="badge bg-success">Hoàn thành</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nháp</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="import_edit.php?id=<?= $row['id']; ?>"
                                   class="btn btn-warning btn-sm">Sửa</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

</body>
</html>