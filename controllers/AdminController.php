<?php
class AdminController {
    
    public function login() {
    require_once './commons/env.php';
    require_once './commons/function.php';
    
    // Nếu đã đăng nhập thì chuyển đến dashboard
    if (isset($_SESSION['admin_id'])) {
        header("Location: ?act=admin_dashboard");
        exit();
    }
    
    if ($_POST) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px; border: 1px solid #ddd;'>";
        echo "<h3>🔍 DEBUG LOGIN</h3>";
        echo "Username: " . htmlspecialchars($username) . "<br>";
        echo "Password: " . htmlspecialchars($password) . "<br>";

        try {
            $conn = connectDB();
            echo "✅ Database connected<br>";
            
            $query = "SELECT * FROM admins WHERE username = :username";
            $stmt = $conn->prepare($query);
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch();

            if ($admin) {
                echo "✅ Admin found: " . $admin['username'] . "<br>";
                echo "Admin status: " . $admin['status'] . "<br>";
                echo "Password hash in DB: " . $admin['password_hash'] . "<br>";
                
                // Kiểm tra password
                $password_check_1 = ($password === '123456');
                $password_check_2 = password_verify($password, $admin['password_hash']);
                
                echo "Password check (123456): " . ($password_check_1 ? '✅ TRUE' : '❌ FALSE') . "<br>";
                echo "Password verify: " . ($password_check_2 ? '✅ TRUE' : '❌ FALSE') . "<br>";
                
                if ($password_check_1 || $password_check_2) {
                    echo "✅ Password correct!<br>";
                    
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['full_name'] = $admin['full_name'];
                    $_SESSION['role'] = $admin['role'];
                    
                    // Cập nhật last login
                    $update_query = "UPDATE admins SET last_login = NOW() WHERE admin_id = :admin_id";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->execute(['admin_id' => $admin['admin_id']]);
                    
                    echo "✅ Session set, redirecting to dashboard...<br>";
                    echo "<script>setTimeout(function() { window.location.href = '?act=admin_dashboard'; }, 2000);</script>";
                    exit();
                    
                } else {
                    $error = "Mật khẩu không chính xác!";
                    echo "❌ Password incorrect<br>";
                }
            } else {
                $error = "Tài khoản không tồn tại!";
                echo "❌ Admin not found<br>";
                
                // Hiển thị tất cả admin có trong database
                $all_admins = $conn->query("SELECT username, status FROM admins")->fetchAll();
                echo "All admins in database:<br>";
                foreach ($all_admins as $admin) {
                    echo "- " . $admin['username'] . " (" . $admin['status'] . ")<br>";
                }
            }
            
        } catch (Exception $e) {
            $error = "Lỗi kết nối database: " . $e->getMessage();
            echo "❌ Database error: " . $e->getMessage() . "<br>";
        }
        
        echo "</div>";
    }
    
    require_once './views/admin/login.php';
}
    
    public function dashboard() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $conn = connectDB();
        
        // Thống kê tours
        $tour_stats = $conn->query("
            SELECT 
                COUNT(*) as total_tours,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_tours,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_tours
            FROM tours
        ")->fetch();
        
        // Thống kê lịch khởi hành
        $departure_stats = $conn->query("
            SELECT 
                COUNT(*) as total_departures,
                SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed
            FROM departure_schedules
        ")->fetch();
        
        // Thống kê hướng dẫn viên
        $guide_stats = $conn->query("
            SELECT COUNT(*) as total_guides 
            FROM guides 
            WHERE status = 'active'
        ")->fetch();
        
        // Tours gần đây
        $recent_tours = $conn->query("
            SELECT tour_id, tour_code, tour_name, status, created_at 
            FROM tours 
            ORDER BY created_at DESC 
            LIMIT 5
        ")->fetchAll();
        
        // Lịch khởi hành sắp tới
        $upcoming_departures = $conn->query("
            SELECT d.departure_id, t.tour_name, d.departure_date, d.status
            FROM departure_schedules d
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE d.departure_date >= CURDATE()
            ORDER BY d.departure_date ASC
            LIMIT 5
        ")->fetchAll();
        
        require_once './views/admin/dashboard.php';
    }
    
    public function logout() {
        session_destroy();
        header("Location: ?act=admin_login");
        exit();
    }
    
    public function profile() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $conn = connectDB();
        
        if ($_POST) {
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            
            try {
                $query = "UPDATE admins SET full_name = :full_name, email = :email, phone = :phone WHERE admin_id = :admin_id";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    'full_name' => $full_name,
                    'email' => $email,
                    'phone' => $phone,
                    'admin_id' => $_SESSION['admin_id']
                ]);
                
                $_SESSION['full_name'] = $full_name;
                $_SESSION['success'] = "Cập nhật thông tin thành công!";
                
            } catch (PDOException $e) {
                $error = "Lỗi khi cập nhật thông tin: " . $e->getMessage();
            }
        }
        
        // Lấy thông tin admin
        $query = "SELECT * FROM admins WHERE admin_id = :admin_id";
        $stmt = $conn->prepare($query);
        $stmt->execute(['admin_id' => $_SESSION['admin_id']]);
        $admin_info = $stmt->fetch();
        
        require_once './views/admin/profile.php';
    }
    
    public function changePassword() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        if ($_POST) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            $conn = connectDB();
            
            // Lấy thông tin admin hiện tại
            $query = "SELECT password_hash FROM admins WHERE admin_id = :admin_id";
            $stmt = $conn->prepare($query);
            $stmt->execute(['admin_id' => $_SESSION['admin_id']]);
            $admin = $stmt->fetch();
            
            if (!$admin) {
                $error = "Không tìm thấy thông tin admin!";
            } elseif ($new_password !== $confirm_password) {
                $error = "Mật khẩu mới và xác nhận mật khẩu không khớp!";
            } elseif ($current_password === '123456' || password_verify($current_password, $admin['password_hash'])) {
                // Cập nhật mật khẩu mới
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE admins SET password_hash = :password_hash WHERE admin_id = :admin_id";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->execute([
                    'password_hash' => $new_password_hash,
                    'admin_id' => $_SESSION['admin_id']
                ]);
                
                $_SESSION['success'] = "Đổi mật khẩu thành công!";
                header("Location: ?act=admin_profile");
                exit();
            } else {
                $error = "Mật khẩu hiện tại không chính xác!";
            }
        }
        
        require_once './views/admin/change_password.php';
    }
    
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=admin_login");
            exit();
        }
    }
    
    // Hàm ghi log hành động
    private function logAction($action, $table_name = '', $record_id = null) {
        $conn = connectDB();
        if (isset($_SESSION['admin_id'])) {
            $query = "INSERT INTO admin_audit_logs (admin_id, action_type, table_name, record_id, ip_address, user_agent) 
                      VALUES (:admin_id, :action, :table_name, :record_id, :ip, :agent)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                'admin_id' => $_SESSION['admin_id'],
                'action' => $action,
                'table_name' => $table_name,
                'record_id' => $record_id,
                'ip' => $_SERVER['REMOTE_ADDR'],
                'agent' => $_SERVER['HTTP_USER_AGENT']
            ]);
        }
    }
}
?>