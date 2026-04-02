<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once("admin/config/database.php");

$user = $_SESSION['user'] ?? null;
if(!$user){
    echo "<script>window.location='index.php?page=login'</script>";
    exit;
}

$user_id = (int)$user['id'];

// ===== FILTER =====
$status = $_GET['status'] ?? '';

$where = "WHERE customer_id = $user_id";

if($status){
    $status = $conn->real_escape_string($status);
    $where .= " AND status = '$status'";
}

$result = $conn->query("
    SELECT * FROM orders 
    $where
    ORDER BY id DESC
");

// ===== MAP STATUS =====
$statusMap = [
    "pending" => "🕐 Đang xử lý",
    "confirmed" => "✅ Đã xác nhận",
    "completed" => "🎉 Hoàn thành",
    "cancelled" => "❌ Đã hủy"
];
?>

<style>
body{background:#f5f5f5;font-family:Arial;}

.order-container{max-width:900px;margin:30px auto;}

.order-filter{margin-bottom:20px;}

.order-filter a{
    padding:6px 12px;border-radius:6px;text-decoration:none;
    background:#eee;margin-right:5px;color:#000;
}

.order-filter a.active{background:#0f766e;color:#fff;}

.order-card{
    background:#fff;padding:15px;border-radius:10px;
    margin-bottom:15px;box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.order-header{
    display:flex;justify-content:space-between;
    margin-bottom:10px;font-weight:bold;
}

.order-items{border-top:1px solid #eee;padding-top:10px;}

.order-item{display:flex;justify-content:space-between;margin-bottom:5px;}

.status{font-weight:bold;}

.total{text-align:right;font-weight:bold;margin-top:10px;}

.btn-cancel{
    background:red;color:#fff;border:none;
    padding:6px 10px;border-radius:6px;cursor:pointer;
}

/* MODAL */
#cancelModal{
    display:none;position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.5);z-index:9999;
    justify-content:center;align-items:center;
}
.modal-box{
    background:#fff;padding:20px;border-radius:12px;width:300px;text-align:center;
}
.modal-actions{margin-top:15px;display:flex;justify-content:space-around;}
.modal-actions button{
    padding:8px 15px;border:none;border-radius:6px;cursor:pointer;
}
.btn-ok{background:#2563eb;color:#fff;}
.btn-close{background:#ccc;}

/* TOAST */
.toast{
    position:fixed;
    top:20px;
    right:20px;
    background:#16a34a;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    z-index:9999;
}
</style>

<div class="order-container">

<h3>📦 Đơn hàng của bạn</h3>

<div class="order-filter">
    <a href="index.php?page=order" class="<?= !$status ? 'active' : '' ?>">Tất cả</a>
    <a href="index.php?page=order&status=pending" class="<?= $status=='pending'?'active':'' ?>">Đang xử lý</a>
    <a href="index.php?page=order&status=confirmed" class="<?= $status=='confirmed'?'active':'' ?>">Đã xác nhận</a>
    <a href="index.php?page=order&status=completed" class="<?= $status=='completed'?'active':'' ?>">Hoàn thành</a>
    <a href="index.php?page=order&status=cancelled" class="<?= $status=='cancelled'?'active':'' ?>">Đã hủy</a>
</div>

<?php if(!$result || $result->num_rows == 0): ?>
<p>Không có đơn hàng</p>
<?php else: ?>

<?php while($order = $result->fetch_assoc()): ?>

<div class="order-card">

<div class="order-header">
    <div>Đơn #<?= $order['id'] ?></div>
    <div class="status">
        <?= $statusMap[strtolower(trim($order['status']))] ?? $order['status'] ?>
    </div>
</div>

<div>Ngày: <?= $order['created_at'] ?></div>

<div class="order-items">

<?php
$oid = (int)$order['id'];

$items = $conn->query("
    SELECT od.*, p.name 
    FROM order_details od
    JOIN products p ON p.id = od.product_id
    WHERE od.order_id = $oid
");
?>

<?php while($item = $items->fetch_assoc()): ?>
<div class="order-item">
    <div><?= $item['name'] ?> x <?= $item['quantity'] ?></div>
    <div><?= number_format($item['subtotal']) ?>đ</div>
</div>
<?php endwhile; ?>

</div>

<hr>

<div class="total">
    Tổng: <?= number_format($order['total_amount']) ?>đ
</div>

<?php if(strtolower(trim($order['status'])) == 'pending'): ?>
<button class="btn-cancel" onclick="cancelOrder(<?= $order['id'] ?>)">
    Hủy đơn
</button>
<?php endif; ?>

</div>

<?php endwhile; ?>
<?php endif; ?>

</div>

<!-- MODAL -->
<div id="cancelModal">
    <div class="modal-box">
        <h4>Xác nhận</h4>
        <p>Hủy đơn này?</p>
        <div class="modal-actions">
            <button class="btn-ok" onclick="confirmCancel()">OK</button>
            <button class="btn-close" onclick="closeModal()">Hủy</button>
        </div>
    </div>
</div>

<script>
let cancelId = null;

function toast(msg){
    let t=document.createElement("div");
    t.className="toast";
    t.innerText=msg;
    document.body.appendChild(t);
    setTimeout(()=>t.remove(),1500);
}

function cancelOrder(id){
    cancelId = id;
    document.getElementById("cancelModal").style.display = "flex";
}

function closeModal(){
    document.getElementById("cancelModal").style.display = "none";
    cancelId = null;
}

function confirmCancel(){
    if(!cancelId) return;

    fetch("/web2_softdrink/action/cancel-order.php?id=" + cancelId)
    .then(res => res.json())
    .then(data=>{
        if(data.status==="success"){
            toast("✅ Hủy thành công!");
            setTimeout(()=>location.reload(),1000);
        }else{
            toast("❌ " + (data.msg || "Lỗi"));
        }
    })
    .catch(err=>{
        toast("❌ Lỗi server!");
    });

    closeModal();
}
</script>