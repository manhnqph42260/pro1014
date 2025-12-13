<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// QUAN TRỌNG: DÙNG guide_id, KHÔNG DÙNG guide_logged_in
if (!isset($_SESSION['guide_id'])) {
    header('Location: index.php');
    exit();
}

$guide_name = $_SESSION['full_name'] ?? 'Hướng dẫn viên';
$guide_code = $_SESSION['guide_code'] ?? 'HDV';
date_default_timezone_set('Asia/Ho_Chi_Minh');
?>

<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'HDV Dashboard'; ?> - Tour Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        :root {
            --guide-primary: #0d6efd;
            --guide-secondary: #6c757d;
            --guide-success: #198754;
            --guide-info: #0dcaf0;
            --guide-warning: #ffc107;
            --guide-danger: #dc3545;
        }

        .guide-sidebar {
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            min-height: 100vh;
            padding: 0;
        }

        .guide-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .guide-sidebar .nav-link:hover,
        .guide-sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #0dcaf0;
        }

        .guide-sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
        }

        .guide-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }

        .guide-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .guide-card .card-header {
            background: white;
            border-bottom: 2px solid #0d6efd;
            font-weight: 600;
            padding: 15px 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar HDV – ĐẸP NHƯ CŨ -->
            <nav class="col-md-3 col-lg-2 d-md-block guide-sidebar">
                <div class="position-sticky pt-4">
                    <div class="text-center mb-4">
                        <h5 class="text-white">HDV Panel</h5>
                        <p class="text-white-50"><?php echo htmlspecialchars($guide_code); ?></p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="?act=guide-dashboard">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="?act=guide-schedule">
                                <i class="bi bi-calendar-week me-2"></i> Lịch Trình Tour
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="?act=guide-guest-list">
                                <i class="bi bi-people me-2"></i> Danh Sách Khách hàng
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="?act=guide-attendance-list">
                                <i class="bi bi-clipboard-check me-2"></i> Điểm Danh
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="?act=guide-journal">
                                <i class="bi bi-journal-text me-2"></i> Nhật Ký Tour
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?act=guide-special-requests">
                                <i class="bi bi-star me-2"></i> Yêu Cầu Đặc Biệt
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-warning" href="?act=guide-incident-report">
                                <i class="bi bi-exclamation-triangle me-2"></i> Báo Cáo Sự Cố
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link text-danger" href="?act=logout">
                                <i class="bi bi-box-arrow-right"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 guide-content">
                <div class="d-flex justify-content-between align-items-center pt-4 pb-2 mb-3 border-bottom">
                    <h1 class="h3"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                    <div class="text-muted">
                        Xin chào, <strong><?php echo htmlspecialchars($guide_name); ?></strong>
                    </div>
                </div>

                <!-- Thông báo thành công -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>