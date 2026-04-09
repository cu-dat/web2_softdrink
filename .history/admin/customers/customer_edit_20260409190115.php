<?php
include "../config/database.php";
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/functions.php';

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

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $role = $_POST['role'] ?? 'customer';
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
    
    $sql = "UPDATE users SET full_name=?, email=?, phone=?, address=?, role=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssii", $full_name, $email, $phone, $address, $role, $status, $id);
    
    if ($stmt->execute()) {
        header("Location: customer_list.php?action=edit_success&message=" . urlencode("Đã sửa thông tin {$full_name} thành công!"));
        exit();
    } else {
        $error = "Cập nhật thất bại!";
    }
}
?>

<!-- Phần HTML form giữ nguyên như cũ -->
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">✏️ Sửa người dùng</h5>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($row['full_name']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email'] ?? '') ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($row['phone'] ?? '') ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($row['address'] ?? '') ?>">
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
                        <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>Bị khóa</option>
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