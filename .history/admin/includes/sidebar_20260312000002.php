<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
    <div class="logo">
        <h2>🥤 SoftDrink</h2>
        <small>Admin Panel</small>
    </div>
    <nav>
        <ul>
            <li>
                <a href="index.php" class="<?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                    <span class="icon">📊</span> Dashboard
                </a>
            </li>
            <li>
                <a href="categories.php" class="<?php echo in_array($currentPage, ['categories.php','category_add.php','category_edit.php']) ? 'active' : ''; ?>">
                    <span class="icon">📁</span> Categories
                </a>
            </li>
            <li>
                <a href="products.php" class="<?php echo in_array($currentPage, ['products.php','product_add.php','product_edit.php']) ? 'active' : ''; ?>">
                    <span class="icon">🍹</span> Products
                </a>
            </li>
            <li>
                <a href="orders.php" class="<?php echo in_array($currentPage, ['orders.php','order_detail.php']) ? 'active' : ''; ?>">
                    <span class="icon">📦</span> Orders
                </a>
            </li>
            <li>
                <a href="customers.php" class="<?php echo $currentPage == 'customers.php' ? 'active' : ''; ?>">
                    <span class="icon">👥</span> Customers
                </a>
            </li>
            <li>
                <a href="/logout.php">
                    <span class="icon">🚪</span> Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>