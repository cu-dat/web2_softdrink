<?php
include "../config/database.php";
require_once '../includes/header.php';
require_once '../includes/navbar.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: customer_list.php");
    exit();
}

$result = $conn->query("SELECT * FROM users WHERE id = $id");
$row = $result->fetch_assoc();
if (!$row) {
    header("Location: customer_list.php");
    exit();
}
?>

<div class="container mt-5">
    <div class="card shadow">

        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">✏️ Sửa người dùng</h5>
        </div>

        <div class="card-body">

            <form action="customer_update.php" method="POST">

                <input type="hidden" name="id" value="<?= $row['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text" name="full_name" 
                           class="form-control"
                           value="<?= htmlspecialchars($row['full_name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" 
                           class="form-control"
                           value="<?= htmlspecialchars($row['email']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" 
                           class="form-control"
                           value="<?= htmlspecialchars($row['phone'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" 
                           class="form-control"
                           value="<?= htmlspecialchars($row['address'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="customer" <?= $row['role'] == 'customer' ? 'selected' : '' ?>>Khách hàng</option>
                        <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= ($row['status'] == 1) ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= ($row['status'] == 0) ? 'selected' : '' ?>>Bị khóa</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="customer_list.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>