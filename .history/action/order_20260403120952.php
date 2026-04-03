<?php
session_start();
require_once("../admin/config/database.php");

header('Content-Type: application/json');

// ===== CHECK LOGIN =====
$user = $_SESSION['user'] ?? null;
if(!$user){
    echo json_encode(["status"=>"not_login"]);
    exit;
}

$user_id = (int)$user['id'];

// ===== NHẬN DATA =====
$data = json_decode(file_get_contents("php://input"), true);

$name    = $conn->real_escape_string($data['name'] ?? '');
$phone   = $conn->real_escape_string($data['phone'] ?? '');
$address = $conn->real_escape_string($data['address'] ?? '');
$payment = $conn->real_escape_string($data['payment'] ?? '');

// ===== CART =====
$cart = $_SESSION['cart'] ?? [];

if(empty($cart)){
    echo json_encode(["status"=>"empty"]);
    exit;
}

// ===== TÍNH TOTAL & KIỂM TRA TỒN KHO =====
$total = 0;
$products = [];

foreach($cart as $id => $qty){
    $id = (int)$id;
    $qty = (int)$qty;

    // Lấy thông tin sản phẩm + tồn kho hiện tại
    $res = $conn->query("
        SELECT p.*, IFNULL(i.stock,0) as stock
        FROM products p
        LEFT JOIN inventory i ON p.id = i.product_id
        WHERE p.id = $id
    ");

    if(!$res) continue;

    $p = $res->fetch_assoc();
    if(!$p) continue;

    // Kiểm tra tồn kho (vẫn nên giữ để báo khách hàng nếu hết hàng)
    if($p['stock'] < $qty){
        echo json_encode([
            "status"=>"out_of_stock",
            "product"=>$p['name']
        ]);
        exit;
    }

    $subtotal = $p['price'] * $qty;
    $total += $subtotal;

    $products[] = [
        "id"=>$id,
        "qty"=>$qty,
        "price"=>$p['price'],
        "subtotal"=>$subtotal
    ];
}

// ===== START TRANSACTION =====
$conn->begin_transaction();

try{

    // ===== TẠO ORDER (status = 'pending') =====
    $conn->query("
        INSERT INTO orders(customer_id, total_amount, status, note)
        VALUES($user_id, $total, 'pending', '$payment')
    ");

    $order_id = $conn->insert_id;

    // ===== INSERT DETAILS (KHÔNG TRỪ KHO) =====
    foreach($products as $p){
        $id  = $p['id'];
        $qty = $p['qty'];

        $conn->query("
            INSERT INTO order_details(order_id, product_id, quantity, price, subtotal)
            VALUES($order_id, $id, $qty, {$p['price']}, {$p['subtotal']})
        ");

        // ✅ ĐÃ XÓA LỆNH UPDATE INVENTORY
        // Kho sẽ chỉ bị trừ khi admin xác nhận đơn (qua order_complete.php hoặc order_detail.php)
    }

    // ===== COMMIT =====
    $conn->commit();

    // ===== CLEAR CART =====
    unset($_SESSION['cart']);

    echo json_encode([
        "status"=>"success",
        "order_id"=>$order_id
    ]);

}catch(Exception $e){
    $conn->rollback();
    echo json_encode([
        "status"=>"error",
        "message"=>$e->getMessage()
    ]);
}