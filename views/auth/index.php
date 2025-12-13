<?php
// views/auth/login.php
// LƯU Ý: ĐÃ LOẠI BỎ TOÀN BỘ LOGIC XỬ LÝ, CHỈ CÒN HTML

// Lấy error từ session (nếu có)
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

// Role mặc định
$role = $_POST['role'] ?? 'admin';
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
            width: 100%;
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
        .demo-credentials {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
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
            <?php if (!empty($error)): ?>
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
                        <div>HDV</div>
                        <small>Hướng dẫn viên</small>
                    </div>
                </div>
            </div>

            <!-- Form đăng nhập -->
            <form method="POST" action="?act=process_login">
                <input type="hidden" id="roleInput" name="role" value="<?php echo htmlspecialchars($role); ?>">

                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-person"></i> Tên đăng nhập
                    </label>
                    <input type="text" name="username" class="form-control" 
                           placeholder="Nhập tên đăng nhập, email hoặc mã HDV" 
                           required autofocus>
                </div>

                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-lock"></i> Mật khẩu
                    </label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Nhập mật khẩu" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3" 
                        style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Đăng nhập
                </button>
            </form>

            <!-- Thông tin đăng nhập demo -->
            <div class="demo-credentials">
                <h6><i class="bi bi-info-circle me-2"></i>Thông tin đăng nhập demo:</h6>
                <div class="mb-1">
                    <strong>Admin:</strong> superadmin / 123456
                </div>
                <div class="mb-1">
                    <strong>HDV:</strong> HDV001 / password123
                </div>
                <div>
                    <strong>HDV:</strong> HDV002 / password123
                </div>
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
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>