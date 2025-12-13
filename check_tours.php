<?php
// Thiết lập hiển thị dạng văn bản thuần để dễ copy
header('Content-Type: text/plain; charset=utf-8');

echo "=== THÔNG TIN ĐƯỜNG DẪN & CẤU TRÚC THƯ MỤC ===\n";
echo "Thư mục gốc: " . __DIR__ . "\n";
echo "Thời gian quét: " . date('Y-m-d H:i:s') . "\n";
echo "==============================================\n\n";

// Hàm đệ quy để quét thư mục
function scanFolder($dir, $prefix = '') {
    // Lấy danh sách file và thư mục
    $items = scandir($dir);

    // Loại bỏ . và ..
    $items = array_diff($items, ['.', '..']);

    // Sắp xếp lại để folder lên trước hoặc theo alphabet tùy ý (để mặc định)
    foreach ($items as $key => $item) {
        // Bỏ qua các thư mục rác nếu không cần thiết (tùy bạn chọn)
        if (in_array($item, ['.git', '.idea', 'node_modules', 'vendor'])) {
            echo $prefix . "├── " . $item . " (Đã ẩn chi tiết để gọn nhẹ)\n";
            continue;
        }

        $path = $dir . DIRECTORY_SEPARATOR . $item;
        
        // Kiểm tra xem là phần tử cuối cùng trong danh sách hay chưa để vẽ cây
        $isLast = ($key === array_key_last($items));
        $currentPrefix = $isLast ? "└── " : "├── ";
        $nextPrefix    = $isLast ? "    " : "│   ";

        echo $prefix . $currentPrefix . $item;

        if (is_dir($path)) {
            echo "/\n"; // Đánh dấu là thư mục
            scanFolder($path, $prefix . $nextPrefix); // Đệ quy vào bên trong
        } else {
            echo "\n";
        }
    }
}

// Chạy hàm quét bắt đầu từ thư mục hiện tại
scanFolder(__DIR__);
?>