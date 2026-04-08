<?php
session_start();
require_once("../admin/config/database.php");

header('Content-Type: application/json');

// ===== CHECK LOGIN =====
if(!isset($_SESSION['user'])){
    echo json_encode([
        "status"=>"error",
        "msg"=>"not_login"
    ]);
    exit;
}

$user_id = (int)$_SESSION['user']['id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id <= 0){
    echo json_encode([
        "status"=>"error",
        "msg"=>"invalid_id"
    ]);
    exit;
}

// ===== CHECK ORDER =====
$res = $conn->query("
    SELECT * FROM orders 
    WHERE id = $id AND customer_id = $user_id
");

if(!$res || $res->num_rows == 0){
    echo json_encode([
        "status"=>"error",
        "msg"=>"order_not_found"
    ]);
    exit;
}

$order = $res->fetch_assoc();
$status = strtolower(trim($order['status']));

// ❌ đã cancel rồi thì không làm nữa
if($status === 'cancelled'){
    echo json_encode([
        "status"=>"error",
        "msg"=>"already_cancelled"
    ]);
    exit;
}

// ===== LẤY ITEMS =====
$items = $conn->query("
    SELECT product_id, quantity 
    FROM order_details 
    WHERE order_id = $id
");

if(!$items){
    echo json_encode([
        "status"=>"error",
        "msg"=>"cannot_get_items"
    ]);
    exit;
}

// ===== TRANSACTION =====
$conn->begin_transaction();

try{

    // ✅ nếu đã confirm → hoàn kho
    if($status === 'confirmed'){

        while($item = $items->fetch_assoc()){
            $pid = (int)$item['product_id'];
            $qty = (int)$item['quantity'];

            $conn->query("
                UPDATE inventory
                SET stock = stock + $qty
                WHERE product_id = $pid
            ");
        }
    }

    // ===== UPDATE STATUS =====
    $conn->query("
        UPDATE orders 
        SET status = 'cancelled'
        WHERE id = $id
    ");

    $conn->commit();

    echo json_encode([
        "status"=>"success"
    ]);

}catch(Exception $e){

    $conn->rollback();

    echo json_encode([
        "status"=>"error",
        "msg"=>$e->getMessage()
    ]);
}