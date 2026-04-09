<?php 
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm khách hàng</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f6f9;">

<div class="container mt-5">
    <div class="card shadow">
        
        <!-- Header -->
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">➕ Thêm khách hàng</h4>
        </div>

        <!-- Body -->
        <div class="card-body">

            <form action="customer_insert.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="customer">Khách hàng</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Button -->
                <div class="d-flex justify-content-between">
                    <a href="customer_list.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button type="submit" class="btn btn-success">💾 Lưu</button>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>