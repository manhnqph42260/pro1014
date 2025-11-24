<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Quản trị Tour</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .login-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        .login-box {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .login-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-box">
                    <div class="login-header">
                        <i class="bi bi-mountains display-4 mb-3"></i>
                        <h2 class="h4 mb-0">Đăng nhập Quản trị</h2>
                        <p class="mb-0 opacity-75">Hệ thống quản lý Tour du lịch</p>
                    </div>
                    
                    <div class="p-4">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Tên đăng nhập</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" name="username" class="form-control" required 
                                           placeholder="Nhập username">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control" required 
                                           placeholder="Nhập mật khẩu">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Quên mật khẩu? <a href="#" class="text-decoration-none">Liên hệ quản trị viên</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>