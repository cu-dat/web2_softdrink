<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/navbar.php';
require_once '../includes/header.php';

requireAdmin($conn);

$result = $conn->query("
    SELECT * FROM imports ORDER BY id ASC
");
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
                        <th>ID</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= formatDate($row['created_at']) ?></td>

                            <td>
                                <?= $row['status'] == 'completed'
                                    ? '<span class="badge bg-success">Hoàn thành</span>'
                                    : '<span class="badge bg-warning">Nháp</span>' ?>
                            </td>
                            

                            <td>
                                
                                <a href="import_add_item.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Thêm SP</a>
                                <?php if ($row['status'] == 'draft'): ?>

        <!-- ✅ Nút sửa -->
        <a href="import_add_item.php?id=<?= $row['id'] ?>" 
           class="btn btn-sm btn-warning">
           Sửa
        </a>
        <
                                <?php if ($row['status'] == 'draft'): ?>
                                    <a href="import_complete.php?id=<?= $row['id'] ?>"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Hoàn thành?')">
                                        Hoàn tất
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
<?php require_once '../includes/footer.php'; ?>