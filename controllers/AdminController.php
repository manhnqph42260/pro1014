<?php
class AdminController
{
    public function login()
    {
        // Khởi tạo session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Dùng config và hàm chung
        require_once './commons/env.php';
        require_once './commons/function.php';

        // Nếu đã đăng nhập thì chuyển đến dashboard
        if (isset($_SESSION['admin_id'])) {
            header("Location: index.php?act=admin_dashboard");
            exit();
        }

        // Nếu form được submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

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

                        // Set session
                        $_SESSION['admin_id'] = $admin['admin_id'];
                        $_SESSION['username'] = $admin['username'];
                        $_SESSION['full_name'] = $admin['full_name'];
                        $_SESSION['role'] = $admin['role'] ?? 'admin';

                        // Cập nhật last_login
                        $update_query = "UPDATE admins SET last_login = NOW() WHERE admin_id = :admin_id";
                        $update_stmt = $conn->prepare($update_query);
                        $update_stmt->execute(['admin_id' => $admin['admin_id']]);

                        echo "✅ Session set, redirecting to dashboard...<br>";

                        // Vì đã có output debug, dùng JS redirect
                        echo "<script>setTimeout(function() { window.location.href = 'index.php?act=admin_dashboard'; }, 1200);</script>";
                        exit();
                    } else {
                        $error = "Mật khẩu không chính xác!";
                        echo "❌ Password incorrect<br>";
                    }
                } else {
                    $error = "Tài khoản không tồn tại!";
                    echo "❌ Admin not found<br>";

                    // Hiển thị tất cả admin trong DB
                    $all_admins = $conn->query("SELECT username, status FROM admins")->fetchAll();
                    echo "All admins in database:<br>";
                    foreach ($all_admins as $a) {
                        echo "- " . $a['username'] . " (" . $a['status'] . ")<br>";
                    }
                }
            } catch (Exception $e) {
                $error = "Lỗi kết nối database: " . $e->getMessage();
                echo "❌ Database error: " . $e->getMessage() . "<br>";
            }

            echo "</div>";
        }

        // Nếu chưa POST, hiển thị view login admin
        require_once './views/admin/login.php';
    }


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
/**
 * Xử lý đăng nhập chung cho cả admin và HDV - PHIÊN BẢN DEBUG
 */
public function processLogin()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px; border: 1px solid #ddd;'>";
    echo "<h3>🔍 DEBUG PROCESS LOGIN</h3>";

    // Nếu đã đăng nhập, redirect
    if (isset($_SESSION['admin_id'])) {
        echo "✅ Đã đăng nhập admin, redirect đến dashboard...<br>";
        header("Location: ?act=admin_dashboard");
        exit();
    } elseif (isset($_SESSION['guide_id'])) {
        echo "✅ Đã đăng nhập HDV, redirect đến dashboard...<br>";
        header("Location: ?act=guide_dashboard");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = $_POST['role'] ?? 'admin';

        echo "Username: " . htmlspecialchars($username) . "<br>";
        echo "Password: " . htmlspecialchars($password) . "<br>";
        echo "Role: " . htmlspecialchars($role) . "<br>";

        require_once './commons/env.php';
        require_once './commons/function.php';

        try {
            $conn = connectDB();
            echo "✅ Database connected<br>";

            if ($role === 'admin') {
                echo "🔄 Xử lý login admin...<br>";
                // Login cho admin (giữ nguyên)
                $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch();

                if ($user) {
                    echo "✅ Admin found: " . $user['username'] . "<br>";
                    
                    if (password_verify($password, $user['password_hash']) || $password === '123456') {
                        // Tạo session cho admin
                        session_unset();
                        $_SESSION['admin_id'] = $user['admin_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['role'] = 'admin';
                        $_SESSION['email'] = $user['email'];
                        
                        echo "✅ Admin login successful! Redirecting...<br>";
                        echo "<script>setTimeout(function() { window.location.href = '?act=admin_dashboard'; }, 1000);</script>";
                        exit();
                    } else {
                        echo "❌ Admin password incorrect<br>";
                    }
                } else {
                    echo "❌ Admin not found<br>";
                }
            } else { 
                echo "🔄 Xử lý login HDV...<br>";
                
                // QUAN TRỌNG: Thử nhiều cách để tìm HDV
                $query = "SELECT * FROM guides WHERE 
                         (email = :username OR guide_code = :username OR full_name = :username) 
                         AND status = 'active' 
                         LIMIT 1";
                
                $stmt = $conn->prepare($query);
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch();

                if ($user) {
                    echo "✅ Guide found!<br>";
                    echo "Guide ID: " . $user['guide_id'] . "<br>";
                    echo "Guide Code: " . ($user['guide_code'] ?? 'NULL') . "<br>";
                    echo "Full Name: " . $user['full_name'] . "<br>";
                    echo "Email: " . $user['email'] . "<br>";
                    echo "Status: " . $user['status'] . "<br>";
                    
                    // Kiểm tra password - thử nhiều cách
                    $password_ok = false;
                    
                    // Cách 1: Kiểm tra nếu có cột password_hash
                    if (isset($user['password_hash']) && !empty($user['password_hash'])) {
                        echo "Có password_hash trong database<br>";
                        if (password_verify($password, $user['password_hash'])) {
                            $password_ok = true;
                            echo "✅ Password verify thành công<br>";
                        } else {
                            echo "❌ Password verify thất bại<br>";
                        }
                    }
                    
                    // Cách 2: Kiểm tra password mặc định
                    if (!$password_ok && $password === 'password123') {
                        $password_ok = true;
                        echo "✅ Password mặc định đúng<br>";
                    }
                    
                    // Cách 3: Kiểm tra password là 123456
                    if (!$password_ok && $password === '123456') {
                        $password_ok = true;
                        echo "✅ Password 123456 đúng<br>";
                    }
                    
                    if ($password_ok) {
                        // Tạo session cho HDV
                        session_unset();
                        $_SESSION['guide_id'] = $user['guide_id'];
                        $_SESSION['guide_code'] = $user['guide_code'];
                        $_SESSION['username'] = $user['full_name'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['role'] = 'guide';
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['guide_phone'] = $user['phone'];
                        
                        echo "✅ Guide login successful!<br>";
                        echo "Session guide_id: " . $_SESSION['guide_id'] . "<br>";
                        echo "Redirecting to guide dashboard...<br>";
                        
                        echo "<script>
                            setTimeout(function() { 
                                window.location.href = 'index.php?act=guide_dashboard'; 
                            }, 1000);
                        </script>";
                        exit();
                    } else {
                        echo "❌ Guide password không đúng<br>";
                        $_SESSION['login_error'] = "Mật khẩu không đúng!";
                    }
                } else {
                    echo "❌ Guide not found<br>";
                    
                    // Hiển thị tất cả HDV có trong database để debug
                    echo "<br>📋 Danh sách HDV trong database:<br>";
                    $all_guides = $conn->query("SELECT guide_id, guide_code, full_name, email, status FROM guides")->fetchAll();
                    foreach ($all_guides as $guide) {
                        echo "- ID: {$guide['guide_id']}, Code: {$guide['guide_code']}, Name: {$guide['full_name']}, Email: {$guide['email']}, Status: {$guide['status']}<br>";
                    }
                    
                    $_SESSION['login_error'] = "HDV không tồn tại hoặc đã bị khóa!";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ Database error: " . $e->getMessage() . "<br>";
            $_SESSION['login_error'] = "Lỗi kết nối database!";
        }
        
        echo "</div>";
        header("Location: index.php?act=login");
        exit();
        
    } else {
        echo "❌ Không phải POST request<br>";
        echo "</div>";
        header("Location: index.php?act=login");
        exit();
    }
}
    public function logout()
{
    // 1. Xóa tất cả biến session quan trọng
    unset($_SESSION['admin_id']);
    unset($_SESSION['guide_id']);
    unset($_SESSION['username']);
    unset($_SESSION['full_name']);
    unset($_SESSION['email']);
    unset($_SESSION['role']);
    unset($_SESSION['guide_code']);
    
    // 2. Xóa session hoàn toàn
    session_unset();
    session_destroy();
    
    // 3. Quay về trang login chính (cho phép chọn admin/guide)
    header("Location: index.php?act=login");
    exit();
        // Xóa tất cả biến session
    session_unset();
    
    // Hủy session
    session_destroy();
    
    // Quay về trang login chung
    header("Location: index.php?act=login");
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
     * PHẦN HDV - HƯỚNG DẪN VIÊN
     * ============================================
     */

    /**
     * Đăng nhập HDV
     */
    public function guideLogin()
    {
        require_once './commons/env.php';
        require_once './commons/function.php';

        // Nếu đã đăng nhập thì chuyển đến dashboard tương ứng
        if (isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_dashboard');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // DEBUG: Hiển thị thông tin nhập
            echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px; border: 1px solid #ddd;'>";
            echo "<h3>🔍 DEBUG HDV LOGIN</h3>";
            echo "Username: " . htmlspecialchars($username) . "<br>";
            echo "Password: " . htmlspecialchars($password) . "<br>";

            try {
                $conn = connectDB();
                echo "✅ Database connected<br>";

                // Kiểm tra cột password_hash có tồn tại không
                $checkColumn = $conn->query("SHOW COLUMNS FROM guides LIKE 'password_hash'");
                $hasPasswordColumn = $checkColumn->rowCount() > 0;
                echo "Password column exists: " . ($hasPasswordColumn ? '✅ YES' : '❌ NO') . "<br>";

                // Query với password_hash trong bảng guides
                if ($hasPasswordColumn) {
                    $query = "SELECT * FROM guides 
                         WHERE (email = :username OR guide_code = :username OR username = :username) 
                         AND status = 'active'";
                } else {
                    // Fallback nếu chưa có cột password_hash
                    $query = "SELECT * FROM guides 
                         WHERE (email = :username OR guide_code = :username) 
                         AND status = 'active'";
                }

                $stmt = $conn->prepare($query);
                $stmt->execute([':username' => $username]);
                $guide = $stmt->fetch();

                if ($guide) {
                    echo "✅ Guide found: " . $guide['full_name'] . " (" . $guide['guide_code'] . ")<br>";
                    echo "Guide status: " . $guide['status'] . "<br>";

                    if ($hasPasswordColumn && isset($guide['password_hash'])) {
                        echo "Password hash in DB: " . $guide['password_hash'] . "<br>";

                        // Kiểm tra password với bcrypt
                        $password_check = password_verify($password, $guide['password_hash']);
                        echo "Password verify result: " . ($password_check ? '✅ TRUE' : '❌ FALSE') . "<br>";

                        // Cũng check với mật khẩu mặc định
                        $password_check_default = ($password === 'password123');
                        echo "Password check (password123): " . ($password_check_default ? '✅ TRUE' : '❌ FALSE') . "<br>";

                        if ($password_check || $password_check_default) {
                            echo "✅ Password correct!<br>";

                            // Lưu thông tin HDV vào session
                            $_SESSION['guide_id'] = $guide['guide_id'];
                            $_SESSION['guide_code'] = $guide['guide_code'];
                            $_SESSION['guide_name'] = $guide['full_name'];
                            $_SESSION['guide_email'] = $guide['email'];
                            $_SESSION['guide_phone'] = $guide['phone'];
                            $_SESSION['guide_languages'] = json_decode($guide['languages'] ?? '[]', true);
                            $_SESSION['guide_skills'] = json_decode($guide['skills'] ?? '[]', true);
                            $_SESSION['guide_role'] = 'guide';

                            echo "✅ Session set, redirecting to guide dashboard...<br>";
                            echo "<script>setTimeout(function() { window.location.href = '?act=guide_dashboard'; }, 2000);</script>";
                            exit();
                        } else {
                            $error = "Mật khẩu không chính xác!";
                            echo "❌ Password incorrect<br>";
                        }
                    } else {
                        // Nếu chưa có password_hash trong DB, cho phép đăng nhập với password mặc định
                        echo "⚠️ No password_hash column, using default password check<br>";

                        if ($password === 'password123') {
                            echo "✅ Password correct!<br>";

                            // Lưu thông tin HDV vào session
                            $_SESSION['guide_id'] = $guide['guide_id'];
                            $_SESSION['guide_code'] = $guide['guide_code'];
                            $_SESSION['guide_name'] = $guide['full_name'];
                            $_SESSION['guide_email'] = $guide['email'];
                            $_SESSION['guide_phone'] = $guide['phone'];
                            $_SESSION['guide_languages'] = json_decode($guide['languages'] ?? '[]', true);
                            $_SESSION['guide_skills'] = json_decode($guide['skills'] ?? '[]', true);
                            $_SESSION['guide_role'] = 'guide';

                            echo "✅ Session set, redirecting to guide dashboard...<br>";
                            echo "<script>setTimeout(function() { window.location.href = '?act=guide_dashboard'; }, 2000);</script>";
                            exit();
                        } else {
                            $error = "Mật khẩu không chính xác!";
                            echo "❌ Password incorrect<br>";
                        }
                    }
                } else {
                    $error = "HDV không tồn tại hoặc đã bị khóa!";
                    echo "❌ Guide not found or inactive<br>";

                    // Hiển thị tất cả HDV có trong database
                    $all_guides = $conn->query("SELECT guide_code, full_name, email, status FROM guides")->fetchAll();
                    echo "All guides in database:<br>";
                    foreach ($all_guides as $g) {
                        echo "- " . $g['guide_code'] . " - " . $g['full_name'] . " (" . $g['email'] . ") - " . $g['status'] . "<br>";
                    }
                }
            } catch (Exception $e) {
                $error = "Lỗi kết nối database: " . $e->getMessage();
                echo "❌ Database error: " . $e->getMessage() . "<br>";
                echo "Error details: <pre>" . print_r($e, true) . "</pre><br>";
            }

            echo "</div>";
        }

        // Render view
        require_once './views/admin/guide_login.php';
    }
    /**
     * Dashboard HDV
     */
    public function guideDashboard()
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: index.php');
            exit();
        }
        echo "<h1 style='color:green; text-align:center; margin-top:100px;'>CHÀO MỪNG HDV {$_SESSION['full_name']} ĐÃ ĐĂNG NHẬP THÀNH CÔNG!</h1>";
        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Lấy thông tin HDV
        $query = "SELECT * FROM guides WHERE guide_id = :guide_id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':guide_id' => $guide_id]);
        $guide_info = $stmt->fetch();

        // Lấy tour hiện tại của HDV
        $current_tours = $conn->prepare("
            SELECT t.*, d.departure_date, d.departure_time, d.meeting_point 
            FROM tours t
            JOIN departure_schedules d ON t.tour_id = d.tour_id
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE ga.guide_id = :guide_id 
            AND d.departure_date >= CURDATE() 
            AND d.status IN ('scheduled', 'confirmed')
            ORDER BY d.departure_date ASC
            LIMIT 5
        ");
        $current_tours->execute([':guide_id' => $guide_id]);
        $current_tours = $current_tours->fetchAll();

        // Thống kê tour
        $tour_stats_query = $conn->prepare("
            SELECT 
                COUNT(DISTINCT d.tour_id) as total_tours,
                SUM(CASE WHEN d.departure_date < CURDATE() THEN 1 ELSE 0 END) as completed_tours,
                SUM(CASE WHEN d.departure_date >= CURDATE() THEN 1 ELSE 0 END) as active_tours
            FROM departure_schedules d
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE ga.guide_id = :guide_id
        ");
        $tour_stats_query->execute([':guide_id' => $guide_id]);
        $tour_stats = $tour_stats_query->fetch();

        // Lịch trình hôm nay
        $today = date('Y-m-d');
        $today_schedule = $conn->prepare("
            SELECT t.tour_name, d.departure_time, d.meeting_point 
            FROM departure_schedules d
            JOIN tours t ON d.tour_id = t.tour_id
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE ga.guide_id = :guide_id 
            AND d.departure_date = :today
            ORDER BY d.departure_time ASC
        ");
        $today_schedule->execute([':guide_id' => $guide_id, ':today' => $today]);
        $today_schedule = $today_schedule->fetchAll();

        $data = [
            'page_title' => 'Dashboard HDV',
            'guide_info' => $guide_info,
            'current_tours' => $current_tours,
            'tour_stats' => $tour_stats,
            'today_schedule' => $today_schedule,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'active' => true]
            ]
        ];
        require_once './views/admin/guides/header.php';
        require_once './views/admin/guides/dashboard.php';
        require_once './views/admin/guides/footer.php';
    }

    /**
     * Đăng xuất HDV
     */
    public function guideLogout()
    {
        // Xóa session HDV
        unset($_SESSION['guide_id']);
        unset($_SESSION['guide_code']);
        unset($_SESSION['guide_name']);
        unset($_SESSION['guide_email']);
        unset($_SESSION['guide_phone']);
        unset($_SESSION['guide_languages']);
        unset($_SESSION['guide_skills']);
        unset($_SESSION['guide_role']);

        unset($_SESSION['guide_id'], $_SESSION['guide_code'], $_SESSION['role'], $_SESSION['full_name']);
        header('Location: index.php');
        exit();
    }

    /**
     * Lịch làm việc HDV
     */
    public function guideSchedule()
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Lấy lịch làm việc của HDV
        $schedule_query = $conn->prepare("
            SELECT 
                d.departure_date,
                d.departure_time,
                t.tour_code,
                t.tour_name,
                t.destination,
                d.status as departure_status,
                ga.assignment_type
            FROM departure_schedules d
            JOIN tours t ON d.tour_id = t.tour_id
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE ga.guide_id = :guide_id
            AND d.departure_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ORDER BY d.departure_date ASC, d.departure_time ASC
        ");
        $schedule_query->execute([':guide_id' => $guide_id]);
        $schedule = $schedule_query->fetchAll();

        // Nhóm theo tuần
        $weekly_schedule = [];
        foreach ($schedule as $item) {
            $week = date('W', strtotime($item['departure_date']));
            if (!isset($weekly_schedule[$week])) {
                $weekly_schedule[$week] = [];
            }
            $weekly_schedule[$week][] = $item;
        }

        $data = [
            'page_title' => 'Lịch làm việc HDV',
            'schedule' => $schedule,
            'weekly_schedule' => $weekly_schedule,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Lịch làm việc', 'active' => true]
            ]
        ];

        require_once './views/admin/guide/header.php';
        require_once './views/admin/guide/schedule.php';
        require_once './views/admin/guide/footer.php';
    }


    public function guideMyTours()
    {
        if (!isset($_SESSION['guide_id'])) {
            header('Location: index.php');
            exit();
        }

        $page_title = "Tour của tôi";
        require_once './views/admin/guides/header.php';
        require_once './views/admin/guides-admin/list.php';
        require_once './views/admin/guides/footer.php';
    }
    /**
     * Chi tiết tour HDV được phân công
     */
    public function guideTourDetail($tour_id = null)
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        if (!$tour_id && isset($_GET['id'])) {
            $tour_id = $_GET['id'];
        }

        if (!$tour_id) {
            header('Location: ?act=guide_dashboard');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Kiểm tra xem HDV có được phân công tour này không
        $assignment_check = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM guide_assignments ga
            JOIN departure_schedules ds ON ga.departure_id = ds.departure_id
            WHERE ds.tour_id = :tour_id 
            AND ga.guide_id = :guide_id
        ");
        $assignment_check->execute([':tour_id' => $tour_id, ':guide_id' => $guide_id]);
        $assignment_result = $assignment_check->fetch();

        if ($assignment_result['count'] == 0) {
            $_SESSION['error_message'] = 'Bạn không được phân công tour này';
            header('Location: ?act=guide_dashboard');
            exit();
        }

        // Lấy thông tin tour
        $tour_query = $conn->prepare("
            SELECT t.*, 
                   d.departure_date,
                   d.departure_time,
                   d.meeting_point,
                   d.status as departure_status,
                   ga.assignment_type
            FROM tours t
            JOIN departure_schedules d ON t.tour_id = d.tour_id
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE t.tour_id = :tour_id 
            AND ga.guide_id = :guide_id
            LIMIT 1
        ");
        $tour_query->execute([':tour_id' => $tour_id, ':guide_id' => $guide_id]);
        $tour = $tour_query->fetch();

        // Lấy lịch trình tour
        $itinerary_query = $conn->prepare("
            SELECT * FROM tour_itineraries 
            WHERE tour_id = :tour_id 
            ORDER BY day_number ASC
        ");
        $itinerary_query->execute([':tour_id' => $tour_id]);
        $itinerary = $itinerary_query->fetchAll();

        // Lấy danh sách dịch vụ
        $services_query = $conn->query("
            SELECT * FROM tour_itineraries LIMIT 0
        ");
        $services = $services_query->fetchAll();

        $data = [
            'page_title' => 'Chi tiết Tour',
            'tour' => $tour,
            'itinerary' => $itinerary,
            'services' => $services,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Chi tiết Tour', 'active' => true]
            ]
        ];
        require_once './views/admin/guides/header.php';
        require_once './views/admin/guides/tour_detail.php';
        require_once './views/admin/guides/footer.php';
    }

    /**
     * Danh sách khách hàng trong tour
     */
    public function guideTourParticipants($tour_id = null)
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        if (!$tour_id && isset($_GET['tour_id'])) {
            $tour_id = $_GET['tour_id'];
        }

        if (!$tour_id) {
            header('Location: ?act=guide_dashboard');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Lấy thông tin tour
        $tour_query = $conn->prepare("
            SELECT t.tour_code, t.tour_name, d.departure_date
            FROM tours t
            JOIN departure_schedules d ON t.tour_id = d.tour_id
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE t.tour_id = :tour_id 
            AND ga.guide_id = :guide_id
            LIMIT 1
        ");
        $tour_query->execute([':tour_id' => $tour_id, ':guide_id' => $guide_id]);
        $tour_info = $tour_query->fetch();

        if (!$tour_info) {
            $_SESSION['error_message'] = 'Tour không tồn tại hoặc bạn không được phân công';
            header('Location: ?act=guide_dashboard');
            exit();
        }

        // Lấy danh sách khách hàng (giả lập)
        $participants = [
            [
                'id' => 1,
                'full_name' => 'Nguyễn Văn An',
                'phone' => '0912345678',
                'email' => 'an.nguyen@example.com',
                'group' => 'Gia đình A',
                'special_requests' => 'Ăn chay',
                'medical_notes' => 'Dị ứng hải sản',
                'attendance_status' => 'present'
            ],
            [
                'id' => 2,
                'full_name' => 'Trần Thị Bình',
                'phone' => '0923456789',
                'email' => 'binh.tran@example.com',
                'group' => 'Gia đình A',
                'special_requests' => 'Không có',
                'medical_notes' => 'Huyết áp cao',
                'attendance_status' => 'present'
            ]
        ];

        $data = [
            'page_title' => 'Danh sách khách hàng',
            'tour_info' => $tour_info,
            'participants' => $participants,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Chi tiết Tour', 'link' => "?act=guide_tour_detail&id={$tour_id}"],
                ['title' => 'Danh sách khách', 'active' => true]
            ]
        ];

        require_once './views/admin/guide/header.php';
        require_once './views/admin/guide/tour_participants.php';
        require_once './views/admin/guide/footer.php';
    }

    /**
     * Điểm danh khách hàng
     */
    public function guideAttendance($tour_id = null)
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        if (!$tour_id && isset($_GET['tour_id'])) {
            $tour_id = $_GET['tour_id'];
        }

        if (!$tour_id) {
            header('Location: ?act=guide_dashboard');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Lấy thông tin tour
        $tour_query = $conn->prepare("
            SELECT t.tour_code, t.tour_name, d.departure_date
            FROM tours t
            JOIN departure_schedules d ON t.tour_id = d.tour_id
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE t.tour_id = :tour_id 
            AND ga.guide_id = :guide_id
            LIMIT 1
        ");
        $tour_query->execute([':tour_id' => $tour_id, ':guide_id' => $guide_id]);
        $tour_info = $tour_query->fetch();

        if (!$tour_info) {
            $_SESSION['error_message'] = 'Tour không tồn tại hoặc bạn không được phân công';
            header('Location: ?act=guide_dashboard');
            exit();
        }

        $data = [
            'page_title' => 'Điểm danh khách hàng',
            'tour_info' => $tour_info,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Chi tiết Tour', 'link' => "?act=guide_tour_detail&id={$tour_id}"],
                ['title' => 'Điểm danh', 'active' => true]
            ]
        ];

        require_once './views/admin/guide/header.php';
        require_once './views/admin/guide/attendance.php';
        require_once './views/admin/guide/footer.php';
    }

    /**
     * Nhật ký tour
     */
    public function guideJournal($tour_id = null)
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        if (!$tour_id && isset($_GET['tour_id'])) {
            $tour_id = $_GET['tour_id'];
        }

        if (!$tour_id) {
            header('Location: ?act=guide_dashboard');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Xử lý POST khi lưu nhật ký
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $journal_data = $_POST;
            $journal_data['guide_id'] = $guide_id;
            $journal_data['tour_id'] = $tour_id;

            // Giả lập lưu nhật ký
            $_SESSION['success_message'] = 'Đã lưu nhật ký thành công';
            header("Location: ?act=guide_journal&tour_id={$tour_id}");
            exit();
        }

        // Lấy thông tin tour
        $tour_query = $conn->prepare("
            SELECT t.tour_code, t.tour_name, t.duration_days
            FROM tours t
            JOIN departure_schedules d ON t.tour_id = d.tour_id
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE t.tour_id = :tour_id 
            AND ga.guide_id = :guide_id
            LIMIT 1
        ");
        $tour_query->execute([':tour_id' => $tour_id, ':guide_id' => $guide_id]);
        $tour_info = $tour_query->fetch();

        if (!$tour_info) {
            $_SESSION['error_message'] = 'Tour không tồn tại hoặc bạn không được phân công';
            header('Location: ?act=guide_dashboard');
            exit();
        }

        // Lấy nhật ký hiện có
        $journals_query = $conn->prepare("
            SELECT * FROM guide_journals 
            WHERE tour_id = :tour_id AND guide_id = :guide_id
            ORDER BY journal_date DESC
        ");
        $journals_query->execute([':tour_id' => $tour_id, ':guide_id' => $guide_id]);
        $journals = $journals_query->fetchAll();

        $data = [
            'page_title' => 'Nhật ký Tour',
            'tour_info' => $tour_info,
            'journals' => $journals,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Chi tiết Tour', 'link' => "?act=guide_tour_detail&id={$tour_id}"],
                ['title' => 'Nhật ký Tour', 'active' => true]
            ]
        ];

        require_once './views/admin/guides/header.php';
        require_once './views/admin/guides/journal.php';
        require_once './views/admin/guides/footer.php';
    }

    /**
     * Yêu cầu đặc biệt của khách
     */
    public function guideSpecialRequests()
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Lấy các tour hiện tại của HDV
        $current_tours_query = $conn->prepare("
            SELECT DISTINCT t.tour_id, t.tour_code, t.tour_name
            FROM tours t
            JOIN departure_schedules d ON t.tour_id = d.tour_id
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE ga.guide_id = :guide_id 
            AND d.departure_date >= CURDATE()
        ");
        $current_tours_query->execute([':guide_id' => $guide_id]);
        $current_tours = $current_tours_query->fetchAll();

        $data = [
            'page_title' => 'Yêu cầu đặc biệt',
            'current_tours' => $current_tours,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Yêu cầu đặc biệt', 'active' => true]
            ]
        ];

        require_once './views/admin/guide/header.php';
        require_once './views/admin/guide/special_requests.php';
        require_once './views/admin/guide/footer.php';
    }

    /**
     * Báo cáo sự cố
     */
    public function guideIncidentReports()
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Lấy báo cáo sự cố của HDV
        $incidents_query = $conn->prepare("
            SELECT ir.*, t.tour_name, d.departure_date
            FROM incident_reports ir
            JOIN departure_schedules d ON ir.departure_id = d.departure_id
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE ir.guide_id = :guide_id
            ORDER BY ir.incident_date DESC
        ");
        $incidents_query->execute([':guide_id' => $guide_id]);
        $incidents = $incidents_query->fetchAll();

        $data = [
            'page_title' => 'Báo cáo sự cố',
            'incidents' => $incidents,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Báo cáo sự cố', 'active' => true]
            ]
        ];

        require_once './views/admin/guide/header.php';
        require_once './views/admin/guide/incident_report.php';
        require_once './views/admin/guide/footer.php';
    }

    /**
     * Tải xuống dữ liệu offline
     */
    public function guideOfflineMode()
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Xử lý yêu cầu tải dữ liệu offline
        if (isset($_GET['download'])) {
            $download_type = $_GET['download'];

            // Lấy dữ liệu cần tải offline
            $offline_data = [];

            // Lấy thông tin HDV
            $guide_info = $conn->prepare("SELECT * FROM guides WHERE guide_id = :guide_id");
            $guide_info->execute([':guide_id' => $guide_id]);
            $offline_data['guide_info'] = $guide_info->fetch();

            // Lấy tour hiện tại
            $current_tours = $conn->prepare("
                SELECT t.*, d.departure_date, d.departure_time, d.meeting_point 
                FROM tours t
                JOIN departure_schedules d ON t.tour_id = d.tour_id
                JOIN guide_assignments ga ON d.departure_id = ga.departure_id
                WHERE ga.guide_id = :guide_id 
                AND d.departure_date >= CURDATE() 
                AND d.status IN ('scheduled', 'confirmed')
            ");
            $current_tours->execute([':guide_id' => $guide_id]);
            $offline_data['current_tours'] = $current_tours->fetchAll();

            // Lấy lịch trình cho từng tour
            foreach ($offline_data['current_tours'] as &$tour) {
                $itinerary = $conn->prepare("
                    SELECT * FROM tour_itineraries 
                    WHERE tour_id = :tour_id 
                    ORDER BY day_number ASC
                ");
                $itinerary->execute([':tour_id' => $tour['tour_id']]);
                $tour['itinerary'] = $itinerary->fetchAll();
            }

            // Tạo file JSON để tải xuống
            $filename = "offline_data_" . date('Ymd_His') . ".json";
            $json_data = json_encode($offline_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            // Xuất file JSON
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $json_data;
            exit();
        }

        // Lấy thông tin về dữ liệu có sẵn
        $offline_summary = $conn->prepare("
            SELECT 
                COUNT(DISTINCT d.departure_id) as total_departures,
                COUNT(DISTINCT d.tour_id) as total_tours,
                MIN(d.departure_date) as earliest_date,
                MAX(d.departure_date) as latest_date
            FROM departure_schedules d
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE ga.guide_id = :guide_id 
            AND d.departure_date >= CURDATE()
            AND d.status IN ('scheduled', 'confirmed')
        ");
        $offline_summary->execute([':guide_id' => $guide_id]);
        $summary = $offline_summary->fetch();

        $data = [
            'page_title' => 'Chế độ Offline',
            'summary' => $summary,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Chế độ Offline', 'active' => true]
            ]
        ];

        require_once './views/admin/guide/header.php';
        require_once './views/admin/guide/offline_mode.php';
        require_once './views/admin/guide/footer.php';
    }

    /**
     * Profile HDV
     */
    public function guideProfile()
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $bio = $_POST['bio'] ?? '';

            try {
                $query = "UPDATE guides SET full_name = :full_name, email = :email, phone = :phone, bio = :bio WHERE guide_id = :guide_id";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    ':full_name' => $full_name,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':bio' => $bio,
                    ':guide_id' => $guide_id
                ]);

                // Cập nhật session
                $_SESSION['guide_name'] = $full_name;
                $_SESSION['guide_email'] = $email;
                $_SESSION['guide_phone'] = $phone;

                $_SESSION['success_message'] = 'Cập nhật thông tin thành công!';
            } catch (PDOException $e) {
                $error = "Lỗi khi cập nhật thông tin: " . $e->getMessage();
            }
        }

        // Lấy thông tin HDV
        $query = "SELECT * FROM guides WHERE guide_id = :guide_id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':guide_id' => $guide_id]);
        $guide_info = $stmt->fetch();

        $data = [
            'page_title' => 'Hồ sơ HDV',
            'guide_info' => $guide_info,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Hồ sơ HDV', 'active' => true]
            ]
        ];

        require_once './views/admin/guide/header.php';
        require_once './views/admin/guide/profile.php';
        require_once './views/admin/guide/footer.php';
    }

    /**
     * Đổi mật khẩu HDV
     */
    public function guideChangePassword()
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Kiểm tra mật khẩu hiện tại (trong demo dùng 'password123')
            if ($current_password !== 'password123') {
                $error = "Mật khẩu hiện tại không chính xác!";
            } elseif ($new_password !== $confirm_password) {
                $error = "Mật khẩu mới và xác nhận mật khẩu không khớp!";
            } elseif (strlen($new_password) < 6) {
                $error = "Mật khẩu mới phải có ít nhất 6 ký tự!";
            } else {
                // Trong thực tế, bạn sẽ mã hóa mật khẩu ở đây
                // $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                $_SESSION['success_message'] = 'Đổi mật khẩu thành công!';
                header('Location: ?act=guide_profile');
                exit();
            }
        }

        $data = [
            'page_title' => 'Đổi mật khẩu',
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Hồ sơ HDV', 'link' => '?act=guide_profile'],
                ['title' => 'Đổi mật khẩu', 'active' => true]
            ]
        ];

        require_once './views/admin/guide/header.php';
        require_once './views/admin/guide/change_password.php';
        require_once './views/admin/guide/footer.php';
    }

    /**
     * Tạo báo cáo sự cố
     */
    public function guideCreateIncidentReport()
    {
        // Kiểm tra đăng nhập HDV
        if (!isset($_SESSION['guide_id'])) {
            header('Location: ?act=guide_login');
            exit();
        }

        require_once './commons/env.php';
        require_once './commons/function.php';

        $conn = connectDB();
        $guide_id = $_SESSION['guide_id'];

        // Lấy danh sách tour hiện tại để chọn
        $current_tours = $conn->prepare("
            SELECT DISTINCT t.tour_id, t.tour_code, t.tour_name, d.departure_date
            FROM tours t
            JOIN departure_schedules d ON t.tour_id = d.tour_id
            JOIN guide_assignments ga ON d.departure_id = ga.departure_id
            WHERE ga.guide_id = :guide_id 
            AND d.departure_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            AND d.departure_date <= CURDATE()
            ORDER BY d.departure_date DESC
        ");
        $current_tours->execute([':guide_id' => $guide_id]);
        $tours = $current_tours->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tour_id = $_POST['tour_id'] ?? '';
            $incident_date = $_POST['incident_date'] ?? date('Y-m-d');
            $incident_type = $_POST['incident_type'] ?? '';
            $description = $_POST['description'] ?? '';
            $severity = $_POST['severity'] ?? 'low';
            $actions_taken = $_POST['actions_taken'] ?? '';
            $follow_up_required = isset($_POST['follow_up_required']) ? 1 : 0;

            // Kiểm tra dữ liệu đầu vào
            if (empty($tour_id) || empty($incident_type) || empty($description)) {
                $error = "Vui lòng điền đầy đủ thông tin báo cáo!";
            } else {
                try {
                    // Lấy departure_id
                    $departure_query = $conn->prepare("
                        SELECT departure_id FROM departure_schedules 
                        WHERE tour_id = :tour_id 
                        AND departure_date = :incident_date
                        LIMIT 1
                    ");
                    $departure_query->execute([
                        ':tour_id' => $tour_id,
                        ':incident_date' => $incident_date
                    ]);
                    $departure = $departure_query->fetch();

                    if ($departure) {
                        // Giả lập lưu báo cáo sự cố
                        // Trong thực tế, bạn sẽ thêm vào database
                        // $query = "INSERT INTO incident_reports (...) VALUES (...)";

                        $_SESSION['success_message'] = 'Đã gửi báo cáo sự cố thành công!';
                        header('Location: ?act=guide_incident_reports');
                        exit();
                    } else {
                        $error = "Không tìm thấy lịch khởi hành phù hợp!";
                    }
                } catch (PDOException $e) {
                    $error = "Lỗi khi lưu báo cáo: " . $e->getMessage();
                }
            }
        }

        $data = [
            'page_title' => 'Tạo báo cáo sự cố',
            'tours' => $tours,
            'breadcrumb' => [
                ['title' => 'Dashboard HDV', 'link' => '?act=guide_dashboard'],
                ['title' => 'Báo cáo sự cố', 'link' => '?act=guide_incident_reports'],
                ['title' => 'Tạo báo cáo mới', 'active' => true]
            ]
        ];

        require_once './views/admin/guide/header.php';
        require_once './views/admin/guide/create_incident_report.php';
        require_once './views/admin/guide/footer.php';
    }


    /**
     * ============================================
     * TIỆN ÍCH HỖ TRỢ
     * ============================================
     */

    private function checkAdminAuth()
    {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=admin_login");
            exit();
        }
    }
}
