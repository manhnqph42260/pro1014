<?php
$page_title = "Quản lý Tour";
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
        .container {
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }
        .table th {
            background: #f8f9fa;
        }
        .status-published { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .status-draft { background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .status-locked { background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .search-form {
            margin-bottom: 20px;
        }
        .search-form input, .search-form select {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .tour-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .no-image {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border: 1px dashed #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 10px;
        }
        .tour-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-edit { background: #ffc107; color: black; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-view { background: #17a2b8; color: white; }
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
                <a href="?act=admin_tours" class="nav-item active">🗺️ Quản lý Tour</a>
                <a href="?act=admin_departures" class="nav-item">📅 Lịch khởi hành</a>
                <a href="?act=admin_guides" class="nav-item">👨‍💼 HDV</a>
                <a href="?act=admin_services" class="nav-item">🔔 Dịch vụ</a>
                <a href="?act=admin_logout" class="nav-item">🚪 Đăng xuất</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1>Quản lý Tour</h1>
                </div>
                <div class="header-right">
                    <span>Xin chào, <?php echo $_SESSION['full_name'] ?? 'Admin'; ?></span>
                </div>
            </header>

            <div class="content-area">
                <div class="container">
                    <div class="header">
                        <h1>Quản lý Tour (<?php echo $total_tours; ?> tour)</h1>
                        <a href="?act=admin_tours_create" class="btn">+ Tạo Tour mới</a>
                    </div>

                    <!-- Search Form -->
                    <div class="search-form">
                        <form method="GET">
                            <input type="hidden" name="act" value="admin_tours">
                            <input type="text" name="search" placeholder="Tìm kiếm tour..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                            <select name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="published" <?php echo ($_GET['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                                <option value="draft" <?php echo ($_GET['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Bản nháp</option>
                                <option value="locked" <?php echo ($_GET['status'] ?? '') === 'locked' ? 'selected' : ''; ?>>Đã khóa</option>
                            </select>
                            <button type="submit" style="padding: 8px 15px; background: #28a745; color: white; border: none; border-radius: 4px;">Tìm kiếm</button>
                            <a href="?act=admin_tours" style="padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">Xóa tìm kiếm</a>
                        </form>
                    </div>

                    <!-- Tours Table -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Mã Tour</th>
                                <th>Tên Tour</th>
                                <th>Điểm đến</th>
                                <th>Thời gian</th>
                                <th>Giá người lớn</th>
                                <th>Số chỗ</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($tours) > 0): ?>
                                <?php foreach ($tours as $tour): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($tour['featured_image']) && file_exists("uploads/" . $tour['featured_image'])): ?>
                                            <img src="uploads/<?php echo $tour['featured_image']; ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>" class="tour-image">
                                        <?php else: ?>
                                            <div class="no-image">No Image</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo $tour['tour_code']; ?></strong>
                                    </td>
                                    <td>
                                        <div class="tour-info">
                                            <div>
                                                <strong><?php echo htmlspecialchars($tour['tour_name']); ?></strong>
                                                <?php if (!empty($tour['description'])): ?>
                                                    <br><small style="color: #666;"><?php echo mb_substr($tour['description'], 0, 50) . '...'; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($tour['destination']); ?></td>
                                    <td><?php echo $tour['duration_days']; ?> ngày</td>
                                    <td><?php echo number_format($tour['price_adult']); ?> VNĐ</td>
                                    <td><?php echo $tour['max_participants']; ?> chỗ</td>
                                    <td>
                                        <span class="status-<?php echo $tour['status']; ?>">
                                            <?php 
                                            $status_text = [
                                                'draft' => 'Bản nháp',
                                                'published' => 'Đã xuất bản',
                                                'locked' => 'Đã khóa'
                                            ];
                                            echo $status_text[$tour['status']] ?? $tour['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?act=admin_tours_edit&id=<?php echo $tour['tour_id']; ?>" class="btn-sm btn-edit">✏️ Sửa</a>
                                            <a href="index.php?act=tour_detail&id=<?= $tour['tour_id'] ?>"  class="btn-sm btn-edit">Chi tiết</a>
                                            <a href="?act=admin_tours_delete&id=<?php echo $tour['tour_id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Bạn có chắc muốn xóa tour này?')">🗑️ Xóa</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 40px;">
                                        <p style="font-size: 18px; color: #6c757d; margin-bottom: 20px;">📭 Không có tour nào.</p>
                                        <a href="?act=admin_tours_create" class="btn" style="font-size: 16px;">➕ Tạo tour đầu tiên</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>