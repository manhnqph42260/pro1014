<?php
class GuideController {
    
    public function __construct() {
        // KHÔNG gọi session_start() ở đây
    }
    
    // 1. Hiển thị form đăng nhập
    public function login() {
        // Nếu đã đăng nhập, chuyển hướng đến dashboard
        if (isset($_SESSION['guide_logged_in']) && $_SESSION['guide_logged_in'] === true) {
            header('Location: index.php?act=guide_dashboard');
            exit();
        }
        
        require_once './views/admin/guides/guide_login.php';
    }
    
    // 2. Xử lý đăng nhập
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // KIỂM TRA THÔNG TIN ĐĂNG NHẬP
            if (($username === 'HDV001' || $username === 'guidea@tour.com') && $password === 'password123') {
                // Đăng nhập thành công
                $_SESSION['guide_logged_in'] = true;
                $_SESSION['guide_id'] = 1;
                $_SESSION['guide_name'] = 'Nguyễn Văn A';
                $_SESSION['guide_code'] = 'HDV001';
                
                // Chuyển hướng đến dashboard
                header('Location: index.php?act=guide_dashboard');
                exit();
            }
            elseif (($username === 'HDV002' || $username === 'guideb@tour.com') && $password === 'password123') {
                // Đăng nhập thành công
                $_SESSION['guide_logged_in'] = true;
                $_SESSION['guide_id'] = 2;
                $_SESSION['guide_name'] = 'Trần Thị B';
                $_SESSION['guide_code'] = 'HDV002';
                
                // Chuyển hướng đến dashboard
                header('Location: index.php?act=guide_dashboard');
                exit();
            }
            else {
                // Đăng nhập thất bại
                $_SESSION['login_error'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
                header('Location: index.php?act=guide_login');
                exit();
            }
        } else {
            // Nếu không phải POST, quay lại trang login
            header('Location: index.php?act=guide_login');
            exit();
        }
    }
    
    // 3. Dashboard HDV
    public function dashboard() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['guide_logged_in']) || $_SESSION['guide_logged_in'] !== true) {
            header('Location: index.php?act=guide_login');
            exit();
        }
        
        require_once './views/admin/guides/dashboard.php';
    }
    
    // 4. Danh sách tour của HDV
    public function myTours() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['guide_logged_in']) || $_SESSION['guide_logged_in'] !== true) {
            header('Location: index.php?act=guide_login');
            exit();
        }
        
        require_once './views/admin/guides/list.php';
    }
    
    // 5. Xem chi tiết tour
    public function tourDetail($id) {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['guide_logged_in']) || $_SESSION['guide_logged_in'] !== true) {
            header('Location: index.php?act=guide_login');
            exit();
        }
        
        require_once './views/admin/guides/tour_detail.php';
    }
    
    // 6. Nhật ký tour
    public function journal() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['guide_logged_in']) || $_SESSION['guide_logged_in'] !== true) {
            header('Location: index.php?act=guide_login');
            exit();
        }
        
        require_once './views/admin/guides/journal.php';
    }
    
    // 7. Đăng xuất
    public function logout() {
        // Xóa session
        unset($_SESSION['guide_logged_in']);
        unset($_SESSION['guide_id']);
        unset($_SESSION['guide_name']);
        unset($_SESSION['guide_code']);
        
        // Chuyển hướng về trang login
        header('Location: index.php?act=guide_login');
        exit();
    }
}
?>