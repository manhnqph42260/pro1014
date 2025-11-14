<?php
require_once '../config.php'; // File config của bạn
require_once 'function.php';

echo "<h3>Testing Database Connection</h3>";

try {
    $conn = connectDB();
    
    if ($conn) {
        echo "✅ <strong>Kết nối database '" . DB_NAME . "' THÀNH CÔNG!</strong><br>";
        
        // Test query đơn giản
        $stmt = $conn->query("SELECT DATABASE() as db_name");
        $result = $stmt->fetch();
        echo "✅ Đang sử dụng database: " . $result['db_name'] . "<br>";
        
        // Kiểm tra version MySQL
        $stmt = $conn->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        echo "✅ MySQL Version: " . $result['version'] . "<br>";
        
    } else {
        echo "❌ Kết nối database THẤT BẠI!";
    }
    
} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
?>