<?php
include "../config/database.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM customers WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {

    // 🔥 kiểm tra password đúng cách
    if (password_verify($password, $row['password'])) {
        echo "Đăng nhập thành công";
    } else {
        echo "Sai mật khẩu";
    }

} else {
    echo "Không tìm thấy user";
}
?>