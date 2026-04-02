<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
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