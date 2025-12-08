<?php
// Kiểm tra session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['guide_logged_in']) || $_SESSION['guide_logged_in'] !== true) {
    header('Location: index.php?act=guide_login');
    exit();
}

// Lấy thông tin HDV từ session
$guide_name = $_SESSION['guide_name'] ?? 'Hướng dẫn viên';
$guide_code = $_SESSION['guide_code'] ?? 'HDV001';
$guide_id = $_SESSION['guide_id'] ?? 1;

// Xác định trang hiện tại
$current_page = $_GET['act'] ?? 'guide_dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HDV - <?php echo $guide_name; ?> | <?php 
        switch($current_page) {
            case 'guide_dashboard': echo 'Dashboard'; break;
            case 'guide_my_tours': echo 'Tour của tôi'; break;
            case 'guide_journal': echo 'Nhật ký tour'; break;
            case 'guide_attendance': echo 'Điểm danh'; break;
            case 'guide_participants': echo 'Khách hàng'; break;
            case 'guide_special_requests': echo 'Yêu cầu đặc biệt'; break;
            case 'guide_tour_detail': echo 'Chi tiết tour'; break;
            default: echo 'Hệ thống HDV';
        }
    ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Guide Custom CSS -->
    <link rel="stylesheet" href="./views/admin/guides/guide_style.css">
    
    <style>
        /* Additional inline styles to preserve your original design */
        .guide-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        
        .guide-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
        }
        
        .timeline-item {
            border-left: 2px solid #667eea;
            padding-left: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -6px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #667eea;
        }
    </style>
</head>
<body>
    <!-- Guide Navigation Sidebar -->
    <div class="guide-sidebar" id="guideSidebar">
        <div class="sidebar-header position-relative">
            <div class="guide-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <h5 class="guide-name mb-1"><?php echo $guide_name; ?></h5>
            <p class="guide-code text-white-50 mb-0"><?php echo $guide_code; ?></p>
            <div class="mt-2">
                <span class="badge bg-success">Đang hoạt động</span>
            </div>
            <div class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-chevron-left"></i>
            </div>
        </div>
        
        <nav class="nav flex-column">
            <a href="?act=guide_dashboard" class="nav-link <?php echo $current_page == 'guide_dashboard' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="?act=guide_my_tours" class="nav-link <?php echo $current_page == 'guide_my_tours' ? 'active' : ''; ?>">
                <i class="bi bi-suitcase"></i>
                <span>Tour của tôi</span>
            </a>
            
            <a href="?act=guide_journal" class="nav-link <?php echo $current_page == 'guide_journal' ? 'active' : ''; ?>">
                <i class="bi bi-journal-text"></i>
                <span>Nhật ký tour</span>
            </a>
            
            <a href="?act=guide_attendance" class="nav-link <?php echo $current_page == 'guide_attendance' ? 'active' : ''; ?>">
                <i class="bi bi-clipboard-check"></i>
                <span>Điểm danh</span>
            </a>
            
            <a href="?act=guide_participants" class="nav-link <?php echo $current_page == 'guide_participants' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i>
                <span>Khách hàng</span>
            </a>
            
            <a href="?act=guide_special_requests" class="nav-link <?php echo $current_page == 'guide_special_requests' ? 'active' : ''; ?>">
                <i class="bi bi-star"></i>
                <span>Yêu cầu đặc biệt</span>
            </a>
            
            <div class="mt-4 px-3">
                <hr class="text-white-50">
            </div>
            
            <a href="?act=guide_logout" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>Đăng xuất</span>
            </a>
            
            <a href="?act=admin_dashboard" class="nav-link text-warning">
                <i class="bi bi-arrow-left"></i>
                <span>Về Admin</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content-with-sidebar" id="mainContent">
        <!-- Content will be loaded here -->
        <?php 
        // Nội dung chính sẽ được include ở đây
        // Ví dụ: require_once 'dashboard.php'; 
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Toggle sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('guideSidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('sidebarToggle');
        
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            
            // Thay đổi icon
            const icon = toggleBtn.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.className = 'bi bi-chevron-right';
            } else {
                icon.className = 'bi bi-chevron-left';
            }
        });
        
        // Kiểm tra offline mode
        if (localStorage.getItem('guide_offline_mode') === 'true') {
            showOfflineIndicator();
        }
        
        // Offline mode toggle (từ dashboard)
        const offlineToggle = document.getElementById('offlineToggle');
        if (offlineToggle) {
            offlineToggle.addEventListener('change', function() {
                if (this.checked) {
                    localStorage.setItem('guide_offline_mode', 'true');
                    showOfflineIndicator();
                } else {
                    localStorage.removeItem('guide_offline_mode');
                    hideOfflineIndicator();
                }
            });
        }
        
        function showOfflineIndicator() {
            let indicator = document.getElementById('offlineIndicator');
            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'offlineIndicator';
                indicator.className = 'offline-indicator';
                indicator.innerHTML = `
                    <i class="bi bi-wifi-off"></i>
                    <span>Chế độ Offline</span>
                `;
                document.body.appendChild(indicator);
            }
        }
        
        function hideOfflineIndicator() {
            const indicator = document.getElementById('offlineIndicator');
            if (indicator) {
                indicator.remove();
            }
        }
        
        // Auto-hide offline indicator after 5 seconds
        setTimeout(() => {
            const indicator = document.getElementById('offlineIndicator');
            if (indicator) {
                indicator.style.opacity = '0';
                setTimeout(() => indicator.remove(), 500);
            }
        }, 5000);
    });
    </script>
</body>
</html>