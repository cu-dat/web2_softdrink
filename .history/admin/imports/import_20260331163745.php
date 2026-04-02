<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
requireAdmin();

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
        background: #f8f9fa;
    }

    /* ===== CARD ===== */
    .card {
        border: none;
        border-radius: 10px;
    }

    .card-header {
        font-weight: 600;
        font-size: 16px;
        border-radius: 10px 10px 0 0 !important;
    }

    /* ===== TABLE ===== */
    .table {
        border-radius: 8px;
        overflow: hidden;
    }

    .table-primary th {
        background-color: #0d6efd !important;
        color: #fff;
        text-align: center;
    }

    .table td {
        vertical-align: middle;
    }

    /* ===== HOVER ===== */
    .table-hover tbody tr:hover {
        background-color: #f1f5ff;
    }

    /* ===== INPUT ===== */
    .form-control {
        border-radius: 6px;
        padding: 8px 10px;
        border: 1px solid #ddd;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: none;
    }

    /* ===== BUTTON ===== */
    .btn {
        border-radius: 6px;
        font-weight: 500;
    }

    .btn-sm {
        padding: 5px 10px;
    }

    /* ===== BADGE ===== */
    .badge {
        font-size: 12px;
        padding: 5px 8px;
    }

    /* ===== SEARCH BOX ===== */
    .shadow-sm {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05) !important;
    }

    /* ===== SPACING ===== */
    h5 {
        margin: 0;
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
            <form method="GET" class="row mb-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control shadow-sm"
                        placeholder="Tìm mã phiếu / nhà cung cấp..."
                        value="<?= htmlspecialchars($keyword) ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-sm">Tìm</button>
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
                    <?php while ($row = $imports->fetch_assoc()): ?>
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
<?php require_once '../includes/footer.php'; ?>