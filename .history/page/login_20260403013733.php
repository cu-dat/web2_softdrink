<?php if(session_status() == PHP_SESSION_NONE) session_start(); ?>

<div class="auth-wrapper">

    <div class="auth-card">

        <h3 class="auth-title">Đăng nhập</h3>

        <!-- SUCCESS MESSAGE -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- ERROR MESSAGE (FIX: dùng SESSION) -->
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="action/login.php">

            <input class="form-control mb-3" 
                   name="email" 
                   placeholder="Email">

            <div class="position-relative">
                <input type="password" 
                       id="password"
                       class="form-control mb-3" 
                       name="password" 
                       placeholder="Mật khẩu">

                <span onclick="togglePass()" 
                      style="position:absolute; right:10px; top:10px; cursor:pointer;">
                    👁️
                </span>
            </div>

            <button class="btn btn-dark w-100">
                Đăng nhập
            </button>

        </form>

        <!-- 2 LINK NẰM NGANG -->
        <div class="d-flex justify-content-between mt-2">

            <!-- 🔥 chuyển sang trang riêng -->
            <a href="index.php?page=forgot_password">
                Quên mật khẩu?
            </a>

            <a href="index.php?page=register">
                Chưa có tài khoản?
            </a>

        </div>

        <!-- GOOGLE -->
        <a href="action/google_login.php" class="google-btn mt-3">
    
            <img src="https://developers.google.com/identity/images/g-logo.png" alt="google">

            <span>Đăng nhập bằng Google</span>

        </a>

    </div>

</div>

<script>
function togglePass(){
    let p = document.getElementById("password");
    p.type = (p.type === "password") ? "text" : "password";
}
</script>