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
$sql = "
    SELECT * FROM orders 
    WHERE id = $id AND customer_id = $user_id
";
$res = $conn->query($sql);

if(!$res || $res->num_rows == 0){
    echo json_encode([
        "status"=>"error",
        "msg"=>"order_not_found"
    ]);
    exit;
}

$order = $res->fetch_assoc();

// ===== CHECK STATUS =====
$status = strtolower(trim($order['status']));

if($status !== 'pending'){
    echo json_encode([
        "status"=>"error",
        "msg"=>"not_pending",
        "current_status"=>$order['status']
    ]);
    exit;
}

// ===== LẤY ITEMS =====
$item_sql = "
    SELECT * FROM order_details 
    WHERE order_id = $id
";
$items = $conn->query($item_sql);

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

    // 🔄 HOÀN KHO (FIX: inventory)
    while($item = $items->fetch_assoc()){
        $pid = (int)$item['product_id'];
        $qty = (int)$item['quantity'];

        $updateStock = "
            UPDATE inventory
            SET stock = stock + $qty
            WHERE product_id = $pid
        ";

        if(!$conn->query($updateStock)){
            throw new Exception("update_stock_failed");
        }
    }

    // ===== UPDATE STATUS =====
    $updateOrder = "
        UPDATE orders 
        SET status = 'cancelled'
        WHERE id = $id
    ";

    if(!$conn->query($updateOrder)){
        throw new Exception("update_order_failed");
    }

    $conn->commit();

    echo json_encode([
        "status"=>"success"
    ]);
    exit;

}catch(Exception $e){

    $conn->rollback();

    echo json_encode([
        "status"=>"error",
        "msg"=>$e->getMessage()
    ]);
    exit;
}