<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị Tour</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- jQuery với noConflict -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Sử dụng jQuery noConflict nếu cần
var $j = jQuery.noConflict();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
        }

        .sidebar .nav-link {
            color: #bdc3c7;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #34495e;
            color: white;
            border-left-color: #3498db;
        }

        .main-content {
            background: #f8f9fa;
        }

        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky">
                    <div class="sidebar-header p-3">
                        <h4 class="text-center">Admin Panel</h4>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-calendar me-2"></i>
                                Lịch làm việc
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="tourDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-map me-2"></i>
                                Quản lý Tour
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Danh sách Tour</a></li>
                                <li><a class="dropdown-item" href="#">Tạo Tour mới</a></li>
                                <li><a class="dropdown-item" href="#">Loại Tour</a></li>
                            </ul>
                        </li>

                        <!-- THÊM DANH MỤC BOOKING VÀO ĐÂY -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-journal-text me-2"></i>
                                Booking
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-clipboard-check me-2"></i>
                                Điểm danh
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-star me-2"></i>
                                Yêu cầu đặc biệt
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Báo cáo sự cố
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-wifi-off me-2"></i>
                                Chế độ Offline
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                        <li class="nav-item mt-5">
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="index.php?logout=1">
                                <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                            </a>
                        </li>

                        </li>


                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 main-content ms-sm-auto px-md-4">
                <header class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="text-muted">Xin chào, <?php echo $_SESSION['full_name'] ?? 'Admin'; ?></span>
                        </div>
                    </div>
                </header>

                <div class="container-fluid">
                    <!-- Alerts -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success'];
                            unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error'];
                            unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>