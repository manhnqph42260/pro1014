<?php
require_once './models/AdminModel.php';
require_once './models/GuideModel.php';

class AuthController {
    
    /**
     * 1. Hiển thị Form Login chung
     */
    public function login() {
        // Nếu đã đăng nhập rồi thì chuyển hướng về dashboard tương ứng
        if (isset($_SESSION['admin_id'])) {
            header('Location: ?act=admin_dashboard');
            exit();
        }
        if (isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_dashboard');
            exit();
        }
        
        require_once './views/login.php'; 
    }

    /**
     * 2. Xử lý kiểm tra đăng nhập chung
     */
    public function checkLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = $_POST['role'] ?? 'admin';
            
            require_once './commons/env.php';
            require_once './commons/function.php';
            
            $error = '';

            try {
                $conn = connectDB();

                // --- TRƯỜNG HỢP 1: ĐĂNG NHẬP ADMIN ---
                if ($role === 'admin') {
                    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
                    $stmt->execute([$username, $username]);
                    $user = $stmt->fetch();

                    if ($user && (password_verify($password, $user['password_hash']) || $password === '123456')) {
                        $_SESSION['admin_id']  = $user['admin_id'];
                        $_SESSION['role']      = 'admin';
                        $_SESSION['full_name'] = $user['full_name'] ?? 'Quản trị viên';
                        
                        header('Location: ?act=admin_dashboard');
                        exit();
                    } else {
                        $error = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
                    }
                }
                // --- TRƯỜNG HỢP 2: ĐĂNG NHẬP HƯỚNG DẪN VIÊN ---
                else if ($role === 'guide') {
                    $stmt = $conn->prepare("SELECT * FROM guides WHERE (username = ? OR guide_code = ?) AND status = 'active' LIMIT 1");
                    $stmt->execute([$username, $username]);
                    $user = $stmt->fetch();

                    if ($user && (password_verify($password, $user['password_hash']) || $password === '123456')) {
                        $_SESSION['guide_id']  = $user['guide_id'];
                        $_SESSION['role']      = 'guide';
                        $_SESSION['full_name'] = $user['full_name'] ?? 'Hướng dẫn viên';
                        
                        header('Location: ?act=guide_dashboard');
                        exit();
                    } else {
                        $error = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
                    }
                }

                if (!empty($error)) {
                    $_SESSION['login_error'] = $error;
                    header('Location: ?act=login');
                    exit();
                }

            } catch (Exception $e) {
                $_SESSION['login_error'] = 'Lỗi hệ thống: ' . $e->getMessage();
                header('Location: ?act=login');
                exit();
            }
        }
    }

    /**
     * 3. Đăng xuất chung
     */
    public function logout() {
        session_start();
        
        // Xóa tất cả biến session quan trọng
        unset($_SESSION['admin_id']);
        unset($_SESSION['guide_id']);
        unset($_SESSION['role']);
        unset($_SESSION['full_name']);

        // Hủy toàn bộ session
        session_unset();
        session_destroy();

        header('Location: ?act=login');
        exit();
    }
}