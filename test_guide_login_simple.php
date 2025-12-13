<?php
// test_guide_login_simple.php
require_once './commons/env.php';
require_once './commons/function.php';

$conn = connectDB();

echo "<h3>🧪 SIMPLE GUIDE LOGIN TEST</h3>";

// Test cases
$testCases = [
    ['HDV001', 'password123'],
    ['guidea@tour.com', 'password123'],
    ['HDV002', 'password123'],
    ['guideb@tour.com', 'password123'],
];

foreach ($testCases as $test) {
    list($username, $password) = $test;
    
    echo "<h4>Testing: <strong>$username</strong> / <strong>$password</strong></h4>";
    
    // Tìm user
    $sql = "SELECT * FROM guides WHERE guide_code = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p style='color:green'>✅ User found: {$user['guide_code']} - {$user['full_name']}</p>";
        echo "<p>Status: <strong>{$user['status']}</strong></p>";
        
        // Kiểm tra mật khẩu
        if ($password === 'password123' || $password === '123456') {
            echo "<p style='color:green'>✅ Password accepted (default password)</p>";
            echo "<p><a href='?act=login&username=$username&password=$password' class='btn btn-sm btn-success'>Auto Login</a></p>";
        } else {
            echo "<p style='color:red'>❌ Password not accepted</p>";
        }
    } else {
        echo "<p style='color:red'>❌ User not found</p>";
    }
    echo "<hr>";
}

echo '<a href="?act=login" class="btn btn-primary">Go to Login Page</a>';
?>