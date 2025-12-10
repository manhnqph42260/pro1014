<?php
$page_title = "Lịch trình Tour: " . ($tour['tour_name'] ?? '');
require_once './views/admin/header.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
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
        .itinerary-list {
            margin-bottom: 30px;
        }
        .itinerary-day {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .day-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #2c3e50;
        }
        .day-number {
            background: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        .day-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            line-height: 1.5;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .add-itinerary-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            border: 2px dashed #ddd;
        }
        .edit-form {
            display: none;
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border: 1px solid #ffeaa7;
        }
    </style>
            <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
        }
        .sidebar .logo {
            padding: 1rem;
            border-bottom: 1px solid #34495e;
        }
        .sidebar-nav {
            padding: 1rem 0;
        }
        .nav-item {
            display: block;
            padding: 0.75rem 1rem;
            color: #bdc3c7;
            text-decoration: none;
            border-left: 3px solid transparent;
        }
        .nav-item:hover, .nav-item.active {
            background: #34495e;
            color: white;
            border-left-color: #3498db;
        }
        .main-content {
            flex: 1;
            background: #ecf0f1;
        }
        .top-header {
            background: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .content-area {
            padding: 2rem;
        }
        .container {
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }
        .table th {
            background: #f8f9fa;
        }
        .status-published { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .status-draft { background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .status-locked { background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input, .search-form select {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .tour-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .no-image {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border: 1px dashed #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 10px;
        }
        .tour-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-edit { background: #ffc107; color: black; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-view { background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar (giống các trang khác) -->


        <!-- Main Content -->
        <div class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1>Lịch trình: <?php echo htmlspecialchars($tour['tour_name']); ?></h1>
                    <small>Mã tour: <?php echo $tour['tour_code']; ?> | Thời gian: <?php echo $tour['duration_days']; ?> ngày</small>
                </div>
                <div class="header-right">
                    <span>Xin chào, <?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="content-area">
                <div class="container">
                    <!-- Header -->
                    <div class="header">
                        <h2>📅 Quản lý Lịch trình Tour</h2>
                        <div>
                            <a href="?act=admin_tours" class="btn btn-secondary">← Quay lại</a>
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

                    <!-- Add Itinerary Form -->
                    <div class="add-itinerary-form" id="addForm" style="display: none;">
                        <h3>➕ Thêm Ngày Mới</h3>
                        <form method="POST" action="?act=admin_tours_itinerary_add">
                            <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Ngày thứ:</label>
                                    <input type="number" name="day_number" min="1" max="<?php echo $tour['duration_days'] + 10; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Tiêu đề ngày:</label>
                                    <input type="text" name="title" placeholder="VD: Khởi hành Hà Nội - Sapa" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Mô tả chi tiết:</label>
                                <textarea name="description" placeholder="Mô tả tổng quan về ngày này..." rows="3"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Hoạt động:</label>
                                <textarea name="activities" placeholder="Các hoạt động chính trong ngày..." rows="3"></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Chỗ ở:</label>
                                    <input type="text" name="accommodation" placeholder="VD: Khách sạn 3 sao">
                                </div>
                                <div class="form-group">
                                    <label>Bữa ăn:</label>
                                    <input type="text" name="meals" placeholder="VD: Sáng, Trưa, Tối">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Ghi chú HDV:</label>
                                <textarea name="guide_notes" placeholder="Ghi chú đặc biệt cho hướng dẫn viên..." rows="2"></textarea>
                            </div>
                            
                            <a href="?act=admin_tours_itinerary&tour_id=<?php echo $tour_id; ?>"><button type="submit" class="btn btn-success">💾 Lưu Ngày Mới</button></a>
                            <button type="button" onclick="toggleAddForm()" class="btn btn-secondary">❌ Hủy</button>
                        </form>
                    </div>

                    <!-- Itinerary List -->
                    <div class="itinerary-list">
                        <h3>📋 Lịch trình Tour (<?php echo count($itineraries); ?> ngày)</h3>
                        
                        <?php if (count($itineraries) > 0): ?>
                            <?php foreach ($itineraries as $itinerary): ?>
                            <div class="itinerary-day" id="day-<?php echo $itinerary['itinerary_id']; ?>">
                                <div class="day-header">
                                    <div class="day-title">
                                        <span class="day-number">Ngày <?php echo $itinerary['day_number']; ?></span>
                                        - <?php echo htmlspecialchars($itinerary['title']); ?>
                                    </div>
                                    <div class="actions">
                                        <button onclick="toggleEditForm(<?php echo $itinerary['itinerary_id']; ?>)" class="btn">✏️ Sửa</button>
                                        <a href="?act=admin_tours_itinerary_delete&itinerary_id=<?php echo $itinerary['itinerary_id']; ?>&tour_id=<?php echo $tour_id; ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Xóa ngày <?php echo $itinerary['day_number']; ?>?')">🗑️ Xóa</a>
                                    </div>
                                </div>
                                
                                <div class="day-content">
                                    <div>
                                        <div class="info-section">
                                            <div class="info-label">📝 Mô tả:</div>
                                            <div class="info-value"><?php echo nl2br(htmlspecialchars($itinerary['description'])); ?></div>
                                        </div>
                                        <div class="info-section">
                                            <div class="info-label">🎯 Hoạt động:</div>
                                            <div class="info-value"><?php echo nl2br(htmlspecialchars($itinerary['activities'])); ?></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="info-section">
                                            <div class="info-label">🏨 Chỗ ở:</div>
                                            <div class="info-value"><?php echo htmlspecialchars($itinerary['accommodation'] ?? ''); ?></div>
                                        </div>
                                        <div class="info-section">
                                            <div class="info-label">🍽️ Bữa ăn:</div>
                                            <div class="info-value"><?php echo htmlspecialchars($itinerary['meals'] ?? ''); ?></div>
                                        </div>
                                        <div class="info-section">
                                            <div class="info-label">📋 Ghi chú HDV:</div>
                                            <div class="info-value"><?php echo nl2br(htmlspecialchars($itinerary['guide_notes'] ?? '')); ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Form -->
                                <div class="edit-form" id="editForm-<?php echo $itinerary['itinerary_id']; ?>">
                                    <h4>✏️ Chỉnh sửa Ngày <?php echo $itinerary['day_number']; ?></h4>
                                    <form method="POST" action="?act=admin_tours_itinerary_edit">
                                        <input type="hidden" name="itinerary_id" value="<?php echo $itinerary['itinerary_id']; ?>">
                                        <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                                        
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label>Ngày thứ:</label>
                                                <input type="number" name="day_number" value="<?php echo $itinerary['day_number']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Tiêu đề:</label>
                                                <input type="text" name="title" value="<?php echo htmlspecialchars($itinerary['title']); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Mô tả:</label>
                                            <textarea name="description" rows="3"><?php echo htmlspecialchars($itinerary['description']); ?></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Hoạt động:</label>
                                            <textarea name="activities" rows="3"><?php echo htmlspecialchars($itinerary['activities']); ?></textarea>
                                        </div>
                                        
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label>Chỗ ở:</label>
                                                <input type="text" name="accommodation" value="<?php echo htmlspecialchars($itinerary['accommodation']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Bữa ăn:</label>
                                                <input type="text" name="meals" value="<?php echo htmlspecialchars($itinerary['meals']); ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Ghi chú HDV:</label>
                                            <textarea name="guide_notes" rows="2"><?php echo htmlspecialchars($itinerary['guide_notes']); ?></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success">💾 Cập nhật</button>
                                        <button type="button" onclick="toggleEditForm(<?php echo $itinerary['itinerary_id']; ?>)" class="btn btn-secondary">❌ Hủy</button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <h3>📅 Chưa có lịch trình</h3>
                                <p>Tour này chưa có lịch trình nào. Hãy thêm ngày đầu tiên!</p>
                                <button onclick="toggleAddForm()" class="btn btn-success">+ Thêm Ngày Đầu Tiên</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAddForm() {
            const form = document.getElementById('addForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function toggleEditForm(itineraryId) {
            const form = document.getElementById('editForm-' + itineraryId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        // Auto-show add form if no itineraries
        <?php if (count($itineraries) === 0): ?>
        document.addEventListener('DOMContentLoaded', function() {
            toggleAddForm();
        });
        <?php endif; ?>
    </script>
</body>
</html>