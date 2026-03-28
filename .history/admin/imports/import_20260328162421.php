<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
requireAdminLogin();

$imports = $conn->query("SELECT * FROM imports ORDER BY id DESC");
?>

<head

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
                <thead class="table-primary">
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