<?php
require_once __DIR__ . '/config/database.php';

// Tạo hash ĐÚNG cho "admin123"
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>🔧 Fix mật khẩu Admin</h2>";
echo "<p>Mật khẩu: <strong>$password</strong></p>";
echo "<p>Hash mới: <code>$hash</code></p>";

// Kiểm tra verify
echo "<p>Verify test: " . (password_verify($password, $hash) ? '✅ ĐÚNG' : '❌ SAI') . "</p>";

// Cập nhật vào database
$stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
$stmt->bind_param("s", $hash);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "<p style='color:green; font-size:20px;'>✅ Cập nhật mật khẩu thành công!</p>";
        echo "<p>Giờ bạn đăng nhập với:</p>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> admin</li>";
        echo "<li><strong>Password:</strong> admin123</li>";
        echo "</ul>";
        echo "<br><a href='login.php' style='padding:10px 20px; background:#0061f2; color:#fff; border-radius:8px; text-decoration:none;'>→ Đăng nhập ngay</a>";
    } else {
        echo "<p style='color:red;'>❌ Không tìm thấy username 'admin' trong database!</p>";
        
        // Kiểm tra xem có tài khoản nào không
        $result = $conn->query("SELECT id, username FROM admin_users");
        echo "<p>Danh sách tài khoản trong DB:</p>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>ID: {$row['id']} - Username: {$row['username']}</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color:red;'>❌ Lỗi SQL: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>