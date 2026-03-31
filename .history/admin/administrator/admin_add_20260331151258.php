<?php
require_once 'includes/functions.php';
require_once 'config/database.php';

requireSuperAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("
        INSERT INTO admin_users (username, password, full_name, email)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssss", $username, $password, $full_name, $email);
    $stmt->execute();

    header("Location: admin_list.php");
}
?>