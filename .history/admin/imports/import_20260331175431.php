<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

$imports = $conn->query("SELECT * FROM imports ORDER BY created_at DESC");
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h5>📦 Phiếu nhập</h5>
            <a href="import_add.php" class="btn btn-success btn-sm">+ Tạo phiếu</a>
        </div>

        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Mã</th>
                        <th>Trạng thái</th>
                        <th>Ngày</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($row = $imports->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['import_code'] ?></td>
                        <td>
                            <?= $row['status'] === 'completed' 
                                ? '<span class="badge bg-success">Hoàn thành</span>' 
                                : '<span class="badge bg-warning">Nháp</span>' ?>
                        </td>
                        <td><?= formatDate($row['created_at']) ?></td>
                        <td>
                            <a href="import_add_item.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Sửa</a>

                            <?php if ($row['status'] === 'draft'): ?>
                                <a href="import_complete.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Hoàn thành phiếu?')">
                                   Hoàn thành
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
