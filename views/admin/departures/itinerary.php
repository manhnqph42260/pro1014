<?php
// Kiểm tra tour có tồn tại không
if (!isset($tour) || !$tour) {
    echo "<div style='padding: 20px; text-align: center;'>";
    echo "<h2>❌ Lỗi: Tour không tồn tại</h2>";
    echo "<p>Tour bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>";
    echo "<a href='?act=admin_tours' class='btn'>← Quay lại Quản lý Tour</a>";
    echo "</div>";
    exit();
}

$page_title = "Lịch trình Tour: " . htmlspecialchars($tour['tour_name'] ?? 'Tour không xác định');
require_once './views/admin/header.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        /* Giữ nguyên CSS cũ */
        .container {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .btn {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-danger {
            background: #dc3545;
        }
        .tour-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .tour-info h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        .tour-info p {
            margin: 5px 0;
            color: #555;
        }
        /* ... phần CSS còn lại giữ nguyên ... */
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->


        <!-- Main Content -->
        <div class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1>📅 Lịch trình Tour</h1>
                    <div class="tour-info">
                        <h3><?php echo htmlspecialchars($tour['tour_name']); ?></h3>
                        <p><strong>Mã tour:</strong> <?php echo htmlspecialchars($tour['tour_code'] ?? 'N/A'); ?></p>
                        <p><strong>Thời gian:</strong> <?php echo htmlspecialchars($tour['duration_days'] ?? '0'); ?> ngày</p>
                        <p><strong>Điểm đến:</strong> <?php echo htmlspecialchars($tour['destination'] ?? 'Chưa cập nhật'); ?></p>
                    </div>
                </div>
                <div class="header-right">
                    <span>Xin chào, <?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="content-area">
                <div class="container">
                    <!-- Header -->
                    <div class="header">
                        <h2>📋 Quản lý Lịch trình</h2>
                        <div>
                            <a href="?act=admin_tours" class="btn btn-secondary">← Quay lại Danh sách Tour</a>
                            <button onclick="toggleAddForm()" class="btn btn-success">+ Thêm ngày mới</button>
                        </div>
                    </div>

                    <!-- Messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-error">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Phần còn lại của code giữ nguyên -->
                    <!-- Add Itinerary Form -->
                    <div class="add-itinerary-form" id="addForm" style="display: none;">
                        <h3>➕ Thêm Ngày Mới</h3>
                        <form method="POST" action="?act=admin_tours_itinerary_add">
                            <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Ngày thứ:</label>
                                    <input type="number" name="day_number" min="1" max="<?php echo ($tour['duration_days'] ?? 10) + 10; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Tiêu đề ngày:</label>
                                    <input type="text" name="title" placeholder="VD: Khởi hành Hà Nội - Sapa" required>
                                </div>
                            </div>
                            
                            <!-- ... phần form còn lại giữ nguyên ... -->