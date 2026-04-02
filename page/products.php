<?php
$products = require(__DIR__ . "/../data/products_db.php");

// ===== FILTER =====
$filtered = [];

foreach($products as $item){

    if(isset($_GET['category_id']) && !empty($_GET['category_id'])){
        if(!in_array($item['category_id'], $_GET['category_id'])){
            continue;
        }
    }

    if(isset($_GET['min']) && $_GET['min'] !== ''){
        if($item['price'] < (int)$_GET['min']) continue;
    }

    if(isset($_GET['max']) && $_GET['max'] !== ''){
        if($item['price'] > (int)$_GET['max']) continue;
    }

    if(isset($_GET['keyword']) && $_GET['keyword'] !== ''){
        if(stripos($item['name'], $_GET['keyword']) === false){
            continue;
        }
    }

    $filtered[] = $item;
}

// ===== PAGINATION =====
$perPage = 5;

$pageNum = $_GET['p'] ?? 1;
$pageNum = max(1, (int)$pageNum);

$totalProducts = count($filtered);
$totalPages = max(1, ceil($totalProducts / $perPage));

$start = ($pageNum - 1) * $perPage;

if($start >= $totalProducts){
    $pageNum = 1;
    $start = 0;
}

// CẮT DATA
$products = array_slice($filtered, $start, $perPage);
?>

<div class="row">

    <div class="col-md-3">
        <?php include(__DIR__ . "/../component/product-toolbar.php"); ?>
    </div>

    <div class="col-md-9">

        <h3>Thế giới thức uống</h3>

        <?php include(__DIR__ . "/../component/product-list.php"); ?>

        <!-- ===== PAGINATION ===== -->
        <div style="text-align:center;margin-top:20px;">

            <div>Trang <?= $pageNum ?> / <?= $totalPages ?></div>

            <!-- PREV -->
            <?php if($pageNum > 1): ?>
                <a href="index.php?page=products&p=<?= $i ?>">«</a>
            <?php endif; ?>

            <!-- NUMBER -->
            <?php for($i=1;$i<=$totalPages;$i++): ?>
                <a href="index.php?page=product&p=<?= $i ?>"
                   style="
                        margin:0 5px;
                        padding:6px 10px;
                        background:<?= ($i==$pageNum)?'#000':'#eee' ?>;
                        color:<?= ($i==$pageNum)?'#fff':'#000' ?>;
                        text-decoration:none;
                        border-radius:5px;
                   ">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <!-- NEXT -->
            <?php if($pageNum < $totalPages): ?>
                <a href="index.php?page=product&p=<?= $pageNum+1 ?>">»</a>
            <?php endif; ?>

        </div>

    </div>

</div>