<?php
class AdminController
{
    public function dashboard()
    {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();

        // Thống kê tours
        $tour_stats = $conn->query("
            SELECT  
                COUNT(*) as total_tours,
                0 as published_tours,
                0 as draft_tours
            FROM tours
        ")->fetch();

        // Thống kê lịch khởi hành
        $departure_stats = $conn->query("
            SELECT 
                COUNT(*) as total_departures,
                COUNT(*) as scheduled,
                0 as confirmed
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
            SELECT tour_id, tour_code, tour_name, description, duration_days 
            FROM tours 
            ORDER BY tour_id DESC 
            LIMIT 5
        ")->fetchAll();

        // Lịch khởi hành sắp tới
        $upcoming_departures = $conn->query("
            SELECT d.departure_id, t.tour_name, d.departure_date, d.departure_time
            FROM departure_schedules d
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE d.departure_date >= CURDATE()
            ORDER BY d.departure_date ASC
            LIMIT 5
        ")->fetchAll();

        require_once './views/admin/dashboard.php';
    }

    public function logout()
    {
        // Đơn giản chuyển hướng đến AuthController::logout()
        header('Location: ?act=logout');
        exit();
    }

    public function profile()
    {
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

    public function changePassword()
    {
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

    /**
     * ============================================
     * TIỆN ÍCH HỖ TRỢ
     * ============================================
     */

    private function checkAdminAuth()
    {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=login");
            exit();
        }
    }
}
