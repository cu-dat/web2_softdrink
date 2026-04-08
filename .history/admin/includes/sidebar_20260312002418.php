<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<aside class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white vh-100" style="width:250px;">

    <!-- Logo -->
    <div class="text-center mb-4 border-bottom pb-3">
        <h4 class="mb-0">🥤 SoftDrink</h4>
        <small class="text-secondary">Trang quản trị</small>
    </div>

    <!-- Menu -->
    <ul class="nav nav-pills flex-column mb-auto">

        <li class="nav-item mb-1">
            <a href="index.php"
               class="nav-link text-white <?php echo $currentPage == 'index.php' ? 'active bg-primary' : ''; ?>">
                📊 Bảng điều khiển
            </a>
        </li>

        <li class="mb-1">
            <a href="categories.php"
               class="nav-link text-white <?php echo in_array($currentPage,['categories.php','category_add.php','category_edit.php']) ? 'active bg-primary' : ''; ?>">
                📁 Danh mục
            </a>
        </li>

        <li class="mb-1">
            <a href="products.php"
               class="nav-link text-white <?php echo in_array($currentPage,['products.php','product_add.php','product_edit.php']) ? 'active bg-primary' : ''; ?>">
                🍹 Sản phẩm
            </a>
        </li>

        <li class="mb-1">
            <a href="orders.php"
               class="nav-link text-white <?php echo in_array($currentPage,['orders.php','order_detail.php']) ? 'active bg-primary' : ''; ?>">
                📦 Đơn hàng
            </a>
        </li>

        <li class="mb-1">
            <a href="customers.php"
               class="nav-link text-white <?php echo $currentPage == 'customers.php' ? 'active bg-primary' : ''; ?>">
                👥 Khách hàng
            </a>
        </li>

    </ul>

    <hr>

    <!-- Logout -->
    <a href="../admin/auth/logout.php" class="btn btn-danger w-100">
        🚪 Đăng xuất
    </a>

</aside>