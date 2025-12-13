<?php
// fix_guide_status.php - FIXED VERSION
require_once './commons/env.php';
require_once './commons/function.php';

$conn = connectDB();

echo "<h3>🔧 FIX GUIDE STATUS (NO PASSWORD COLUMN)</h3>";

// 1. Kiểm tra cấu trúc bảng
echo "<h4>📋 Checking table structure...</h4>";
$stmt = $conn->query("DESCRIBE guides");
$columns = $stmt->fetchAll();

$hasPasswordColumn = false;
foreach ($columns as $col) {
    if (in_array($col['Field'], ['password_hash', 'password', 'pass'])) {
        $hasPasswordColumn = true;
        echo "<p>Found password column: <strong>{$col['Field']}</strong> ({$col['Type']})</p>";
    }
}

if (!$hasPasswordColumn) {
    echo "<p style='color:orange'>⚠️ No password column found in guides table!</p>";
    echo "<p>Will use default password logic.</p>";
}

// 2. Update HDV001 thành active
$sql = "UPDATE guides SET status = 'active' WHERE guide_code = 'HDV001'";
$stmt = $conn->prepare($sql);
$result = $stmt->execute();

if ($result) {
    echo "<p style='color:green'>✅ Đã cập nhật HDV001 thành active</p>";
} else {
    echo "<p style='color:red'>❌ Lỗi khi cập nhật HDV001</p>";
}

// 3. Cập nhật cả HDV002 để chắc chắn
$sql = "UPDATE guides SET status = 'active' WHERE guide_code = 'HDV002'";
$stmt = $conn->prepare($sql);
$result = $stmt->execute();

if ($result) {
    echo "<p style='color:green'>✅ Đã cập nhật HDV002 thành active</p>";
} else {
    echo "<p style='color:red'>❌ Lỗi khi cập nhật HDV002</p>";
}

// 4. Kiểm tra xem có cột password không, nếu có thì update
$columnNames = array_column($columns, 'Field');
if (in_array('password', $columnNames)) {
    // Cập nhật password cho cả 2 HDV
    $guides = ['HDV001', 'HDV002'];
    foreach ($guides as $code) {
        $sql = "UPDATE guides SET password = :pass WHERE guide_code = :code";
        $stmt = $conn->prepare($sql);
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $result = $stmt->execute([':pass' => $hashedPassword, ':code' => $code]);
        
        if ($result) {
            echo "<p style='color:green'>✅ Đã thêm password cho $code</p>";
        }
    }
} else {
    echo "<p style='color:orange'>⚠️ Không có cột password trong bảng guides</p>";
    echo "<p>Hệ thống sẽ sử dụng logic mật khẩu mặc định</p>";
}

// 5. Hiển thị kết quả
echo "<h4>📊 Kết quả sau khi fix:</h4>";
$stmt = $conn->query("SELECT guide_id, guide_code, full_name, email, status FROM guides ORDER BY guide_id");
$guides = $stmt->fetchAll();

echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>
        <tr style='background: #f2f2f2;'>
            <th>ID</th>
            <th>Guide Code</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>";

foreach ($guides as $g) {
    $statusColor = $g['status'] === 'active' ? 'green' : 'orange';
    echo "<tr>
            <td>{$g['guide_id']}</td>
            <td><strong>{$g['guide_code']}</strong></td>
            <td>{$g['full_name']}</td>
            <td>{$g['email']}</td>
            <td style='color:{$statusColor};'><strong>{$g['status']}</strong></td>
          </tr>";
}
echo "</table>";

echo '<div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border: 1px solid #b3d7ff;">
        <h5>🔑 Thông tin login test:</h5>
        <p><strong>HDV001:</strong> guidea@tour.com / password123</p>
        <p><strong>HDV002:</strong> guideb@tour.com / password123</p>
        <p><em>Hoặc dùng Guide Code: HDV001 / password123</em></p>
      </div>';

echo '<p style="margin-top: 20px;"><a href="?act=login" class="btn btn-primary">Test Login Now</a></p>';
?>