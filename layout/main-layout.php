<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Soft Drink</title>

<!-- BOOTSTRAP -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<!-- CSS RIÊNG -->
<link href="/web2_softdrink/assets/style.css" rel="stylesheet">

<style>
/* FIX KHOẢNG TRẮNG */
body{
    margin:0;
    padding:0;
}

.navbar{
    margin-bottom:0 !important;
}

#bannerCarousel{
    margin-top:0 !important;
}

/* 🔥 quan trọng */
.no-gap{
    margin-top:0 !important;
    padding-top:0 !important;
}
</style>

</head>

<body>

<!-- HEADER -->
<?php include(__DIR__ . "/../includes/header.php"); ?>

<!-- CONTENT -->
<div class="container no-gap">
    <?php include(__DIR__ . "/../" . $page); ?>
</div>

<?php include(__DIR__ . "/../includes/footer.php"); ?>
<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function updateCart(type, id){
    fetch(`action/cart.php?type=${type}&id=${id}`)
    .then(res => res.json())
    .then(() => {
        updateCartCount();
        reloadCart();
    });
}

function updateCartCount(){
    fetch("action/cart.php")
    .then(res => res.json())
    .then(data => {
        let total = 0;
        for(let i in data.cart){
            total += data.cart[i];
        }

        let badge = document.getElementById("cart-count");
        if(badge){
            badge.innerText = total;
        }
    });
}

function reloadCart(){
    fetch("component/cart.php")
    .then(res => res.text())
    .then(html => {
        document.getElementById("cartContainer").innerHTML = html;
    });
}

updateCartCount();
</script>

</body>
</html>