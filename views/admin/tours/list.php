<?php
$page_title = "Quản lý Tour";
require_once '../pro1014/views/admin/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
    <div>
        <h1 class="h2">Quản lý Tour</h1>
        <p class="mb-0">Tổng số: <span class="badge bg-primary"><?php echo $total_tours; ?></span> tour</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="?act=admin_tours_create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Tạo Tour mới
        </a>
    </div>
</div>

<!-- Search & Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="act" value="admin_tours">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tên tour, mã tour..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="published" <?php echo ($_GET['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                    <option value="draft" <?php echo ($_GET['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Bản nháp</option>
                    <option value="locked" <?php echo ($_GET['status'] ?? '') === 'locked' ? 'selected' : ''; ?>>Đã khóa</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Tìm kiếm
                </button>
            </div>
            <div class="col-md-2">
                <a href="?act=admin_tours" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tours Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (count($tours) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Mã Tour</th>
                            <th class="border-0">Tên Tour</th>
                            <th class="border-0">Điểm đến</th>
                            <th class="border-0">Thời gian</th>
                            <th class="border-0 text-end">Giá người lớn</th>
                            <th class="border-0">Trạng thái</th>
                            <th class="border-0 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tours as $tour): ?>
                        <tr>
                            <td>
                                <span class="fw-bold text-primary"><?php echo $tour['tour_code']; ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($tour['tour_name']); ?></h6>
                                        <small class="text-muted"><?php echo $tour['max_participants']; ?> chỗ</small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($tour['destination']); ?></td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-clock me-1"></i><?php echo $tour['duration_days']; ?> ngày
                                </span>
                            </td>
                            <td class="text-end fw-bold text-success">
                                <?php echo number_format($tour['price_adult']); ?> ₫
                            </td>
                            <td>
                                <?php 
                                $status_config = [
                                    'draft' => ['class' => 'bg-warning', 'text' => 'Bản nháp'],
                                    'published' => ['class' => 'bg-success', 'text' => 'Đã xuất bản'],
                                    'locked' => ['class' => 'bg-danger', 'text' => 'Đã khóa']
                                ];
                                $status = $status_config[$tour['status']] ?? ['class' => 'bg-secondary', 'text' => $tour['status']];
                                ?>
                                <span class="badge <?php echo $status['class']; ?>">
                                    <?php echo $status['text']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="?act=admin_tours_edit&id=<?php echo $tour['tour_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Sửa tour">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?act=admin_tours_delete&id=<?php echo $tour['tour_id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       data-bs-toggle="tooltip" title="Xóa tour"
                                       onclick="return confirm('Xóa tour <?php echo htmlspecialchars($tour['tour_name']); ?>?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                </div>
                <h5 class="text-muted">Không có tour nào</h5>
                <p class="text-muted mb-4">Hãy tạo tour đầu tiên để bắt đầu</p>
                <a href="?act=admin_tours_create" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Tạo tour đầu tiên
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination (có thể thêm sau) -->
<?php if (count($tours) > 0): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item disabled">
            <a class="page-link" href="#" tabindex="-1">Previous</a>
        </li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item">
            <a class="page-link" href="#">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once '../pro1014/views/admin/footer.php'; ?>
