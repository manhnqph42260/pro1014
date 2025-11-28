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
                <a href="?act=admin_bookings" class="nav-item ">📋 Quản lý Booking</a>                
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
    
    <div class="col-md-4">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-calendar-check fs-1 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="card-title mb-0"><?php echo $departure_stats['total_departures'] ?? 0; ?></h3>
                        <p class="card-text text-muted mb-0">Lịch khởi hành</p>
                        <small class="text-muted">Đã xác nhận: <?php echo $departure_stats['confirmed'] ?? 0; ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-person-badge fs-1 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h3 class="card-title mb-0"><?php echo $guide_stats['total_guides'] ?? 0; ?></h3>
                        <p class="card-text text-muted mb-0">Hướng dẫn viên</p>
                        <small class="text-muted">Đang hoạt động</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-lightning me-2"></i>Thao tác nhanh</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="?act=admin_tours_create" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Tạo Tour mới
                    </a>
                    <a href="?act=admin_tours" class="btn btn-outline-primary">
                        <i class="bi bi-list-ul me-1"></i>Quản lý Tour
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="bi bi-calendar-plus me-1"></i>Tạo lịch trình
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Tours -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>Tour gần đây</span>
                    <a href="?act=admin_tours" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </h5>
                <div class="list-group list-group-flush">
                    <?php if (count($recent_tours) > 0): ?>
                        <?php foreach ($recent_tours as $tour): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($tour['tour_name']); ?></h6>
                                <small class="text-muted"><?php echo $tour['tour_code']; ?></small>
                            </div>
                            <span class="badge bg-<?php echo $tour['status'] === 'published' ? 'success' : 'warning'; ?>">
                                <?php echo $tour['status'] === 'published' ? 'Đã xuất bản' : 'Bản nháp'; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2">Chưa có tour nào</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Departures -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calendar-event me-2"></i>Lịch khởi hành sắp tới</span>
                    <a href="#" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </h5>
                <div class="list-group list-group-flush">
                    <?php if (count($upcoming_departures) > 0): ?>
                        <?php foreach ($upcoming_departures as $departure): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($departure['tour_name']); ?></h6>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($departure['departure_date'])); ?></small>
                            </div>
                            <span class="badge bg-<?php echo $departure['status'] === 'confirmed' ? 'success' : 'info'; ?>">
                                <?php echo $departure['status'] === 'confirmed' ? 'Đã xác nhận' : 'Đang chờ'; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-calendar-x fs-1"></i>
                            <p class="mt-2">Không có lịch khởi hành</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
