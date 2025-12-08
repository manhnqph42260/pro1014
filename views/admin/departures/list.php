<?php
$page_title = "Quản lý Lịch khởi hành";
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
        <div class="sidebar">
            <div class="logo">
                <h2>🏔️ Tour Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="?act=admin_dashboard" class="nav-item">📊 Dashboard</a>
                <a href="?act=admin_tours" class="nav-item">🗺️ Quản lý Tour</a>
                <a href="?act=admin_departures" class="nav-item active">📅 Lịch khởi hành</a>
                <a href="?act=admin_guides" class="nav-item">👨‍💼 HDV</a>
                <a href="?act=admin_services" class="nav-item">🔔 Dịch vụ</a>
                <a href="?act=admin_logout" class="nav-item">🚪 Đăng xuất</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1>📅 Lịch khởi hành</h1>
                </div>
                <div class="header-right">
                    <span>Xin chào, <?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="content-area">
                <div class="container">
                    <!-- Header -->
                    <div class="header">
                        <h2>Quản lý Lịch khởi hành</h2>
                        <a href="?act=admin_departures_create" class="btn">+ Tạo lịch mới</a>
                    </div>

                    <!-- Messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Departures Table -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mã Tour</th>
                                <th>Tên Tour</th>
                                <th>Ngày khởi hành</th>
                                <th>Giờ</th>
                                <th>Số chỗ</th>
                                <th>Giá người lớn</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($departures) > 0): ?>
                                <?php foreach ($departures as $departure): ?>
                                <tr>
                                    <td><strong><?php echo $departure['tour_code']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($departure['tour_name'] ?? ''); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($departure['departure_date'] ?? '')); ?></td>
                                    <td><?php echo $departure['departure_time'] ? date('H:i', strtotime($departure['departure_time'])) : '--:--'; ?></td>
                                    <td><?php echo $departure['expected_slots']; ?> chỗ</td>
                                    <td><?php echo number_format($departure['price_adult']); ?> VNĐ</td>
                                    <td>
                                        <span class="status-<?php echo $departure['status']; ?>">
                                            <?php 
                                            $status_text = [
                                                'scheduled' => 'Đã lên lịch',
                                                'confirmed' => 'Đã xác nhận',
                                                'completed' => 'Đã hoàn thành',
                                                'cancelled' => 'Đã hủy'
                                            ];
                                            echo $status_text[$departure['status']] ?? $departure['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <!-- Thêm cột Hành động với link detail -->
<td>
    <div class="btn-group btn-group-sm" role="group">
        <a href="?act=admin_departure_detail&id=<?= $departure['departure_id'] ?>" 
           class="btn btn-info" title="Xem chi tiết & Phân bổ">
            <i class="fas fa-eye">Chi tiết</i>
        </a>
        <a href="?act=admin_departures_edit&id=<?= $departure['departure_id'] ?>" 
           class="btn btn-warning" title="Sửa">
            <i class="fas fa-edit">Sửa</i>
        </a>
        <a href="?act=admin_departures_delete&id=<?= $departure['departure_id'] ?>" 
           class="btn btn-danger" title="Xóa"
           onclick="return confirm('Xóa lịch khởi hành này?')">
            <i class="fas fa-trash">Xóa</i>
        </a>
    </div>
</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 20px;">
                                        <p>Chưa có lịch khởi hành nào.</p>
                                        <a href="?act=admin_departures_create" class="btn">Tạo lịch đầu tiên</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>