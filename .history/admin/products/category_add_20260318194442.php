<?php
$pageTitle = 'Add Category';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = isset($_POST['status']) ? 1 : 0;

    if ($name == '') {
        $error = "Tên loại không được để trống";
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name, description, status) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $description, $status);

        if ($stmt->execute()) {
            header("Location: product.php");
            exit();
        } else {
            $error = "Thêm thất bại";
        }
    }
}
?>

<div class="container mt-4">

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow">

        <!-- Header -->
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📂 Thêm loại sản phẩm</h5>
            <a href="products.php" class="btn btn-light btn-sm">← Quay lại</a>
        </div>

        <!-- Body -->
        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Tên loại *</label>
                    <input type="text" name="name" class="form-control" required placeholder="Ví dụ: Nước ngọt">
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Mô tả loại..."></textarea>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="status" class="form-check-input" checked>
                    <label class="form-check-label">Hoạt động</label>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="products.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-info">💾 Lưu loại</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>