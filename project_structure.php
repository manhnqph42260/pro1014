<?php
function getDirectoryStructure($dir, $prefix = '') {
    $structure = '';
    $items = scandir($dir);
    
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        
        $path = $dir . '/' . $item;
        $structure .= $prefix . '├── ' . $item . "\n";
        
        if (is_dir($path)) {
            $structure .= getDirectoryStructure($path, $prefix . '│   ');
        }
    }
    
    return $structure;
}

echo "<pre>";
echo "📁 CẤU TRÚC DỰ ÁN CỦA BẠN:\n";
echo "==========================\n";
echo getDirectoryStructure(__DIR__);
echo "</pre>";

// Hiển thị thông tin thêm
echo "<h3>📊 Thông tin bổ sung:</h3>";
echo "Thư mục gốc: " . __DIR__ . "<br>";
echo "URL dự án: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "<br>";

// Kiểm tra các file quan trọng
$important_files = [
    'index.php',
    'commons/env.php', 
    'commons/function.php',
    'controllers/AdminController.php',
    'controllers/TourController.php',
    'views/admin/dashboard.php'
];

echo "<h3>🔍 Kiểm tra file quan trọng:</h3>";
foreach ($important_files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ " . $file . " - TỒN TẠI<br>";
    } else {
        echo "❌ " . $file . " - KHÔNG TỒN TẠI<br>";
    }
}
?>