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

<style>

/* ===== BACKGROUND ===== */
body {
    background: #f5f6fa;
}

/* ===== CARD ===== */
.card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.card-header {
    font-weight: 600;
    font-size: 16px;
}

/* ===== TABLE ===== */
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

/* ===== INPUT ===== */
.form-control,
.form-select {
    border-radius: 8px;
    padding: 8px 10px;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 5px rgba(13,110,253,0.3);
}

/* ===== BUTTON ===== */
.btn {
    border-radius: 8px;
    font-weight: 500;
}

/* ===== BADGE ===== */
.badge {
    font-size: 13px;
    padding: 6px 10px;
}

/* ===== TOTAL ===== */
h5 {
    font-weight: 600;
}

.text-danger {
    font-weight: bold;
}

/* ===== HOVER TABLE ===== */
.table-hover tbody tr:hover {
    background-color: #f1f5ff;
}

/* ===== SEARCH BOX ===== */
input[type="text"]::placeholder {
    color: #999;
}

</style>
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