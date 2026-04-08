<?php
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

requireSuperAdmin(); // chỉ super admin được thêm

$id = $_GET['id'] ?? 0;

// lấy dữ liệu admin
$stmt = $conn->prepare("SELECT * FROM admin_users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// ❌ nếu không tồn tại
if (!$user) {
    die("Không tìm thấy admin!");
}

// ✅ gán biến để dùng trong form
$username  = $user['username'];
$full_name = $user['full_name'];
$email     = $user['email'];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username  = trim($_POST['username']);
    $password  = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);

    // ===== VALIDATE =====
    if (empty($username) || empty($password) || empty($full_name)) {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải >= 6 ký tự.";
    } else {

        // check trùng username
        $check = $conn->prepare("SELECT id FROM admin_users WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username đã tồn tại!";
        } else {

            // hash password
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // insert
            $stmt = $conn->prepare("
                INSERT INTO admin_users (username, password, full_name, email)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param("ssss", $username, $hashed, $full_name, $email);

            if ($stmt->execute()) {
                // redirect cho sạch form
                header("Location: admin_list.php?add=1");
                exit();
            } else {
                $error = "Có lỗi xảy ra!";
            }
        }
    }
}
?>
<div class="container mt-5">

    <div class="card shadow border-0">

        <div class="card-header bg-warning">
            <h5 class="mb-0">✏️ Sửa Administrator</h5>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" value="<?= $username['username'] ?>" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label>Họ tên</label>
                    <input type="text" name="full_name" value="<?= $full_name['full_name'] ?>" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $email['email'] ?>" class="form-control">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="admin_list.php" class="btn btn-secondary">⬅ Quay lại</a>
                    <button class="btn btn-warning">💾 Cập nhật</button>
                </div>

            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>