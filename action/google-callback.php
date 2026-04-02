<?php
session_start();

require_once("../admin/config/database.php");
require_once("../config/env.php");

// ===== LOAD ENV =====
loadEnv(__DIR__ . "/../.env");

// ===== LẤY BIẾN =====
$client_id     = getenv('GOOGLE_CLIENT_ID');
$client_secret = getenv('GOOGLE_CLIENT_SECRET');
$redirect_uri  = getenv('GOOGLE_REDIRECT_URI');

// ===== CHECK ENV =====
if (!$client_id || !$client_secret || !$redirect_uri) {
    die("Thiếu cấu hình .env");
}

// ===== CHECK CODE =====
if (!isset($_GET['code'])) {
    header("Location: ../index.php?page=login");
    exit;
}

$code = $_GET['code'];

/// ===== LẤY TOKEN =====
$token_url = "https://oauth2.googleapis.com/token";

$data = [
    'code' => $code,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($token_url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("CURL ERROR: " . curl_error($ch));
}

curl_close($ch);

$token = json_decode($response, true);

if (!isset($token['access_token'])) {
    die("Lỗi lấy token");
}

/// ===== LẤY USER INFO =====
$ch = curl_init("https://www.googleapis.com/oauth2/v2/userinfo");
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $token['access_token']
    ],
    CURLOPT_RETURNTRANSFER => true
]);

$user_info = curl_exec($ch);

if (curl_errno($ch)) {
    die("CURL ERROR USER: " . curl_error($ch));
}

curl_close($ch);

$user = json_decode($user_info, true);

if (!isset($user['email'])) {
    die("Không lấy được user info");
}

$email = $user['email'];
$name  = $user['name'] ?? 'User Google';

/// ===== CHECK DB =====
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {

    // tạo username từ email
    $username = explode('@', $email)[0] . rand(100,999);

    // insert user mới
    $stmt = $conn->prepare("
        INSERT INTO users(username, full_name, email, password, provider, role, status)
        VALUES(?, ?, ?, NULL, 'google', 'customer', 1)
    ");
    $stmt->bind_param("sss", $username, $name, $email);
    $stmt->execute();

    // lấy lại user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user_db = $stmt->get_result()->fetch_assoc();

} else {
    $user_db = $result->fetch_assoc();
}

/// ===== LOGIN =====
$_SESSION['user'] = [
    'id' => $user_db['id'],
    'full_name' => $user_db['full_name'],
    'email' => $user_db['email']
];

header("Location: ../index.php");
exit;