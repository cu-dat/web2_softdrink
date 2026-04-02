<?php
require_once(__DIR__ . "/../admin/config/database.php");

/* ===== LẤY ID ===== */
$id = (int)($_GET['id'] ?? 0);

/* ===== LẤY PRODUCT + STOCK ===== */
$sql = "SELECT p.*, IFNULL(i.stock,0) as stock
FROM products p
LEFT JOIN inventory i ON p.id = i.product_id
WHERE p.id = $id
LIMIT 1";

$result = $conn->query($sql);
$product = $result->fetch_assoc();

if(!$product){
    echo "<div class='alert alert-danger'>Không tìm thấy sản phẩm!</div>";
    return;
}
?>

<style>
.detail-box{
    background:#fff;
    padding:30px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
    display:flex;
    gap:40px;
    margin-top:20px; /* ✅ thêm cho đẹp */
}

.detail-img{
    flex:1;
    text-align:center;
}

.detail-img img{
    width:100%;
    max-width:350px;
    object-fit:contain;
    transition:0.3s;
}

.detail-img img:hover{
    transform:scale(1.05);
}

.detail-info{
    flex:1;
}

.title{
    font-size:28px;
    font-weight:bold;
}

.price{
    color:#0a7c66;
    font-size:26px;
    margin:15px 0;
}

.qty-box{
    display:flex;
    align-items:center;
    gap:10px;
    margin:20px 0;
}

.qty-btn{
    width:35px;
    height:35px;
    border:none;
    background:#ddd;
    font-size:18px;
    cursor:pointer;
}

.qty-input{
    width:60px;
    text-align:center;
}

.add-btn{
    background:#cbb48b;
    border:none;
    padding:12px 30px;
    font-weight:bold;
    cursor:pointer;
}

.add-btn:hover{
    background:#bda276;
}

.desc{
    margin-top:20px;
    color:#555;
}

.toast-custom{
    position:fixed;
    top:20px;
    right:20px;
    background:#16a34a;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    opacity:0;
    transform:translateX(100%);
    transition:0.3s;
    z-index:9999;
}
.toast-custom.show{
    opacity:1;
    transform:translateX(0);
}
</style>

<!-- ✅ FIX CHUẨN: BỌC CONTAINER GIỐNG HEADER/FOOTER -->

<div class="container-fluid px-5">

```
<div class="detail-box">

    <!-- IMAGE -->
    <div class="detail-img">

        <?php
        $img = $product['image'] ?? '';

        if(empty($img)){
            $img = "assets/images/default.png";
        }else{
            $img = "assets/images/" . $img;
        }
        ?>

        <img src="<?= $img ?>" 
             onerror="this.onerror=null;this.src='assets/images/default.png'">

    </div>

    <!-- INFO -->
    <div class="detail-info">

        <div class="title"><?= $product['name'] ?></div>

        <div class="text-muted"> #<?= $product['id'] ?></div>

        <div class="price">
            <?= number_format($product['price']) ?>đ
        </div>

        <?php
        $isOut = ($product['status'] == 0 || $product['stock'] <= 0);
        ?>

        <div class="qty-box">
            <span>Số lượng</span>

            <button class="qty-btn" onclick="changeQty(-1)">-</button>

            <input type="text" id="qty" value="1" class="qty-input">

            <button class="qty-btn" onclick="changeQty(1)">+</button>
        </div>

        <?php if(!$isOut): ?>
            <button class="add-btn" onclick="addToCart(<?= $product['id'] ?>)">
                🛒 Thêm vào giỏ
            </button>
        <?php else: ?>
            <button class="add-btn" disabled>
                ❌ Hết hàng
            </button>
        <?php endif; ?>

        <div class="desc">
            Nước giải khát giúp bù nước và năng lượng nhanh chóng.
        </div>

    </div>

</div>
```

</div>

<script>
function changeQty(n){
    let qty = document.getElementById("qty");
    let value = parseInt(qty.value) || 1;

    value += n;
    if(value < 1) value = 1;

    qty.value = value;
}

function showToast(msg){
    let t = document.createElement("div");
    t.className = "toast-custom";
    t.innerText = msg;

    document.body.appendChild(t);

    setTimeout(()=> t.classList.add("show"), 50);
    setTimeout(()=> t.remove(), 2000);
}

function addToCart(id){
    let qty = document.getElementById("qty").value;

    fetch(`action/cart.php?type=add&id=${id}&qty=${qty}`)
    .then(res => res.json())
    .then(data => {

        if(data.status === "not_login"){
            showToast("🔒 Vui lòng đăng nhập");
            setTimeout(()=> location.href="index.php?page=login", 1000);
            return;
        }

        let badge = document.getElementById("cart-count");
        if(badge){
            badge.innerText = data.count || 0;
        }

        if(typeof reloadCart === "function") reloadCart();

        showToast("🛒 +" + qty);
    });
}
</script>
