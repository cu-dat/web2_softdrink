<?php
require_once '../includes/functions.php';
require_once '../config/database.php';

requireAdmin($conn);

// ===== FILTER =====
$from   = $_GET['from'] ?? '';
$to     = $_GET['to'] ?? '';
$status = $_GET['status'] ?? '';
$sort   = $_GET['sort'] ?? 'desc';

// ===== QUERY =====
$sql = "
SELECT o.*, u.full_name, u.address
FROM orders o
LEFT JOIN users u ON o.customer_id = u.id
WHERE 1
";

// lọc theo ngày
if ($from && $to) {
    $sql .= " AND DATE(o.created_at) BETWEEN '$from' AND '$to'";
}

// lọc theo trạng thái
if ($status) {
    $sql .= " AND o.status = '$status'";
}

// sắp xếp theo địa chỉ
if ($sort === 'address') {
    $sql .= " ORDER BY u.address ASC";
} else {
    $sql .= " ORDER BY o.created_at DESC";
}

$result = $conn->query($sql);
?>