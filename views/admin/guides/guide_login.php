<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Quản lý Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
            max-width: 450px;
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
        .role-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
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
            padding: 12px;
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #858796;
        }
        .role-input:checked + .role-label {
            border-color: #667eea;
            background-color: #f0f4ff;
            color: #667eea;
            font-weight: bold;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-login:hover {
            opacity: 0.9;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php 
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
            <div style="font-size: 48px; margin-bottom: 15px;">
                <i class="bi bi-mountain"></i>
            </div>
            <h3>Tour Management</h3>
            <p class="mb-0">Hệ thống Quản lý Tour Du lịch</p>
        </div>
        
        <div class="login-body">
            <form method="POST" action="?act=check_login">
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" class="role-input" id="role_admin" name="role" value="admin">
                        <label class="role-label" for="role_admin">
                            <i class="bi bi-shield-lock"></i><br>
                            Quản Trị Viên
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" class="role-input" id="role_guide" name="role" value="guide" checked>
                        <label class="role-label" for="role_guide">
                            <i class="bi bi-person-badge"></i><br>
                            Hướng Dẫn Viên
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="username" class="form-label">Tên đăng nhập / Email / Mã HDV</label>
                    <input type="text" class="form-control form-control-lg" id="username" name="username" 
                           placeholder="Nhập tên đăng nhập" required
                           value="HDV001">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" 
                           placeholder="Nhập mật khẩu" required
                           value="123456">
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                </div>
                
                <button type="submit" class="btn btn-login mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Đăng Nhập
                </button>
                
                <div class="text-center text-muted small">
                    <p>Demo: Admin / superadmin (123456) hoặc HDV / HDV001 (123456)</p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>