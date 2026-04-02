<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireAdmin($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $code = 'IMP' . time();
    $note = $_POST['note'];

    $stmt = $conn->prepare("
        INSERT INTO imports (import_code, note) 
        VALUES (?, ?)
    ");
    $stmt->bind_param("ss", $code, $note);
    $stmt->execute();

    $id = $stmt->insert_id;

    header("Location: import_add_item.php?id=$id");
    exit();
}
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5>➕ Tạo phiếu nhập</h5>
        </div>

        <div class="card-body">
            <form method="POST">
                <textarea name="note" class="form-control" placeholder="Ghi chú"></textarea>
                <button class="btn btn-success mt-3">Tạo phiếu</button>
            </form>
        </div>
    </div>
</div>