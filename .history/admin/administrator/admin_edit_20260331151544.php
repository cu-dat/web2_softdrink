<div class="container mt-5">

    <div class="card shadow border-0">

        <div class="card-header bg-warning">
            <h5 class="mb-0">✏️ Sửa Administrator</h5>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" value="<?= $user['username'] ?>" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label>Họ tên</label>
                    <input type="text" name="full_name" value="<?= $user['full_name'] ?>" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $user['email'] ?>" class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="admin_list.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button class="btn btn-warning">💾 Cập nhật</button>
                </div>

            </form>

        </div>

    </div>

</div>