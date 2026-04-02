<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>

<style>
.cart-box{
    width:350px;
    background:#fff;
    border-radius:12px;
    padding:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
    font-family:Arial;
}

.cart-item{
    display:flex;
    gap:10px;
    padding:10px 0;
    border-bottom:1px solid #eee;
    align-items:center;
}

.cart-item img{
    width:55px;
    height:55px;
    object-fit:cover;
    border-radius:6px;
}

.cart-info{flex:1;}

.cart-name{font-size:14px;font-weight:500;}

.cart-price{color:#0f766e;font-size:13px;font-weight:bold;}

.cart-qty{display:flex;gap:5px;margin-top:5px;}

.cart-qty button{
    width:25px;height:25px;
    border:none;background:#eee;
    border-radius:4px;cursor:pointer;
}

.cart-remove{
    color:red;
    font-size:18px;
    cursor:pointer;
}

.cart-total{
    display:flex;
    justify-content:space-between;
    font-weight:bold;
    margin-top:10px;
}

.cart-actions a{
    display:block;
    text-align:center;
    margin-top:10px;
    background:#0f766e;
    color:#fff;
    padding:10px;
    border-radius:6px;
    text-decoration:none;
}

/* toast */
.toast{
    position:fixed;
    top:20px;
    right:20px;
    background:#16a34a;
    color:#fff;
    padding:10px 20px;
    border-radius:8px;
    z-index:9999;
    opacity:0;
    transform:translateY(-20px);
    transition:0.3s;
}
.toast.show{
    opacity:1;
    transform:translateY(0);
}
.toast.error{background:#dc2626;}
</style>

<?php
$user = $_SESSION['user'] ?? null;
$user_id = $user['id'] ?? 0;

if(!$user_id){
?>
<div class="cart-box">
    <div style="text-align:center">
        🔒 Vui lòng đăng nhập<br><br>
        <a href="index.php?page=login" class="login-btn">Đăng nhập</a>
    </div>
</div>

<style>
.login-btn{
    background:#0f766e;
    color:#fff;
    padding:8px 15px;
    border-radius:6px;
    text-decoration:none;
}
</style>
<?php
    return;
}

require_once(__DIR__ . "/../admin/config/database.php");

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<div class="cart-box">

<?php if(empty($cart)): ?>
    <div>🛒 Giỏ hàng trống</div>
<?php else: ?>

<?php foreach($cart as $id => $qty): 
$id = (int)$id;
$qty = (int)$qty;

$res = $conn->query("SELECT * FROM products WHERE id=$id");
if(!$res) continue;

$p = $res->fetch_assoc();
if(!$p) continue;

$price = round($p['price']);
$sub   = $price * $qty;
$total += $sub;
?>

<div class="cart-item">

    <?php
    $img = $p['image'] ?? '';
    if(empty($img)){
        $img = "assets/images/default.png";
    }else{
        $img = "assets/images/" . $img;
    }
    ?>

    <img src="/web2_softdrink/<?= $img ?>" 
         onerror="this.onerror=null;this.src='/web2_softdrink/assets/images/default.png'">

    <div class="cart-info">
        <div class="cart-name"><?= $p['name'] ?></div>

        <div class="cart-price">
            <?= number_format($price, 0, ',', '.') ?>đ
        </div>

        <div class="cart-qty">
            <button onclick="updateCart('decrease', <?= $id ?>)">-</button>
            <span><?= $qty ?></span>
            <button onclick="updateCart('increase', <?= $id ?>)">+</button>
        </div>
    </div>

    <div class="cart-remove" onclick="updateCart('remove', <?= $id ?>)">✖</div>
</div>

<?php endforeach; ?>

<div class="cart-total">
    <span>Tổng:</span>
    <span><?= number_format($total, 0, ',', '.') ?>đ</span>
</div>

<div class="cart-actions">
    <a href="index.php?page=checkout">THANH TOÁN</a>
</div>

<?php endif; ?>

</div>

<script>
// ===== TOAST =====
function toast(msg, type="success"){
    let t=document.createElement("div");
    t.className="toast";
    if(type==="error") t.classList.add("error");
    t.innerText=msg;
    document.body.appendChild(t);

    setTimeout(()=>t.classList.add("show"),10);
    setTimeout(()=>{
        t.classList.remove("show");
        setTimeout(()=>t.remove(),300);
    },1500);
}

// ===== RELOAD CART =====
function reloadCart(){
    fetch("component/cart.php?rand=" + Math.random())
    .then(res => res.text())
    .then(html => {
        let box = document.getElementById("cart-dropdown");
        if(box) box.innerHTML = html;
    });
}

// ===== UPDATE CART =====
function updateCart(type, id){

    fetch(`action/cart.php?type=${type}&id=${id}`)
    .then(res => res.json())
    .then(data => {

        // update số lượng icon
        let b=document.getElementById("cart-count");
        if(b) b.innerText=data.count || 0;

        // reload lại UI cart (FIX LỖI CHÍNH)
        reloadCart();

    })
    .catch(()=>{
        toast("❌ Lỗi cập nhật giỏ hàng!", "error");
    });
}
</script>