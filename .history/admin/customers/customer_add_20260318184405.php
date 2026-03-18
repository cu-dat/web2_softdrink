<?php
include "../config/database.php";
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM customers WHERE id = $id");
$row = $result->fetch_assoc();
?>

<div class="container mt-5">
    <div class="card shadow">

        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">✏️ Sửa khách hàng</h5>
        </div>

        <div class="card-body">

            <form action="update.php" method="POST">

                <input type="hidden" name="id" value="<?= $row['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text" name="full_name" 
                           class="form-control"
                           value="<?= $row['full_name'] ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" 
                           class="form-control"
                           value="<?= $row['email'] ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="customer" <?= $row['role']=="customer"?"selected":"" ?>>Customer</option>
                        <option value="staff" <?= $row['role']=="staff"?"selected":"" ?>>Staff</option>
                        <option value="admin" <?= $row['role']=="admin"?"selected":"" ?>>Admin</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="list.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>