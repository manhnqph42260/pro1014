<?php
// create_missing_hdv_views.php
echo "<h3>➕ TẠO CÁC FILE VIEW CÒN THIẾU CHO HDV</h3>";

$basePath = __DIR__ . '/views/admin/guides/';
$filesToCreate = [
    'my_tours.php' => 'Tour của tôi',
    'attendance.php' => 'Điểm danh',
    'guest_list.php' => 'Danh sách khách',
    'journal.php' => 'Nhật ký tour',
    'special_requests.php' => 'Yêu cầu đặc biệt',
];

foreach ($filesToCreate as $filename => $title) {
    $fullPath = $basePath . $filename;
    
    if (!file_exists($fullPath)) {
        $content = <<<HTML
<?php
// views/admin/guides/{$filename}
\$page_title = "$title";
require_once 'header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📋 $title</h4>
                </div>
                <div class="card-body">
                    <h5>Xin chào, <?php echo \$_SESSION['full_name'] ?? 'HDV'; ?>!</h5>
                    <p>Đây là trang <strong>$title</strong>. Chức năng đang được phát triển.</p>
                    
                    <div class="alert alert-info mt-3">
                        <h6><i class="bi bi-info-circle"></i> Thông tin:</h6>
                        <p>Trang này sẽ hiển thị đầy đủ chức năng trong phiên bản tiếp theo.</p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="?act=guide_dashboard" class="btn btn-primary">
                            <i class="bi bi-house"></i> Quay lại Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
HTML;
        
        file_put_contents($fullPath, $content);
        echo "<p style='color:green'>✅ Đã tạo file: <strong>$filename</strong></p>";
    } else {
        echo "<p style='color:blue'>ℹ️ File đã tồn tại: <strong>$filename</strong></p>";
    }
}

// Kiểm tra lại
echo "<h4>📋 Kiểm tra lại sau khi tạo:</h4>";
foreach ($filesToCreate as $filename => $title) {
    $fullPath = $basePath . $filename;
    echo "<p>$filename: " . (file_exists($fullPath) ? '✅ EXISTS' : '❌ MISSING') . "</p>";
}

echo '<div class="mt-4">
        <a href="?act=guide_my_tours" class="btn btn-success">Test: Tour của tôi</a>
        <a href="?act=guide_attendance" class="btn btn-warning">Test: Điểm danh</a>
        <a href="?act=guide_dashboard" class="btn btn-primary">Dashboard</a>
      </div>';
?>