<?php

// lấy page
$pageKey = $_GET['page'] ?? 'home';

// chuẩn hoá (fix lỗi product / products)
if($pageKey == 'product'){
    $pageKey = 'products';
}

// router
switch($pageKey){

    case 'products':
        $page = "page/products.php";
        break;

    case 'detail':
        $page = "component/product-detail.php";
        break;

    case 'login':
        $page = "page/login.php";
        break;

    case 'register':
        $page = "page/register.php";
        break;
    case 'forgot_password':
        $page = "page/forgot_password.php";
        break;

    case 'checkout':
        $page = "page/checkout.php";
        break;

    case 'cart':
        $page = "page/cart.php";
        break;

    case 'profile':
        $page = "page/profile.php";
        break;

    case 'order':
        $page = "page/order.php";
        break;

    default:
        $page = "page/home.php";
        break;
}

// include layout
include(__DIR__ . "/layout/main-layout.php");