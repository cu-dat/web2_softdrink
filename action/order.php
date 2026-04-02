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

// ===== TÍNH TOTAL =====
$total = 0;
$products = [];

foreach($cart as $id => $qty){
    $id = (int)$id;
    $qty = (int)$qty;

    $res = $conn->query("SELECT * FROM products WHERE id=$id");
    if(!$res) continue;

    $p = $res->fetch_assoc();
    if(!$p) continue;

    // 🚨 CHECK KHO NGAY TỪ ĐẦU
    if($p['stock_quantity'] < $qty){
        echo json_encode([
            "status"=>"out_of_stock",
            "product"=>$p['name']
        ]);
        exit;
    }

    $subtotal = $p['price'] * $qty;
    $total += $subtotal;

    // lưu tạm để xử lý sau
    $products[] = [
        "id"=>$id,
        "qty"=>$qty,
        "price"=>$p['price'],
        "subtotal"=>$subtotal
    ];
}

// ===== START TRANSACTION (QUAN TRỌNG) =====
$conn->begin_transaction();

try{

    // ===== TẠO ORDER =====
    $conn->query("
        INSERT INTO orders(customer_id, total_amount, status, note)
        VALUES($user_id, $total, 'pending', '$payment')
    ");

    $order_id = $conn->insert_id;

    // ===== INSERT DETAILS + TRỪ KHO =====
    foreach($products as $p){

        $id  = $p['id'];
        $qty = $p['qty'];

        // insert detail
        $conn->query("
            INSERT INTO order_details(order_id, product_id, quantity, price, subtotal)
            VALUES($order_id, $id, $qty, {$p['price']}, {$p['subtotal']})
        ");

        // 🔥 TRỪ KHO
        $conn->query("
            UPDATE products 
            SET stock_quantity = stock_quantity - $qty
            WHERE id = $id
        ");
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

    // rollback nếu lỗi
    $conn->rollback();

    echo json_encode([
        "status"=>"error",
        "message"=>$e->getMessage()
    ]);
}