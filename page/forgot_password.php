<?php if(session_status() == PHP_SESSION_NONE) session_start(); ?>

<div class="auth-wrapper">

    <div class="auth-card">

        <h3 class="auth-title">Quên mật khẩu</h3>

        <!-- SUCCESS -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- ERROR -->
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="action/forgot_password.php">

            <input class="form-control mb-3" 
                   name="email" 
                   placeholder="Nhập email">

            <input class="form-control mb-3" 
                   name="new_password" 
                   placeholder="Nhập mật khẩu mới">

            <button class="btn btn-warning w-100">
                Đổi mật khẩu
            </button>

        </form>

        <a href="index.php?page=login" class="d-block text-center mt-3">
            ← Quay lại đăng nhập
        </a>

    </div>

</div>