<?php
require_once 'includes/functions.php';
require_once 'config/database.php';
re

requireAdminLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($newPassword) || empty($confirmPassword)) {
        $error = "Vui lòng nhập đầy đủ.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Mật khẩu không khớp.";
    } else {

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
            UPDATE admin_users 
            SET password=?, must_change_password=0 
            WHERE id=?
        ");

        $stmt->bind_param("si", $hashed, $_SESSION['admin_id']);
        $stmt->execute();

        // 👉 sau khi đổi xong → về dashboard
        header("Location: index.php");
        exit();
    }
}
?>