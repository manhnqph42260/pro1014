<?php
// load_full_dashboard.php - Test load full dashboard
session_start();

// Set session giả lập
$_SESSION['guide_id'] = 1;
$_SESSION['full_name'] = 'Nguyễn Văn A';
$_SESSION['role'] = 'guide';
$_SESSION['guide_code'] = 'HDV001';

// Set biến mà dashboard cần
$page_title = "Bảng điều khiển HDV - TEST";

echo "<h3>🧪 TEST LOAD FULL DASHBOARD</h3>";

// Load từng phần
$basePath = __DIR__ . '/views/admin/guides/';

echo "<h4>1. Loading Header:</h4>";
if (file_exists($basePath . 'header.php')) {
    require_once $basePath . 'header.php';
    echo "<p style='color:green'>✅ Header loaded</p>";
} else {
    echo "<p style='color:red'>❌ Header not found</p>";
}

echo "<h4>2. Loading Dashboard:</h4>";
if (file_exists($basePath . 'dashboard.php')) {
    require_once $basePath . 'dashboard.php';
    echo "<p style='color:green'>✅ Dashboard loaded</p>";
} else {
    echo "<p style='color:red'>❌ Dashboard not found</p>";
}

echo "<h4>3. Loading Footer:</h4>";
if (file_exists($basePath . 'footer.php')) {
    require_once $basePath . 'footer.php';
    echo "<p style='color:green'>✅ Footer loaded</p>";
} else {
    echo "<p style='color:red'>❌ Footer not found</p>";
}

echo '<hr><a href="?act=guide_dashboard" class="btn btn-success">Test Real Dashboard</a>';
?>