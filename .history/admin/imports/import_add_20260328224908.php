<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = 'IMP' . time();
    $supplier = sanitize($_POST['supplier_name']);

    $stmt = $conn->prepare("INSERT INTO imports (import_code, supplier_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $code, $supplier);
    $stmt->execute();

    $id = $stmt->insert_id;

    header("Location: import_edit.php?id=" . $id);
    exit();
}
?>
<style>
    /* FORM STYLE GIỐNG PRODUCT */
    .card {
        border-radius: 12px;
        border: none;
    }

    .card-header {
        font-weight: 600;
        font-size: 16px;
    }

    .form-label {
        font-weight: 500;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 5px rgba(13, 110, 253, 0.3);
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 8px 16px;
    }

    /* BUTTON HOVER */
    .btn-success:hover {
        background-color: #198754;
    }

    .btn-secondary:hover {
        background-color: #6c757d;
    }
</style>

<div class="container mt-4">

    <div class="card shadow">

        <!-- HEADER -->
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">🧾 Tạo phiếu nhập</h5>
        </div>

        <!-- BODY -->
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nhà cung cấp</label>
                    <input
                        type="text"
                        name="supplier_name"
                        class="form-control"
                        placeholder="Nhập tên nhà cung cấp..."
                        required>
                </div>

                <!-- BUTTON -->
                <div class="d-flex justify-content-between">
                    <a href="imports.php" class="btn btn-secondary">
                        ⬅ Quay lại
                    </a>

                    <button type="submit" class="btn btn-success">
                        ➕ Tạo phiếu
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>