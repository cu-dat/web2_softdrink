<?php
$products = include(__DIR__ . "/../data/products_db.php");
if(!is_array($products)) $products = [];
?>

<!-- ===== BANNER ===== -->
<div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">

    <div class="carousel-inner">

        <div class="carousel-item active">
            <img src="img/5ca3961b1e6e9abceba6b5ff743d1188.jpg" class="d-block w-100">
        </div>

        <div class="carousel-item">
            <img src="img/banner-fanta-01-2048x683.png" class="d-block w-100">
        </div>

        <div class="carousel-item">
            <img src="img/BANNER-COCACOCA-1810x602px-01-1024x341.png" class="d-block w-100">
        </div>

    </div>

    <!-- BUTTON -->
    <button class="carousel-control-prev" data-bs-target="#bannerCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" data-bs-target="#bannerCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

</div>

<!-- ===== PRODUCT ===== -->
<div class="container mt-5">

    <h4 class="mb-4 section-title">SẢN PHẨM NỔI BẬT</h4>

    <?php
    $products = array_slice($products, 0, 8);
    include(__DIR__ . "/../component/product-list.php");
    ?>

</div>

<style>
.section-title{
    font-weight:bold;
    border-left:5px solid #16a34a;
    padding-left:10px;
}

/* FIX BANNER */
#bannerCarousel img{
    height:400px;
    object-fit:cover;
    border-radius:10px;
}
</style>