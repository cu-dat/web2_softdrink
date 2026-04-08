<?php
if(session_status() === PHP_SESSION_NONE){

    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_domain', $_SERVER['HTTP_HOST']); // 🔥 QUAN TRỌNG

    session_start();
}
require_once(__DIR__ . "/../admin/config/database.php");

$where = "WHERE 1";

if(!empty($_GET['category'])){
    $cats = array_map(function($c) use ($conn){
        return "'" . $conn->real_escape_string($c) . "'";
    }, $_GET['category']);

    $where .= " AND c.name IN (" . implode(",", $cats) . ")";
}

if(isset($_GET['min']) && $_GET['min'] !== ""){
    $where .= " AND p.price >= " . (int)$_GET['min'];
}

if(isset($_GET['max']) && $_GET['max'] !== ""){
    $where .= " AND p.price <= " . (int)$_GET['max'];
}

if(!empty($_GET['keyword'])){
    $kw = $conn->real_escape_string($_GET['keyword']);
    $where .= " AND p.name LIKE '%$kw%'";
}

$perPage = 5;
$pageNum = max(1, (int)($_GET['p'] ?? 1));

/* ===== FIX: thêm inventory ===== */
$countSql = "SELECT COUNT(*) as total 
FROM products p 
LEFT JOIN categories c ON p.category_id = c.id 
LEFT JOIN inventory i ON p.id = i.product_id
$where";

$totalProducts = $conn->query($countSql)->fetch_assoc()['total'];

$totalPages = max(1, ceil($totalProducts / $perPage));
$start = ($pageNum - 1) * $perPage;

/* ===== FIX: lấy stock ===== */
$sql = "SELECT p.*, c.name as category_name, IFNULL(i.stock,0) as stock
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN inventory i ON p.id = i.product_id
$where
ORDER BY p.id DESC
LIMIT $start, $perPage";

$result = $conn->query($sql);

$products = [];
while($row = $result->fetch_assoc()){
    $products[] = $row;
}
?>

<style>
.product-card{
    background:#fff;
    border-radius:10px;
    overflow:hidden;
    transition:0.3s;
    position:relative;
}
.product-card:hover{
    transform:translateY(-6px);
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}

/* ===== HẾT HÀNG ===== */
.product-disabled{
    opacity:0.5;
}
.product-disabled .btn-cart{
    background:#ccc !important;
    cursor:not-allowed;
}
.product-disabled::after{
    content:"HẾT HÀNG";
    position:absolute;
    top:10px;
    left:10px;
    background:red;
    color:#fff;
    padding:4px 8px;
    font-size:12px;
    border-radius:5px;
    z-index:10;
}

.product-img{
    position:relative;
    height:200px;
    display:flex;
    align-items:center;
    justify-content:center;
}
.product-img img{
    max-height:100%;
    object-fit:contain;
}

.overlay{
    position:absolute;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.35);
    display:flex;
    align-items:flex-end;
    justify-content:center;
    opacity:0;
    transition:0.3s;
}
.overlay a{
    color:#fff;
    margin-bottom:20px;
    text-decoration:none;
}
.product-img:hover .overlay{
    opacity:1;
}

.product-info{
    padding:15px;
    text-align:center;
}
.product-name{
    font-size:15px;
    font-weight:500;
    min-height:40px;
}
.product-price{
    color:#0f766e;
    font-size:20px;
    font-weight:bold;
    margin:10px 0;
}

.qty-box{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:6px;
    margin-bottom:10px;
}
.qty-btn{
    width:28px;
    height:28px;
    border:none;
    background:#eee;
    border-radius:6px;
    cursor:pointer;
}
.qty-btn:hover{background:#ddd;}

.qty-input{
    width:40px;
    text-align:center;
    border:1px solid #ccc;
    border-radius:6px;
}

.btn-cart{
    background:#cbb48b;
    border:none;
    padding:10px 20px;
    border-radius:6px;
    cursor:pointer;
}
.btn-cart:hover{background:#bda276;}

.toast{
    position:fixed;
    top:20px;
    right:20px;
    background:#16a34a;
    color:#fff;
    padding:12px 20px;
    border-radius:10px;
    z-index:9999;
    font-size:14px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
    opacity:0;
    transform:translateY(-20px);
    transition:all 0.3s ease;
}

.toast.show{
    opacity:1;
    transform:translateY(0);
}

.toast.error{
    background:#dc2626;
}

.toast.warn{
    background:#f59e0b;
}
</style>

<div class="row">

<?php foreach($products as $p): ?>
<?php
$img = $p['image'] ?? '';
$img = empty($img) ? "assets/images/default.png" : "assets/images/" . $img;

/* ===== LOGIC DUY NHẤT SỬA ===== */
$isOut = ($p['status'] == 0 || $p['stock'] <= 0);
?>

<div class="col-md-3 mb-4">
    <div class="product-card <?= $isOut ? 'product-disabled' : '' ?>">

        <div class="product-img">
            <img src="<?= $img ?>" onerror="this.src='assets/images/default.png'">
            <div class="overlay">
                <a href="index.php?page=detail&id=<?= $p['id'] ?>">QUICK VIEW</a>
            </div>
        </div>

        <div class="product-info">
            <div class="product-name"><?= $p['name'] ?></div>
            <div class="product-price"><?= number_format($p['price']) ?>đ</div>

            <div class="qty-box">
                <button class="qty-btn" onclick="changeQty(<?= $p['id'] ?>,-1)">-</button>
                <input id="qty-<?= $p['id'] ?>" value="1" class="qty-input">
                <button class="qty-btn" onclick="changeQty(<?= $p['id'] ?>,1)">+</button>
            </div>

            <?php if(!$isOut): ?> 
                <button class="btn-cart" onclick="addToCartList(event, <?= $p['id'] ?>)">🛒 Thêm</button>
            <?php else: ?>
                <button class="btn-cart" disabled>❌ Không bán</button>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php endforeach; ?>

</div>

<script>
function changeQty(id,n){
    let i=document.getElementById("qty-"+id);
    let v=parseInt(i.value)||1;
    v+=n;
    if(v<1)v=1;
    i.value=v;
}

function toast(msg, type="success"){
    let t = document.createElement("div");
    t.className = "toast";

    if(type === "error") t.classList.add("error");
    if(type === "warn") t.classList.add("warn");

    t.innerText = msg;

    document.body.appendChild(t);

    setTimeout(()=> t.classList.add("show"), 10);

    setTimeout(()=>{
        t.classList.remove("show");
        setTimeout(()=> t.remove(), 300);
    }, 2000);
}

function addToCartList(e, id){

    <?php if(empty($_SESSION['user'])): ?>
        toast("⚠️ Vui lòng đăng nhập!", "warn");
        return;
    <?php endif; ?>

    let btn = e.target;
    if(btn.hasAttribute("disabled")) return;

    let qty=document.getElementById("qty-"+id).value;

    fetch(`action/cart.php?type=add&id=${id}&qty=${qty}`)
    .then(r=>r.json())
    .then(d=>{
        let b=document.getElementById("cart-count");
        if(b) b.innerText=d.count || 0;

        if(typeof reloadCart==="function") reloadCart();

        toast("🛒 Đã thêm " + qty + " sản phẩm");
    })
    .catch(()=>{
        toast("❌ Lỗi thêm giỏ hàng!", "error");
    });
}
</script>