<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once(__DIR__ . "/../admin/config/database.php");

$user = $_SESSION['user'] ?? null;

// ===== COUNT SESSION CART =====
$count = array_sum($_SESSION['cart'] ?? []);
?>

<style>
/* ===== SEARCH ===== */
.search-wrapper{
    position: relative;
}

.search-input{
    width: 220px;
    padding-right: 40px;
    border-radius: 20px;
}

.search-btn{
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: #ffc107;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
}
/* ===== LOGO ===== */
.logo-img{
    height: 50px;          /* giữ chiều cao cũ */
    transform: scale(1.3); /* phóng to logo */
    transform-origin: left center;
}
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-black px-4">

    <a class="navbar-brand" href="index.php">
    <img src="img/logo.jpg" class="logo-img" alt="Logo">
</a>

    <div class="collapse navbar-collapse">

        <!-- MENU -->
        <ul class="navbar-nav me-auto ms-4">
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php">TRANG CHỦ</a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?page=products">
                    SẢN PHẨM
                </a>
            </li>
        </ul>

        <!-- SEARCH (ĐÃ FIX) -->
        <form class="d-flex me-3 search-box" method="GET">
            <input type="hidden" name="page" value="products">

            <div class="search-wrapper">
                <input class="form-control search-input" name="keyword" placeholder="Tìm kiếm">
                <button class="search-btn">🔍</button>
            </div>
        </form>

        <!-- USER -->
        <div class="dropdown me-3">

            <?php if($user): ?>
                
                <a class="text-white d-flex align-items-center gap-2 dropdown-toggle"
                   data-bs-toggle="dropdown">

                    <i class="bi bi-person-circle fs-5"></i>
                    <span><?= htmlspecialchars($user['full_name'] ?? ') ?></span>

                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="index.php?page=profile">👤 Thông tin</a></li>
                    <li><a class="dropdown-item" href="index.php?page=order">📦 Đơn hàng</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="action/logout.php">
                            🚪 Đăng xuất
                        </a>
                    </li>
                </ul>

            <?php else: ?>

                <a href="index.php?page=login" class="text-white fs-5">
                    <i class="bi bi-person"></i>
                </a>

            <?php endif; ?>

        </div>

        <!-- CART -->
        <div class="dropdown">

            <a class="text-white position-relative"
               data-bs-toggle="dropdown">

                <i class="bi bi-cart3 fs-5"></i>

                <span id="cart-count"
                      class="position-absolute top-0 start-100 translate-middle badge bg-danger">
                    <?= $count ?>
                </span>

            </a>

            <div id="cartContainer"
                 class="dropdown-menu dropdown-menu-end"
                 style="padding:0; border:none; background:transparent;">

                <?php include(__DIR__ . "/../component/cart.php"); ?>

            </div>

        </div>

    </div>
</nav>

<script>
if(typeof BASE === "undefined"){
    var BASE = "/web2_Softdrink/";
}

// ===== TOAST =====
function showToast(message){
    let toast = document.createElement("div");
    toast.innerText = message;

    toast.style.position = "fixed";
    toast.style.top = "20px";
    toast.style.right = "20px";
    toast.style.background = "#16a34a";
    toast.style.color = "#fff";
    toast.style.padding = "12px 20px";
    toast.style.borderRadius = "8px";
    toast.style.boxShadow = "0 5px 15px rgba(0,0,0,0.2)";
    toast.style.zIndex = "9999";
    toast.style.fontSize = "14px";
    toast.style.opacity = "0";
    toast.style.transition = "0.3s";

    document.body.appendChild(toast);

    setTimeout(()=> toast.style.opacity = "1", 10);
    setTimeout(()=> toast.remove(), 2000);
}

// ===== LOAD COUNT =====
function loadCartCount(){
    fetch(BASE + "action/cart.php?type=count")
    .then(res => res.json())
    .then(data => {
        let badge = document.getElementById("cart-count");
        if(badge){
            badge.innerText = data.count ?? 0;
        }
    })
    .catch(err => console.log("COUNT ERROR:", err));
}

// ===== UPDATE CART =====
function updateCart(type, id){
    fetch(BASE + "action/cart.php?type=" + type + "&id=" + id)
    .then(res => res.json())
    .then(data => {

        if(data.status !== "success"){
            alert("Lỗi giỏ hàng!");
            return;
        }

        let badge = document.getElementById("cart-count");
        if(badge){
            badge.innerText = data.count ?? 0;
        }

        showToast("🛒 Đã thêm sản phẩm");
        reloadCart();
    });
}

// ===== RELOAD CART =====
function reloadCart(){
    fetch(BASE + "component/cart.php?nocache=" + Date.now())
    .then(res => res.text())
    .then(html => {

        let temp = document.createElement("div");
        temp.innerHTML = html;

        let newCart = temp.querySelector(".cart-box");

        if(newCart){
            let container = document.getElementById("cartContainer");
            container.innerHTML = "";
            container.appendChild(newCart);
        }
    });
}
</script>