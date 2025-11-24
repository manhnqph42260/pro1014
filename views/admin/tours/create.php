<?php
$page_title = "Tạo Tour Mới";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
        }
        .sidebar .logo {
            padding: 1rem;
            border-bottom: 1px solid #34495e;
        }
        .sidebar-nav {
            padding: 1rem 0;
        }
        .nav-item {
            display: block;
            padding: 0.75rem 1rem;
            color: #bdc3c7;
            text-decoration: none;
            border-left: 3px solid transparent;
        }
        .nav-item:hover, .nav-item.active {
            background: #34495e;
            color: white;
            border-left-color: #3498db;
        }
        .main-content {
            flex: 1;
            background: #ecf0f1;
        }
        .top-header {
            background: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .content-area {
            padding: 2rem;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .alert {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>🏔️ Tour Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="?act=admin_dashboard" class="nav-item">📊 Dashboard</a>
                <a href="?act=admin_tours" class="nav-item">🗺️ Quản lý Tour</a>
                <a href="?act=admin_tours_create" class="nav-item active">➕ Tạo Tour</a>
                <a href="?act=admin_departures" class="nav-item">📅 Lịch khởi hành</a>
                <a href="?act=admin_logout" class="nav-item">🚪 Đăng xuất</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1>Tạo Tour Mới</h1>
                </div>
                <div class="header-right">
                    <span>Xin chào, <?php echo $_SESSION['full_name'] ?? 'Admin'; ?></span>
                </div>
            </header>

            <div class="content-area">
                <div class="form-container">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tour_name">Tên Tour *</label>
                                <input type="text" id="tour_name" name="tour_name" required 
                                       placeholder="Nhập tên tour">
                            </div>
                            <div class="form-group">
                                <label for="tour_code">Mã Tour *</label>
                                <input type="text" id="tour_code" name="tour_code" required 
                                       placeholder="VD: TOUR001" value="TOUR<?php echo time(); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Mô tả Tour</label>
                            <textarea id="description" name="description" 
                                      placeholder="Mô tả chi tiết về tour"></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="destination">Điểm đến *</label>
                                <input type="text" id="destination" name="destination" required 
                                       placeholder="VD: Sapa, Lào Cai">
                            </div>
                            <div class="form-group">
                                <label for="duration_days">Số ngày *</label>
                                <input type="number" id="duration_days" name="duration_days" required 
                                       min="1" max="30" placeholder="VD: 3">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="price_adult">Giá người lớn (VNĐ) *</label>
                                <input type="number" id="price_adult" name="price_adult" required 
                                       min="0" placeholder="VD: 2500000">
                            </div>
                            <div class="form-group">
                                <label for="price_child">Giá trẻ em (VNĐ)</label>
                                <input type="number" id="price_child" name="price_child" 
                                       min="0" placeholder="VD: 1800000">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="max_participants">Số chỗ tối đa *</label>
                                <input type="number" id="max_participants" name="max_participants" required 
                                       min="1" max="100" placeholder="VD: 20">
                            </div>
                            <div class="form-group">
                                <label for="status">Trạng thái *</label>
                                <select id="status" name="status" required>
                                    <option value="draft">Bản nháp</option>
                                    <option value="published">Đã xuất bản</option>
                                    <option value="locked">Đã khóa</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="featured_image">Hình ảnh đại diện</label>
                            <input type="file" id="featured_image" name="featured_image" 
                                   accept="image/*" onchange="previewImage(this)">
                            <img id="imagePreview" class="image-preview" src="" alt="Preview">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">📝 Tạo Tour</button>
                            <a href="?act=admin_tours" class="btn btn-secondary">↩️ Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>