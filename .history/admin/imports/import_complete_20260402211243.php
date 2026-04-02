    <?php
    require_once '../config/database.php';

    $import_id = $_GET['id'] ?? 0;

    // ===== CHECK PHIẾU =====
    $check = $conn->query("SELECT status FROM imports WHERE id = $import_id")->fetch_assoc();

    if (!$check) {
        die("Phiếu không tồn tại!");
    }

    if ($check['status'] === 'completed') {
        die("Phiếu đã hoàn thành!");
    }

    // ===== HÀM TÍNH AVG COST =====
    function calculateAvgCost($qty_old, $cost_old, $qty_new, $cost_new)
    {
        if ($qty_old <= 0) {
            return $cost_new;
        }

        $avg = (
            ($qty_old * $cost_old) + ($qty_new * $cost_new)
        ) / ($qty_old + $qty_new);

        return round($avg);
    }

    // ===== LẤY DANH SÁCH NHẬP =====
    $items = $conn->query("
        SELECT * FROM import_details 
        WHERE import_id = $import_id
    ");

 while ($item = $items->fetch_assoc()) {

    $pid = $item['product_id'];
    $qty_new = (int)$item['quantity'];
    $cost_new = (float)$item['import_price'];

    // ===== LẤY DỮ LIỆU CŨ =====
    $p = $conn->query("
        SELECT cost_price, profit_margin 
        FROM products 
        WHERE id = $pid
    ")->fetch_assoc();

    $cost_old = (float)$p['cost_price'];
    $margin = (float)$p['profit_margin'];

    // ===== LẤY TỒN KHO CŨ =====
    $inv = $conn->query("
        SELECT stock FROM inventory 
        WHERE product_id = $pid
    ")->fetch_assoc();

    $qty_old = (int)($inv['stock'] ?? 0);

    // ===== TÍNH AVG COST =====
    $cost_avg = calculateAvgCost($qty_old, $cost_old, $qty_new, $cost_new);

    // ===== TÍNH GIÁ BÁN =====
    $price = $cost_avg + ($cost_avg * $margin / 100);

    // ===== UPDATE PRODUCTS =====
    $stmt = $conn->prepare("
        UPDATE products 
        SET 
            cost_price = ?, 
            price = ?
        WHERE id = ?
    ");

    $stmt->bind_param("ddi", $cost_avg, $price, $pid);
    $stmt->execute();

    // ===== UPDATE KHO =====
    $stmt2 = $conn->prepare("
        INSERT INTO inventory (product_id, stock)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)
    ");

    $stmt2->bind_param("ii", $pid, $qty_new);
    $stmt2->execute();
}
}

    // ===== UPDATE TRẠNG THÁI PHIẾU =====
    $conn->query("
        UPDATE imports 
        SET status = 'completed' 
        WHERE id = $import_id
    ");

    header("Location: import.php");
    exit();
