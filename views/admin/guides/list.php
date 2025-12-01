<?php require_once './views/admin/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý Hướng dẫn viên</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách HDV</h6>
            <a href="?act=admin_guides_create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm HDV mới
            </a>
        </div>
        
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="mb-4">
                <input type="hidden" name="act" value="admin_guides">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm HDV..." 
                               value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" <?= ($status ?? '') == 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                            <option value="inactive" <?= ($status ?? '') == 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                            <option value="on_leave" <?= ($status ?? '') == 'on_leave' ? 'selected' : '' ?>>Nghỉ phép</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="?act=admin_guides" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
            
            <!-- Guides Table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã HDV</th>
                            <th>Họ tên</th>
                            <th>Điện thoại</th>
                            <th>Email</th>
                            <th>Kinh nghiệm</th>
                            <th>Đánh giá</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guides as $guide): ?>
                        <tr>
                            <td><?= $guide['guide_id'] ?></td>
                            <td><?= htmlspecialchars($guide['guide_code']) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($guide['full_name']) ?></strong>
                                <?php if (!empty($guide['avatar_url'])): ?>
                                    <img src="<?= BASE_URL . '/' . $guide['avatar_url'] ?>" alt="Avatar" 
                                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" class="ml-2">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($guide['phone']) ?></td>
                            <td><?= htmlspecialchars($guide['email']) ?></td>
                            <td><?= $guide['experience_years'] ?> năm</td>
                            <td>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="fa fa-star <?= $i <= $guide['rating'] ? 'text-warning' : 'text-muted' ?>"></span>
                                    <?php endfor; ?>
                                    <small class="ml-1">(<?= number_format($guide['rating'], 1) ?>)</small>
                                </div>
                            </td>
                            <td>
                                <?php
                                $status_badges = [
                                    'active' => 'success',
                                    'inactive' => 'secondary',
                                    'on_leave' => 'warning'
                                ];
                                ?>
                                <span class="badge badge-<?= $status_badges[$guide['status']] ?? 'secondary' ?>">
                                    <?= $guide['status'] == 'active' ? 'Đang hoạt động' :
                                       ($guide['status'] == 'inactive' ? 'Không hoạt động' : 'Nghỉ phép') ?>
                                </span>
                                
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="?act=admin_guides_view&id=<?= $guide['guide_id'] ?>" 
                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="fas fa-eye">Chi tiết</i>
                                    </a>
                                    <a href="?act=admin_guides_edit&id=<?= $guide['guide_id'] ?>" 
                                       class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="fas fa-edit">Sửa</i>
                                    </a>
                                    <a href="?act=admin_guides_delete&id=<?= $guide['guide_id'] ?>" 
                                       class="btn btn-sm btn-danger" title="Xóa"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa HDV này?')">
                                        <i class="fas fa-trash">Xóa</i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($guides)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Không có HDV nào</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once './views/admin/footer.php'; ?>