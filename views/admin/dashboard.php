<?php
$page_title = "Dashboard Quản trị";
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quản trị Tour</title>
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

        .nav-item:hover,
        .nav-item.active {
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .content-area {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 2rem;
            color: #2c3e50;
            margin: 0;
        }

        .quick-actions {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
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
                <a href="?act=admin_dashboard" class="nav-item active">📊 Dashboard</a>
                <a href="?act=admin_tours" class="nav-item">🗺️ Quản lý Tour</a>
                <a href="?act=admin_departures" class="nav-item">📅 Lịch khởi hành</a>
                <a href="?act=admin_guides" class="nav-item">👨‍💼 HDV</a>
                <a href="?act=admin_services" class="nav-item">🔔 Dịch vụ</a>
                <a href="?act=admin_logout" class="nav-item">🚪 Đăng xuất</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1>Dashboard Quản trị</h1>
                </div>
                <div class="header-right">
                    <span>Xin chào, <?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="content-area">
                <!-- Stats -->
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">🏔️</div>
                        <div class="stat-info">
                            <h3><?php echo $tour_stats['total_tours'] ?? 0; ?></h3>
                            <p>Tổng số Tour</p>
                            <small>Đã xuất bản: <?php echo $tour_stats['published_tours'] ?? 0; ?></small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">📅</div>
                        <div class="stat-info">
                            <h3><?php echo $departure_stats['total_departures'] ?? 0; ?></h3>
                            <p>Lịch khởi hành</p>
                            <small>Đã xác nhận: <?php echo $departure_stats['confirmed'] ?? 0; ?></small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">👨‍💼</div>
                        <div class="stat-info">
                            <h3><?php echo $guide_stats['total_guides'] ?? 0; ?></h3>
                            <p>Hướng dẫn viên</p>
                            <small>Đang hoạt động</small>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h3>Thao tác nhanh</h3>
                    <a href="?act=admin_tours_create" class="btn">➕ Tạo Tour mới</a>
                    <a href="?act=admin_tours" class="btn">📋 Quản lý Tour</a>
                    <a href="?act=admin_departures" class="btn">📅 Lịch trình</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>