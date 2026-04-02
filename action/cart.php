<?php
ini_set('session.cookie_path', '/');
session_start();

$type = $_GET['type'] ?? '';
$id   = (int)($_GET['id'] ?? 0);
$qty  = max(1, (int)($_GET['qty'] ?? 1));

$user_id = $_SESSION['user']['id'] ?? 0;

// ===== CHECK LOGIN =====
if($type == "check_login"){
    echo json_encode([
        "logged" => $user_id ? true : false
    ]);
    exit;
}

// ===== COUNT (KHÔNG CẦN ID) =====
if($type == "count"){
    echo json_encode([
        "count" => array_sum($_SESSION['cart'] ?? [])
    ]);
    exit;
}

// ❌ CHƯA LOGIN
if(!$user_id){
    echo json_encode(["status"=>"not_login"]);
    exit;
}

// ===== CHẶN ID LỖI (CHỈ ÁP DỤNG ACTION) =====
if(in_array($type, ["add","increase","decrease","remove"]) && $id <= 0){
    echo json_encode([
        "status" => "error",
        "message" => "invalid_id"
    ]);
    exit;
}

// ===== INIT CART =====
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

// ===== ACTION =====
if($type == "add"){
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
}

if($type == "increase"){
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}

if($type == "decrease"){
    if(isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id]--;
        if($_SESSION['cart'][$id] <= 0){
            unset($_SESSION['cart'][$id]);
        }
    }
}

if($type == "remove"){
    unset($_SESSION['cart'][$id]);
}

// ===== RESPONSE =====
echo json_encode([
    "status" => "success",
    "count"  => array_sum($_SESSION['cart'])
]);