<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
requireAdminLogin();

$keyword = $_GET['keyword'] ?? '';

$sql = "SELECT * FROM imports";

if ($keyword) {
    $sql .= " WHERE import_code LIKE '%$keyword%' OR supplier_name LIKE '%$keyword%'";
}

$sql .= " ORDER BY id DESC";

$imports = $conn->query($sql);
?>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h5>📦 Danh sách phiếu nhập</h5>
            <a href="import_add.php" class="btn btn-success btn-sm">+ Thêm</a>
        </div>

        <div class="card-body">

            <!-- SEARCH -->
            <form method="GET" class="row mb-3">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control shadow-sm"
                        placeholder="Tìm mã phiếu / nhà cung cấp..."
                        value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary">Tìm</button>
                </div>
            </form>

            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Mã</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($row = $imports->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['import_code'] ?></td>
                        <td><?= $row['supplier_name'] ?></td>
                        <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>

                        <td>
                            <?= $row['status'] 
                                ? '<span class="badge bg-success">Hoàn thành</span>' 
                                : '<span class="badge bg-secondary">Nháp</span>' ?>
                        </td>

                        <td>
                            <a href="import_edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                <?= $row['status'] ? 'Xem' : 'Sửa' ?>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>