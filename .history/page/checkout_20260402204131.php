<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once(__DIR__ . "/../admin/config/database.php");

// 🔒 check login
$user = $_SESSION['user'] ?? null;

if(!$user){
    header("Location: index.php?page=login");
    exit;
}

// 🔥 lấy thông tin user từ DB
$user_id = (int)$user['id'];
$resUser = $conn->query("SELECT * FROM users WHERE id=$user_id");
$user_db = $resUser ? $resUser->fetch_assoc() : [];

// 🔥 cart
$cart = $_SESSION['cart'] ?? [];

// lọc cart lỗi
foreach($cart as $id => $qty){
    if($qty <= 0){
        unset($_SESSION['cart'][$id]);
    }
}

$total = 0;
?>

<style>
body{background:#f5f5f5;font-family:Arial;}

.checkout-container{
    max-width:1100px;
    margin:40px auto;
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
}

.box{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.title{font-size:20px;font-weight:bold;margin-bottom:15px;}

.input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    border:1px solid #ddd;
    border-radius:6px;
}

.payment label{display:block;margin-bottom:8px;}

.summary-item{
    display:flex;
    justify-content:space-between;
    margin-bottom:10px;
}

.total{font-size:18px;font-weight:bold;}

.btn{
    width:100%;
    padding:12px;
    background:#0f766e;
    color:#fff;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-weight:bold;
}

.btn:hover{background:#115e59;}

#qr-box{
    display:none;
    text-align:center;
    margin-top:15px;
}

.toast{
    position:fixed;
    top:20px;
    right:20px;
    background:#16a34a;
    color:#fff;
    padding:12px 20px;
    border-radius:8px;
    opacity:0;
    transition:0.3s;
}
</style>

<div class="checkout-container">

<!-- LEFT -->
<div class="box">

<div class="title">📦 Thông tin giao hàng</div>

<input class="input" id="name"
       value="<?= htmlspecialchars($user_db['full_name'] ?? '') ?>"
       placeholder="Họ tên">

<input class="input" id="phone"
       value="<?= htmlspecialchars($user_db['phone'] ?? '') ?>"
       placeholder="Số điện thoại">

<textarea class="input" id="address"
          placeholder="Địa chỉ"><?= htmlspecialchars($user_db['address'] ?? '') ?></textarea>

<div class="title">💳 Thanh toán</div>

<div class="payment">
    <label><input type="radio" name="pay" value="cod" checked> Thanh toán khi nhận hàng</label>
    <label><input type="radio" name="pay" value="bank"> Chuyển khoản</label>
</div>

<!-- 🔥 QR -->
<div id="qr-box">
    <p><b>Quét mã để thanh toán</b></p>

    <img id="qr-img" src="" style="width:200px">

    <p style="font-size:14px;">
        Ngân hàng: <b>MB Bank</b><br>
        STK: <b>123456789</b><br>
        Chủ TK: <b>GIA HUNG</b>
    </p>
</div>

</div>

<!-- RIGHT -->
<div class="box">

<div class="title">🛒 Đơn hàng</div>

<?php if(empty($cart)): ?>
    <div>🛒 Giỏ hàng trống</div>
<?php else: ?>

<?php foreach($cart as $id => $qty): ?>

<?php
$id = (int)$id;
$res = $conn->query("SELECT * FROM products WHERE id=$id");

if(!$res) continue;
$p = $res->fetch_assoc();
if(!$p) continue;

$sub = $p['price'] * $qty;
$total += $sub;
?>

<div class="summary-item">
    <span><?= $p['name'] ?> x<?= $qty ?></span>
    <span><?= number_format($sub) ?>đ</span>
</div>

<?php endforeach; ?>

<hr>

<div class="summary-item total">
    <span>Tổng</span>
    <span id="total"><?= number_format($total) ?></span>
</div>

<button class="btn" onclick="placeOrder()">Đặt hàng</button>

<?php endif; ?>

</div>

</div>

<script>
// 🔥 toast
function showToast(msg){
    let t = document.createElement("div");
    t.className = "toast";
    t.innerText = msg;
    document.body.appendChild(t);

    setTimeout(()=> t.style.opacity = "1", 10);
    setTimeout(()=> t.remove(), 2000);
}

// 🔥 QR update
function updateQR(){
    let total = <?= (int)$total ?>;
    let img = document.getElementById("qr-img");

    img.src = "https://img.vietqr.io/image/MB-123456789-compact.png?amount="
              + total + "&addInfo=ThanhToan";
}

// 🔥 show QR
document.querySelectorAll('input[name="pay"]').forEach(radio => {
    radio.addEventListener('change', function(){

        let qr = document.getElementById("qr-box");

        if(this.value === "bank"){
            qr.style.display = "block";
            updateQR();
        }else{
            qr.style.display = "none";
        }

    });
});

// 🔥 order
function placeOrder(){

    let data = {
        name: document.getElementById("name").value,
        phone: document.getElementById("phone").value,
        address: document.getElementById("address").value,
        payment: document.querySelector('input[name="pay"]:checked').value
    };

    if(!data.name || !data.phone || !data.address){
        showToast("❌ Vui lòng nhập đầy đủ thông tin!");
        return;
    }

    fetch("/web2_softdrink/action/order.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {

        if(data.status === "success"){
            showToast("🎉 Đặt hàng thành công!");

            setTimeout(()=>{
               window.location.href = "index.php?page=orders";
            }, 1500);
        }else{
            showToast("❌ Lỗi đặt hàng!");
        }

    })
    .catch(()=>{
        showToast("❌ Lỗi server!");
    });
}
</script>