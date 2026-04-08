<?php
// lấy tất cả sản phẩm trong phiếu nhập
$items = $conn->query("
    SELECT product_id, quantity, import_price 
    FROM import_details 
    WHERE import_id = $import_id
");

while ($item = $items->fetch_assoc()) {

    $product_id = $item['product_id'];
    $qty_new    = $item['quantity'];
    $price_new  = $item['import_price'];

    // lấy tồn kho hiện tại + giá nhập hiện tại
    $p = $conn->query("
        SELECT stock_quantity, cost_price, profit_margin 
        FROM products 
        WHERE id = $product_id
    ")->fetch_assoc();

    $qty_old   = $p['stock_quantity'];
    $price_old = $p['cost_price'];

    // ===== TÍNH GIÁ BÌNH QUÂN =====
    if ($qty_old + $qty_new > 0) {
        $avg_price = (
            ($qty_old * $price_old) + ($qty_new * $price_new)
        ) / ($qty_old + $qty_new);
    } else {
        $avg_price = $price_new;
    }

    // ===== TÍNH GIÁ BÁN =====
    $margin = $p['profit_margin'];
    $selling_price = $avg_price * (1 + $margin / 100);

    // ===== UPDATE =====
    $stmt = $conn->prepare("
        UPDATE products 
        SET 
            cost_price = ?, 
            price = ?, 
            stock_quantity = stock_quantity + ?
        WHERE id = ?
    ");

    $stmt->bind_param("ddii", $avg_price, $selling_price, $qty_new, $product_id);
    $stmt->execute();
}