

<div class="auth-wrapper">

    <div class="auth-card">

        <h3 class="auth-title">Đăng ký</h3>

        <!-- ERROR -->
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- SUCCESS -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="action/register.php" onsubmit="return validateForm()">

            <input id="name" 
                   class="form-control mb-3" 
                   name="name" 
                   value="<?= $_SESSION['old']['name'] ?? '' ?>"
                   placeholder="Họ tên">

            <input id="email" 
                   class="form-control mb-3" 
                   name="email" 
                   value="<?= $_SESSION['old']['email'] ?? '' ?>"
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
                Đăng ký
            </button>

        </form>

        <a href="index.php?page=login" class="d-block text-center mt-2">
            Đã có tài khoản?
        </a>

    </div>

</div>

<?php unset($_SESSION['old']); ?>

<script>
function togglePass(){
    let p = document.getElementById("password");
    p.type = (p.type === "password") ? "text" : "password";
}

function validateForm(){
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let pass = document.getElementById("password").value.trim();

    if(name === "" || email === "" || pass === ""){
        alert("Nhập đầy đủ thông tin!");
        return false;
    }

    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailPattern.test(email)){
        alert("Email không hợp lệ!");
        return false;
    }

    if(pass.length < 6){
        alert("Mật khẩu >= 6 ký tự");
        return false;
    }

    return true;
}
</script>