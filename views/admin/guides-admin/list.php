<?php
// File: views/admin/guides-admin/list.php

// Sửa đường dẫn header đúng cách
require_once dirname(__DIR__) . '/header.php';

// Khởi tạo biến để tránh lỗi
$guides = [];
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

// Lấy dữ liệu từ database - SỬA ĐƯỜNG DẪN Ở ĐÂY
try {
    // Đường dẫn tương đối từ guides-admin đến commons
    require_once dirname(dirname(dirname(__DIR__))) . '/commons/env.php';
    require_once dirname(dirname(dirname(__DIR__))) . '/commons/function.php';
    
    $conn = connectDB();
    
    $query = "SELECT 
                g.*, 
                gc.category_name,
                gc.color_code
              FROM guides g 
              LEFT JOIN guide_categories gc ON g.category_id = gc.category_id 
              WHERE 1=1";
    $params = [];
    
    if ($search) {
        $query .= " AND (g.full_name LIKE :search OR g.guide_code LIKE :search OR g.email LIKE :search OR g.phone LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if ($status) {
        $query .= " AND g.status = :status";
        $params[':status'] = $status;
    }
    
    $query .= " ORDER BY g.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $guides = $stmt->fetchAll();
} catch (Exception $e) {
    // Log lỗi nếu cần
    error_log("Lỗi khi lấy danh sách HDV: " . $e->getMessage());
}

$page_title = "Quản lý Hướng dẫn viên";
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý Hướng dẫn viên</h1>
    
    <!-- Flash messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách HDV</h6>
            <div>
                <a href="?act=admin_guides_create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Thêm HDV mới
                </a>
                <a href="?act=admin_guide_categories" class="btn btn-info">
                    <i class="fas fa-tags me-1"></i> Quản lý nhóm
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="mb-4">
                <input type="hidden" name="act" value="admin_guides">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm HDV (tên, mã, email, SĐT)..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Đang hoạt động</option>
                            <option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''; ?>>Không hoạt động</option>
                            <option value="on_leave" <?php echo ($status == 'on_leave') ? 'selected' : ''; ?>>Nghỉ phép</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Lọc
                            </button>
                            <a href="?act=admin_guides" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Tổng HDV
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($guides); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Đang hoạt động
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count(array_filter($guides, fn($g) => $g['status'] == 'active')); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Nghỉ phép
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count(array_filter($guides, fn($g) => $g['status'] == 'on_leave')); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-umbrella-beach fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Chưa phân nhóm
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo count(array_filter($guides, fn($g) => empty($g['category_id']))); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tag fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Guides Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">ID</th>
                            <th width="100">Mã HDV</th>
                            <th>Họ tên</th>
                            <th width="120">Điện thoại</th>
                            <th>Email</th>
                            <th width="100">Kinh nghiệm</th>
                            <th width="120">Đánh giá</th>
                            <th width="120">Trạng thái</th>
                            <th width="100">Nhóm</th>
                            <th width="200">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($guides) && is_array($guides)): ?>
                            <?php foreach ($guides as $guide): ?>
                            <tr>
                                <td><?php echo $guide['guide_id']; ?></td>
                                <td>
                                    <strong class="text-primary"><?php echo htmlspecialchars($guide['guide_code']); ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($guide['avatar_url'])): ?>
                                            <img src="<?php echo BASE_URL . '/' . $guide['avatar_url']; ?>" alt="Avatar" 
                                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                                        <?php else: ?>
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #6c757d; color: white; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($guide['full_name']); ?></strong>
                                            <?php if (!empty($guide['id_number'])): ?>
                                                <br>
                                                <small class="text-muted">CCCD: <?php echo htmlspecialchars($guide['id_number']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="tel:<?php echo htmlspecialchars($guide['phone']); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($guide['phone']); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($guide['email']); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($guide['email']); ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        <?php echo $guide['experience_years']; ?> năm
                                    </span>
                                </td>
                                <td>
                                    <div class="rating d-flex align-items-center">
                                        <?php 
                                        $rating = (float)$guide['rating'];
                                        for ($i = 1; $i <= 5; $i++): 
                                        ?>
                                            <i class="fas fa-star <?php echo $i <= $rating ? 'text-warning' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ms-2 fw-bold"><?php echo number_format($rating, 1); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $status_badges = [
                                        'active' => ['class' => 'bg-success', 'text' => 'Đang hoạt động', 'icon' => 'check-circle'],
                                        'inactive' => ['class' => 'bg-secondary', 'text' => 'Không hoạt động', 'icon' => 'times-circle'],
                                        'on_leave' => ['class' => 'bg-warning', 'text' => 'Nghỉ phép', 'icon' => 'umbrella-beach']
                                    ];
                                    $status_info = $status_badges[$guide['status']] ?? $status_badges['inactive'];
                                    ?>
                                    <span class="badge <?php echo $status_info['class']; ?>">
                                        <i class="fas fa-<?php echo $status_info['icon']; ?> me-1"></i>
                                        <?php echo $status_info['text']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($guide['category_name'])): ?>
                                        <span class="badge" style="background-color: <?php echo $guide['color_code'] ?? '#6c757d'; ?>; color: white;">
                                            <i class="fas fa-tag me-1"></i>
                                            <?php echo htmlspecialchars($guide['category_name']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-question-circle me-1"></i>
                                            Chưa phân nhóm
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="?act=admin_guides_view&id=<?php echo $guide['guide_id']; ?>" 
                                           class="btn btn-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?act=admin_guides_edit&id=<?php echo $guide['guide_id']; ?>" 
                                           class="btn btn-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?act=admin_guides_delete&id=<?php echo $guide['guide_id']; ?>" 
                                           class="btn btn-danger" title="Xóa"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa HDV này?\nHọ tên: <?php echo addslashes($guide['full_name']); ?>\nMã: <?php echo $guide['guide_code']; ?>')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                    <div class="mt-1">
                                        <small>
                                            <a href="?act=admin_guides_assignments&guide_id=<?php echo $guide['guide_id']; ?>" 
                                               class="text-decoration-none">
                                                <i class="fas fa-calendar-alt"></i> Lịch phân công
                                            </a>
                                        </small>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5>Không có HDV nào</h5>
                                        <p>Bắt đầu bằng cách thêm HDV mới</p>
                                        <a href="?act=admin_guides_create" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i> Thêm HDV đầu tiên
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Export buttons -->
            <?php if (!empty($guides)): ?>
            <div class="mt-4 border-top pt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted">
                            Tổng cộng: <strong><?php echo count($guides); ?></strong> HDV
                        </span>
                    </div>
                    <div>
                        <button class="btn btn-outline-success" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-1"></i> Xuất Excel
                        </button>
                        <button class="btn btn-outline-danger ms-2" onclick="exportToPDF()">
                            <i class="fas fa-file-pdf me-1"></i> Xuất PDF
                        </button>
                        <button class="btn btn-outline-primary ms-2" onclick="printList()">
                            <i class="fas fa-print me-1"></i> In danh sách
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function exportToExcel() {
    alert('Chức năng xuất Excel sẽ được thực hiện');
    // window.location.href = 'export_excel.php?type=guides';
}

function exportToPDF() {
    alert('Chức năng xuất PDF sẽ được thực hiện');
    // window.location.href = 'export_pdf.php?type=guides';
}

function printList() {
    const originalContent = document.body.innerHTML;
    const printContent = document.querySelector('.card-body').innerHTML;
    
    document.body.innerHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Danh sách Hướng dẫn viên</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .print-header { text-align: center; margin-bottom: 30px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #f2f2f2; }
                .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h2>DANH SÁCH HƯỚNG DẪN VIÊN</h2>
                <p>Ngày xuất: ${new Date().toLocaleDateString('vi-VN')}</p>
                <p>Tổng số: <?php echo count($guides); ?> HDV</p>
            </div>
            ${printContent}
        </body>
        </html>
    `;
    
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}

// Initialize DataTable if needed
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#dataTable').DataTable({
            pageLength: 25,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            }
        });
    }
});
</script>

<?php 
// Sửa đường dẫn footer đúng cách
require_once dirname(__DIR__) . '/footer.php'; 
?>