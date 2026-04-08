$id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT o.*, u.full_name, u.address
    FROM orders o
    LEFT JOIN users u ON o.customer_id = u.id
    WHERE o.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();