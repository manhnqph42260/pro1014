<?php
// =========================================================================
// PHẦN 1: XỬ LÝ LOGIC PHP (GIỮ NGUYÊN CHỨC NĂNG CŨ CỦA BẠN)
// =========================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu đã đăng nhập rồi thì đá về trang tương ứng luôn
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php?act=admin_dashboard");
    exit();
}
if (isset($_SESSION['guide_id'])) {
    header("Location: index.php?act=guide-dashboard");
    exit();
}

$error = '';
$username_val = '';
$role_val = 'admin'; // Mặc định chọn Admin

// Xử lý khi bấm nút Đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once './commons/env.php';
    require_once './commons/function.php';

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = $_POST['role'] ?? 'admin';
    $username_val = $username;
    $role_val = $role;

    try {
        $conn = connectDB();

        // --- TRƯỜNG HỢP 1: ĐĂNG NHẬP ADMIN ---
        if ($role === 'admin') {
            $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            // Check pass (Có hỗ trợ pass '123456' cho demo như code cũ của bạn)
            if ($user && (password_verify($password, $user['password_hash']) || $password === '123456')) {
                // Lưu session Admin
                $_SESSION['admin_id']  = $user['admin_id'];
                $_SESSION['role']      = 'admin';
                $_SESSION['full_name'] = $user['full_name'] ?? 'Quản trị viên';
                
                header("Location: index.php?act=admin_dashboard");
                exit();
            } else {
                $error = 'Tài khoản hoặc mật khẩu Admin không đúng!';
            }
        } 
        // --- TRƯỜNG HỢP 2: ĐĂNG NHẬP HƯỚNG DẪN VIÊN ---
        else if ($role === 'guide') {
            // Cho phép đăng nhập bằng username hoặc mã HDV (guide_code)
            $stmt = $conn->prepare("SELECT * FROM guides WHERE (username = ? OR guide_code = ?) AND status = 'active' LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && (password_verify($password, $user['password_hash']) || $password === '123456')) {
                // Lưu session Guide (Quan trọng: key là 'guide_id')
                $_SESSION['guide_id']   = $user['guide_id'];
                $_SESSION['role']       = 'guide';
                $_SESSION['user_guide'] = $user; // Lưu full thông tin để dùng sau này
                $_SESSION['full_name']  = $user['full_name'];

                header("Location: index.php?act=guide-dashboard");
                exit();
            } else {
                $error = 'Tài khoản HDV không đúng hoặc chưa được kích hoạt!';
            }
        }

    } catch (Exception $e) {
        $error = "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Hệ Thống | Tour Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #224abe;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 550px;
        }
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 40px;
            text-align: center;
        }
        .login-right {
            flex: 1.2;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }
        .brand-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }
        .role-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        .role-option {
            flex: 1;
        }
        .role-input {
            display: none;
        }
        .role-label {
            display: block;
            text-align: center;
            padding: 15px;
            border: 2px solid #e3e6f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #858796;
        }
        .role-label i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 5px;
        }
        /* Hiệu ứng khi chọn Role */
        .role-input:checked + .role-label {
            border-color: var(--primary-color);
            background-color: #f0f4ff;
            color: var(--primary-color);
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.2);
        }
        .form-control-lg {
            font-size: 1rem;
            padding: 15px 20px;
            border-radius: 10px;
            background-color: #f8f9fc;
            border: 1px solid #eaecf4;
        }
        .form-control-lg:focus {
            background-color: #fff;
            box-shadow: none;
            border-color: var(--primary-color);
        }
        .btn-login {
            background-color: var(--primary-color);
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        /* Responsive Mobile */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                max-width: 400px;
                min-height: auto;
            }
            .login-left {
                display: none; /* Ẩn phần bên trái trên mobile cho gọn */
            }
            .login-right {
                padding: 30px;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-left">
            <i class="bi bi-airplane-engines brand-icon"></i>
            <h2 class="fw-bold mb-3">Đăng Nhập</h2>
            <p class="fs-5 opacity-75">Hệ thống quản lý điều hành tour du lịch chuyên nghiệp & toàn diện.</p>
            <div class="mt-4">
                <small class="d-block mb-1"><i class="bi bi-check-circle-fill me-2"></i>Quản lý Tour & Lịch trình</small>
                <small class="d-block mb-1"><i class="bi bi-check-circle-fill me-2"></i>Điều phối Hướng dẫn viên</small>
                <small class="d-block"><i class="bi bi-check-circle-fill me-2"></i>Báo cáo vận hành Real-time</small>
            </div>
        </div>

        <div class="login-right">
            <h3 class="fw-bold text-dark mb-1">Xin chào,</h3>
            <p class="text-muted mb-4">Vui lòng chọn vai trò để tiếp tục.</p>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" class="role-input" id="role_admin" name="role" value="admin" <?= $role_val == 'admin' ? 'checked' : '' ?>>
                        <label class="role-label" for="role_admin">
                            <i class="bi bi-shield-lock"></i>
                            Quản Trị Viên
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" class="role-input" id="role_guide" name="role" value="guide" <?= $role_val == 'guide' ? 'checked' : '' ?>>
                        <label class="role-label" for="role_guide">
                            <i class="bi bi-person-badge"></i>
                            Hướng Dẫn Viên
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-floating">
                        <input type="text" class="form-control form-control-lg" id="username" name="username" 
                               placeholder="Tên đăng nhập" value="<?= htmlspecialchars($username_val) ?>" required>
                        <label for="username">Email, Username hoặc Mã HDV</label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-floating">
                        <input type="password" class="form-control form-control-lg" id="password" name="password" 
                               placeholder="Mật khẩu" required>
                        <label for="password">Mật khẩu</label>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label text-muted" for="remember">
                            Ghi nhớ tôi
                        </label>
                    </div>
                    <a href="#" class="text-decoration-none small">Quên mật khẩu?</a>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login btn-lg text-white">
                        ĐĂNG NHẬP HỆ THỐNG
                    </button>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">Designed by YourName &copy; 2024</small>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>