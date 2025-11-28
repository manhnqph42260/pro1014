<?php
echo "<h3>🔍 Kiểm tra đường dẫn</h3>";

$paths = [
    'views/admin/header.php' => 'Header file',
    'views/admin/tours/create.php' => 'Create tour file', 
    'views/admin/dashboard.php' => 'Dashboard file'
];

foreach ($paths as $path => $description) {
    if (file_exists($path)) {
        echo "✅ $description: $path - TỒN TẠI<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;Absolute path: " . realpath($path) . "<br>";
    } else {
        echo "❌ $description: $path - KHÔNG TỒN TẠI<br>";
    }
}

echo "<h4>📁 Current directory structure:</h4>";
echo "<pre>";
function showDir($dir, $prefix = '') {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $dir . '/' . $item;
        echo $prefix . '├── ' . $item . "\n";
        if (is_dir($path)) {
            showDir($path, $prefix . '│   ');
        }
    }
}
showDir(__DIR__);
echo "</pre>";
?>