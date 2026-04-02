<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
<div class="container-fluid">

    <!-- Logo -->
    <a class="navbar-brand fw-bold" href="#">
        🥤 SoftDrink Admin
    </a>

    <!-- nút menu mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="navbarMenu">

        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage=='index.php'?'active':''; ?>" href="index.php">
                    📊 Bảng điều khiển
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo in_array($currentPage,['products.php','product_add.php','product_edit.php'])?'active':''; ?>" href="products.php">
                    🍹 Sản phẩm
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo in_array($currentPage,['orders.php','order_detail.php'])?'active':''; ?>" href="orders.php">
                    📦 Đơn hàng
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage=='customers.php'?'active':''; ?>" href="customers.php">
                    👥 Khách hàng
                </a>
            </li>

        </ul>

        <!-- Logout -->
        <a href="../admin/logout.php" class="btn btn-danger">
            🚪 Đăng xuất
        </a>

    </div>

</div>
</nav>