while ($item = $items->fetch_assoc()) {

    $pid = $item['product_id'];
    $qty = (int)$item['quantity'];

    $conn->query("
        UPDATE inventory 
        SET stock = stock - $qty
        WHERE product_id = $pid
    ");
}