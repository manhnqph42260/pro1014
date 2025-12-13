<?php
// check_guides.php
require_once './commons/env.php';
require_once './commons/function.php';

$conn = connectDB();

echo "<h3>🔍 KIỂM TRA DATABASE GUIDES</h3>";

// 1. Kiểm tra bảng guides
try {
    $stmt = $conn->query("SELECT * FROM guides");
    $guides = $stmt->fetchAll();
    
    echo "<h4>Tổng số HDV: " . count($guides) . "</h4>";
    
    if (count($guides) > 0) {
        echo "<table border='1' cellpadding='10'>
                <tr>
                    <th>ID</th>
                    <th>Guide Code</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Password Hash</th>
                </tr>";
        
        foreach ($guides as $g) {
            echo "<tr>
                    <td>{$g['guide_id']}</td>
                    <td>{$g['guide_code']}</td>
                    <td>{$g['full_name']}</td>
                    <td>{$g['email']}</td>
                    <td>{$g['status']}</td>
                    <td>" . (isset($g['password_hash']) ? substr($g['password_hash'], 0, 20) . '...' : 'NULL') . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red'>❌ Bảng guides TRỐNG!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Lỗi: " . $e->getMessage() . "</p>";
}

// 2. Kiểm tra tìm kiếm HDV001
echo "<h4>🔎 Tìm kiếm 'HDV001':</h4>";

$search_terms = ['HDV001', 'hdv001', 'HDV001@example.com', 'Hướng dẫn viên 001'];
foreach ($search_terms as $term) {
    $stmt = $conn->prepare("SELECT * FROM guides WHERE guide_code = ? OR email = ? OR full_name LIKE ?");
    $stmt->execute([$term, $term, "%$term%"]);
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<p style='color:green'>✅ Tìm thấy với '$term': {$result['guide_code']} - {$result['full_name']}</p>";
    } else {
        echo "<p style='color:red'>❌ Không tìm thấy với '$term'</p>";
    }
}

// 3. Kiểm tra cấu trúc bảng
echo "<h4>📋 Cấu trúc bảng guides:</h4>";
$stmt = $conn->query("DESCRIBE guides");
$columns = $stmt->fetchAll();

echo "<ul>";
foreach ($columns as $col) {
    echo "<li>{$col['Field']} ({$col['Type']})</li>";
}
echo "</ul>";
?>