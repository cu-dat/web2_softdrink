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

<form method="POST">
    <input name="supplier_name" placeholder="Nhà cung cấp" required>
    <button type="submit">Tạo phiếu</button>
</form>