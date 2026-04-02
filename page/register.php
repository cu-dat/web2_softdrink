<div class="auth-wrapper">

    <div class="auth-card">

        <h3 class="auth-title">Đăng ký</h3>

        <!-- ERROR -->
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                switch($_GET['error']){
                    case 'empty': echo "Vui lòng nhập đầy đủ"; break;
                    case 'exist': echo "Email đã tồn tại"; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- SUCCESS -->
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                Đăng ký thành công! Hãy đăng nhập
            </div>
        <?php endif; ?>

        <form method="POST" action="action/register.php" onsubmit="return validateForm()">

            <input id="name" 
                   class="form-control mb-3" 
                   name="name" 
                   placeholder="Họ tên">

            <input id="email" 
                   class="form-control mb-3" 
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
                Đăng ký
            </button>

        </form>

        <a href="index.php?page=login" class="d-block text-center mt-2">
            Đã có tài khoản?
        </a>

    </div>

</div>

<script>
function togglePass(){
    let p = document.getElementById("password");
    p.type = (p.type === "password") ? "text" : "password";
}

function validateForm(){
    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    let pass = document.getElementById("password").value;

    if(name === "" || email === "" || pass === ""){
        alert("Nhập đầy đủ thông tin!");
        return false;
    }

    if(pass.length < 6){
        alert("Mật khẩu >= 6 ký tự");
        return false;
    }

    return true;
}
</script>