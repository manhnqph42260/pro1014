<?php
$page_title = "Tạo Lịch khởi hành";
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
            max-width: 800px;
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
        
    </style>
    <style>
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
        }
        .table th {
            background: #f8f9fa;
        }
        .status-scheduled { background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; }
        .status-confirmed { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; }
        .status-completed { background: #d1ecf1; color: #0c5460; padding: 4px 8px; border-radius: 4px; }
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input, .search-form select {
            padding: 8px;
            margin-right: 10px;
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
        <!-- Sidebar -->

        <!-- Main Content -->
        <div class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1>Tạo Lịch khởi hành</h1>
                </div>
                <div class="header-right">
                    <span>Xin chào, <?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="content-area">
                <div class="container">
                    <a href="?act=admin_departures" class="btn btn-secondary">← Quay lại</a>
                    
                    <h2>📅 Tạo lịch khởi hành mới</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Chọn Tour:</label>
                                <select name="tour_id" required>
                                    <option value="">-- Chọn tour --</option>
                                    <?php foreach ($tours as $tour): ?>
                                        <option value="<?php echo $tour['tour_id']; ?>">
                                            <?php echo $tour['tour_code']; ?> - <?php echo htmlspecialchars($tour['tour_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ngày khởi hành:</label>
                                <input type="date" name="departure_date" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Giờ khởi hành:</label>
                                <input type="time" name="departure_time">
                            </div>
                            <div class="form-group">
                                <label>Số chỗ dự kiến:</label>
                                <input type="number" name="expected_slots" min="1" max="100" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Giá người lớn (VNĐ):</label>
                                <input type="number" name="price_adult" min="0" required>
                            </div>
                            <div class="form-group">
                                <label>Giá trẻ em (VNĐ):</label>
                                <input type="number" name="price_child" min="0" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Điểm tập trung:</label>
                            <textarea name="meeting_point" rows="2" placeholder="Địa điểm và thông tin tập trung..."><?php echo htmlspecialchars($_POST['meeting_point'] ?? ''); ?></textarea>

                        </div>
                        
                        <div class="form-group">
                            <label>Ghi chú vận hành:</label>
                            <textarea name="operational_notes" rows="3" placeholder="Ghi chú đặc biệt cho đội vận hành..."><?php echo htmlspecialchars($_POST['operational_notes'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn">💾 Tạo lịch khởi hành</button>
                        <a href="?act=admin_departures" class="btn btn-secondary">❌ Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>