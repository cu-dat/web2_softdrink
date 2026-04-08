// lấy user trước
$stmt = $conn->prepare("SELECT id, role FROM admin_users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    header("Location: admin_list.php?msg=notfound");
    exit();
}

// ❌ không reset chính mình
if ($id == $_SESSION['admin_id']) {
    header("Location: admin_list.php?msg=self");
    exit();
}

// ❌ không reset super admin
if ($admin['role'] === 'super_admin') {
    header("Location: admin_list.php?msg=forbidden");
    exit();
}