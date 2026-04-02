<div class="auth-wrapper">

    <div class="auth-card">

        <h3 class="auth-title">Đăng nhập</h3>

        <!-- ERROR MESSAGE -->
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">

                <?php
                switch($_GET['error']){
                    case 'empty': echo "Vui lòng nhập đầy đủ thông tin"; break;
                    case 'notfound': echo "Email không tồn tại"; break;
                    case 'wrong': echo "Sai mật khẩu"; break;
                    case 'google': echo "Tài khoản này đăng nhập bằng Google"; break;
                }
                ?>

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

        <a href="index.php?page=register" class="d-block text-center mt-2">
            Chưa có tài khoản?
        </a>

        <!-- GOOGLE -->
        <a href="action/google_login.php" class="google-btn">
    
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