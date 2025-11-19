<?php
require_once './commons/env.php';
require_once './commons/function.php';

echo "<h3>🔍 Kiểm tra Database Tours</h3>";

try {
    $conn = connectDB();
    echo "✅ Kết nối database thành công<br>";
    
    // Kiểm tra bảng tours
    $stmt = $conn->query("SHOW TABLES LIKE 'tours'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Bảng 'tours' tồn tại<br>";
    } else {
        echo "❌ Bảng 'tours' KHÔNG tồn tại<br>";
        exit();
    }
    
    // Kiểm tra dữ liệu trong tours
    $stmt = $conn->query("SELECT COUNT(*) as count FROM tours");
    $result = $stmt->fetch();
    echo "Số tour trong database: " . $result['count'] . "<br>";
    
    if ($result['count'] > 0) {
        $tours = $conn->query("SELECT tour_id, tour_code, tour_name, status FROM tours")->fetchAll();
        echo "Danh sách tour:<br>";
        foreach ($tours as $tour) {
            echo "- ID: " . $tour['tour_id'] . ", Code: " . $tour['tour_code'] . ", Name: " . $tour['tour_name'] . ", Status: " . $tour['status'] . "<br>";
        }
    } else {
        echo "❌ Không có tour nào trong database<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "<br>";
}
?>