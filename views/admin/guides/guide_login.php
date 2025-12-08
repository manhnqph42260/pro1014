<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Hướng dẫn viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            padding: 30px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            font-weight: bold;
        }
        .btn-login:hover {
            opacity: 0.9;
            color: white;
        }
        .guide-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php 
    // Kiểm tra session error
    if (isset($_SESSION['login_error'])): 
    ?>
    <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo htmlspecialchars($_SESSION['login_error']);
            unset($_SESSION['login_error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="login-card">
        <div class="login-header">
            <div class="guide-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3>Đăng nhập HDV</h3>
            <p class="mb-0">Hệ thống Hướng dẫn viên</p>
        </div>
        
        <div class="login-body">
            <form method="POST" action="?act=guide_process_login">
                <div class="mb-3">
                    <label for="username" class="form-label">Tên đăng nhập / Email</label>
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Nhập mã HDV hoặc email" required
                           value="HDV001">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Nhập mật khẩu" required
                           value="password123">
                    <small class="text-muted">Tài khoản demo: <code>HDV001</code> / <code>password123</code></small>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                </div>
                
                <button type="submit" class="btn btn-login mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập
                </button>
                
                <div class="text-center">
                    <a href="?act=admin_login" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i> Đăng nhập Admin
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>