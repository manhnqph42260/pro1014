<?php
// views/auth/login.php - ĐÃ SỬA LỖI SESSION

// KHÔNG gọi session_start() ở đây nữa
// Vì session đã được start trong index.php

// Lấy error từ session (nếu có)
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
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
            width: 100%;
            overflow: hidden; 
        }
        .card-header { 
            background: linear-gradient(135deg, #667eea, #764ba2); 
            color: white; 
            text-align: center; 
            padding: 2.5rem 1rem; 
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

            <!-- Form đăng nhập ĐƠN GIẢN - KHÔNG CHỌN ROLE -->
            <form method="POST" action="?act=process_login">
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-person"></i> Tên đăng nhập / Email / Mã HDV
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
            <div class="demo-credentials mt-4 p-3 bg-light rounded">
                <h6><i class="bi bi-info-circle me-2"></i>Thông tin đăng nhập demo:</h6>
                <div class="mb-2">
                    <strong class="text-primary">Admin:</strong> superadmin / 123456
                </div>
                <div class="mb-2">
                    <strong class="text-success">Hướng dẫn viên:</strong> HDV001 / password123
                </div>
                <div>
                    <strong class="text-success">Hướng dẫn viên:</strong> HDV002 / password123
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>