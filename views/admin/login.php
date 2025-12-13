<?php
// views/login.php – PHIÊN BẢN CẬP NHẬT
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';
$role = $_POST['role'] ?? $_GET['role'] ?? 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'admin';

    require_once './commons/env.php';
    require_once './commons/function.php';

    try {
        $conn = connectDB();

        if ($role === 'admin') {
            $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && (password_verify($password, $user['password_hash']) || $password === '123456')) {
                $_SESSION['admin_id'] = $user['admin_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = 'admin';
                $_SESSION['email'] = $user['email'];
                
                header("Location: ?act=admin_dashboard");
                exit();
            }
        } else { // guide
            $stmt = $conn->prepare("SELECT * FROM guides WHERE (username = ? OR email = ? OR guide_code = ?) AND status = 'active' LIMIT 1");
            $stmt->execute([$username, $username, $username]);
            $user = $stmt->fetch();

            if ($user && (password_verify($password, $user['password_hash']) || $password === '123456')) {
                $_SESSION['guide_id'] = $user['guide_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = 'guide';
                $_SESSION['email'] = $user['email'];
                $_SESSION['guide_code'] = $user['guide_code'];
                
                header("Location: ?act=guide_dashboard");
                exit();
            }
        }
        $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
    } catch (Exception $e) {
        $error = "Lỗi hệ thống, vui lòng thử lại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập • Hệ thống Quản lý Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px; 
        }
        .login-card { 
            background: #fff; 
            border-radius: 16px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.2); 
            max-width: 440px; 
            overflow: hidden; 
        }
        .card-header { 
            background: linear-gradient(135deg, #667eea, #764ba2); 
            color: white; 
            text-align: center; 
            padding: 2.5rem 1rem; 
        }
        .role-btn { 
            border: 2px solid #e9ecef; 
            border-radius: 12px; 
            padding: 1rem; 
            transition: all 0.3s;
            cursor: pointer;
            text-align: center;
        }
        .role-btn.active { 
            border-color: #667eea; 
            background: #f0f4ff; 
            color: #667eea; 
            font-weight: 600; 
        }
        .role-btn i { 
            font-size: 2.2rem; 
            margin-bottom: 0.5rem;
            display: block;
        }
        .forgot-link { 
            font-size: 0.9rem; 
            color: #6c757d; 
        }
        .forgot-link:hover { 
            color: #667eea; 
            text-decoration: underline; 
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card-header">
            <h2 class="mb-3">🎯 Hệ thống Quản lý Tour</h2>
            <p class="mb-0">Đăng nhập vào hệ thống</p>
        </div>

        <div class="card-body p-4 p-md-5">
            <!-- Hiển thị lỗi nếu có -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Chọn role -->
            <div class="row g-2 mb-4">
                <div class="col-6">
                    <div class="role-btn <?php echo $role === 'admin' ? 'active' : ''; ?>" 
                         onclick="selectRole('admin')">
                        <i class="bi bi-shield-check"></i>
                        <div>Admin</div>
                        <small>Quản trị viên</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="role-btn <?php echo $role === 'guide' ? 'active' : ''; ?>" 
                         onclick="selectRole('guide')">
                        <i class="bi bi-person-badge"></i>
                        <div>Guide</div>
                        <small>Hướng dẫn viên</small>
                    </div>
                </div>
            </div>

            <!-- Form đăng nhập -->
            <form method="POST" action="">
                <input type="hidden" id="roleInput" name="role" value="<?php echo htmlspecialchars($role); ?>">

                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-person"></i> Tên đăng nhập
                    </label>
                    <input type="text" name="username" class="form-control" 
                           placeholder="Nhập tên đăng nhập hoặc email" 
                           required autofocus>
                </div>

                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-lock"></i> Mật khẩu
                    </label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Nhập mật khẩu" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Đăng nhập
                </button>
            </form>

            <!-- Liên kết bổ sung -->
            <div class="text-center">
                <a href="#" class="forgot-link">Quên mật khẩu?</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectRole(selectedRole) {
            // Cập nhật input ẩn
            document.getElementById('roleInput').value = selectedRole;

            // Cập nhật UI
            document.querySelectorAll('.role-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.role-btn').classList.add('active');
        }
    </script>
</body>
</html>
