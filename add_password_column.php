<?php
// add_password_column.php
require_once './commons/env.php';
require_once './commons/function.php';

$conn = connectDB();

echo "<h3>➕ ADD PASSWORD COLUMN TO GUIDES TABLE</h3>";

try {
    // Kiểm tra xem cột password đã có chưa
    $stmt = $conn->query("SHOW COLUMNS FROM guides LIKE 'password'");
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        // Thêm cột password
        $sql = "ALTER TABLE guides ADD COLUMN password VARCHAR(255) NULL AFTER email";
        $conn->exec($sql);
        echo "<p style='color:green'>✅ Đã thêm cột 'password' vào bảng guides</p>";
        
        // Cập nhật password cho các guide có sẵn
        $guides = $conn->query("SELECT guide_id, guide_code FROM guides")->fetchAll();
        
        foreach ($guides as $guide) {
            $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
            $updateSql = "UPDATE guides SET password = :pass WHERE guide_id = :id";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute([':pass' => $hashedPassword, ':id' => $guide['guide_id']]);
            
            echo "<p>Đã set password cho {$guide['guide_code']}: password123</p>";
        }
        
        echo "<p style='color:green'>✅ Đã cập nhật password cho tất cả guides</p>";
        
    } else {
        echo "<p style='color:blue'>ℹ️ Cột 'password' đã tồn tại trong bảng guides</p>";
    }
    
    // Kiểm tra cột password_hash
    $stmt = $conn->query("SHOW COLUMNS FROM guides LIKE 'password_hash'");
    $hashColumnExists = $stmt->rowCount() > 0;
    
    if ($hashColumnExists) {
        echo "<p style='color:blue'>ℹ️ Cột 'password_hash' đã tồn tại</p>";
    }
    
    // Hiển thị cấu trúc bảng
    echo "<h4>📋 Cấu trúc bảng guides hiện tại:</h4>";
    $stmt = $conn->query("DESCRIBE guides");
    $columns = $stmt->fetchAll();
    
    echo "<ul>";
    foreach ($columns as $col) {
        $style = in_array($col['Field'], ['password', 'password_hash']) ? "style='color:green; font-weight:bold'" : "";
        echo "<li $style>{$col['Field']} ({$col['Type']})</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

echo '<div style="margin-top: 20px;">
        <a href="?act=login" class="btn btn-success">Test Login</a>
        <a href="fix_guide_status.php" class="btn btn-primary">Fix Guide Status</a>
      </div>';
?>