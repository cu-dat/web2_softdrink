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

$products = array_slice($filtered, $start, $perPage);
?>

<div class="row">

    <div class="col-md-3">
        <?php include(__DIR__ . "/../component/product-toolbar.php"); ?>
    </div>

    <div class="col-md-9">

        <h3>Thế Giới Uống Nước</h3>

        <?php if(!empty($_GET['keyword'])): ?>
            <p>Kết quả cho: <b>"<?= htmlspecialchars($_GET['keyword']) ?>"</b></p>
        <?php endif; ?>

        <?php if(empty($filtered)): ?>

            <div class="alert alert-warning">
                ❌ Không có sản phẩm nào phù hợp!
            </div>

        <?php else: ?>

            <?php include(__DIR__ . "/../component/product-list.php"); ?>

        <?php endif; ?>

        <!-- ===== PAGINATION ===== -->
        <?php if($totalPages > 1): ?>
        <div style="text-align:center;margin:40px 0;">

            <div style="margin-bottom:10px;">
                Trang <?= $pageNum ?> / <?= $totalPages ?>
            </div>

            <?php $query = $_GET; ?>

            <!-- PREV -->
            <?php if($pageNum > 1): ?>
                <?php $query['p'] = $pageNum - 1; ?>
                <a href="index.php?<?= http_build_query($query) ?>">«</a>
            <?php endif; ?>

            <!-- NUMBER -->
            <?php for($i=1;$i<=$totalPages;$i++): ?>
                <?php $query['p'] = $i; ?>
                <a href="index.php?<?= http_build_query($query) ?>"
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
                <?php $query['p'] = $pageNum + 1; ?>
                <a href="index.php?<?= http_build_query($query) ?>">»</a>
            <?php endif; ?>

        </div>
        <?php endif; ?>

    </div>

</div>