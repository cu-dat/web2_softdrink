<?php
if(session_status() === PHP_SESSION_NONE){

    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_domain', $_SERVER['HTTP_HOST']); // 🔥 QUAN TRỌNG

    session_start();
}
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

/* ===== 3 SẢN PHẨM KHÁC ===== */
$randomProducts = $conn->query("SELECT * FROM products WHERE id != $id ORDER BY RAND() LIMIT 3");
?>

<style>
.main-layout{
    display:flex;
    gap:30px;
}

.left{ flex:3; }
.right{ flex:1; }

/* ===== PRODUCT ===== */
.detail-box{
    background:#f8f8f8;
    padding:40px;
    border-radius:15px;
    display:flex;
    gap:60px;
    align-items:center;
}

.detail-img img{
    max-width:280px;
    transition:0.3s;
}
.detail-img img:hover{
    transform:scale(1.08);
}

.title{
    font-size:32px;
    font-weight:700;
}

.price{
    color:#0a7c66;
    font-size:28px;
    font-weight:bold;
    margin:20px 0;
}

.qty-box{
    display:flex;
    gap:10px;
    align-items:center;
    margin-bottom:20px;
}

.qty-btn{
    width:40px;
    height:40px;
    border:none;
    background:#ddd;
    cursor:pointer;
}

.qty-input{
    width:60px;
    height:40px;
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

/* ===== FEATURED ===== */
.feature-box{
    background:#fff;
    padding:20px;
    border-radius:15px;
}

.feature-title{
    font-size:20px;
    font-weight:bold;
    margin-bottom:15px;
}

.feature-item{
    display:flex;
    gap:15px;
    align-items:center;
    margin-bottom:15px;
    padding:10px;
    border-radius:10px;
    transition:0.2s;
}

.feature-item:hover{
    background:#f3f3f3;
    transform:translateX(5px);
}

.feature-item img{
    width:70px;
    height:70px;
    object-fit:contain;
}

.feature-price{
    color:#0a7c66;
}

/* ===== DETAILS ===== */
.details-box{
    margin-top:30px;
    background:#f8f8f8;
    padding:30px;
    border-radius:15px;
}

/* ===== TOAST ===== */
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

<div class="container-fluid px-5">

<div class="main-layout">

    <!-- LEFT -->
    <div class="left">

        <div class="detail-box">

            <!-- IMAGE -->
            <div class="detail-img">
                <?php
                $img = !empty($product['image']) 
                    ? "assets/images/".$product['image'] 
                    : "assets/images/default.png";
                ?>
                <img src="<?= $img ?>" 
                     onerror="this.onerror=null;this.src='assets/images/default.png'">
            </div>

            <!-- INFO -->
            <div>

                <div class="title"><?= $product['name'] ?></div>
                <div>#<?= $product['id'] ?></div>

                <div class="price">
                    <?= number_format($product['price']) ?>đ
                </div>

                <?php
                $isOut = ($product['status'] == 0 || $product['stock'] <= 0);
                ?>

                <div class="qty-box">
                    <span>Số lượng</span>

                    <button class="qty-btn" onclick="changeQty(-1)">-</button>
                    <input id="qty" value="1" class="qty-input">
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

                <div style="margin-top:15px;">
                    Nước giải khát giúp bù nước và năng lượng nhanh chóng.
                </div>

            </div>

        </div>

        <!-- DETAILS -->
        <div class="details-box">
            <h4>Mô Tả</h4>
            <div>
                <?= nl2br($product['description'] ?? 'Chưa có mô tả sản phẩm') ?>
            </div>
        </div>

    </div>

    <!-- RIGHT -->
    <div class="right">

        <div class="feature-box">

            <div class="feature-title">Featured Products</div>

            <?php while($item = $randomProducts->fetch_assoc()): 
                $img = !empty($item['image']) 
                    ? "assets/images/".$item['image'] 
                    : "assets/images/default.png";
            ?>

              <a href="index.php?page=detail&id=<?= $item['id'] ?>" 
                   style="text-decoration:none; color:inherit;">


                <div class="feature-item">

                    <img src="<?= $img ?>"  
                         onerror="this.onerror=null;this.src='assets/images/default.png'">

                    <div>
                        <div><?= $item['name'] ?></div>
                        <div class="feature-price">
                            <?= number_format($item['price']) ?>đ
                        </div>
                    </div>

                </div>

            </a>

            <?php endwhile; ?>

        </div>

    </div>

</div>

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

        toast("🛒 Đã thêm " + qty + " sản phẩm");
    });
}
</script>