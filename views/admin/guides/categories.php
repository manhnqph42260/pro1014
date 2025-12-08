<?php require_once './views/admin/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý Nhóm HDV</h1>
    
    <!-- Flash Message -->
    <?php if ($flash = $this->getFlash()): ?>
    <div class="alert alert-<?= $flash['type'] == 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= $flash['message'] ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Nhóm HDV</h6>
            <div>
                <a href="?act=admin_guide_category_create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm Nhóm mới
                </a>
                <a href="?act=admin_guides" class="btn btn-secondary">
                    <i class="fas fa-users"></i> Quản lý HDV
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Category Types Tabs -->
            <ul class="nav nav-tabs mb-4" id="categoryTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
                        <i class="fas fa-list"></i> Tất cả
                    </a>
                </li>
                <?php foreach ($categoryTypes as $typeKey => $typeName): ?>
                <li class="nav-item">
                    <a class="nav-link" id="<?= $typeKey ?>-tab" data-toggle="tab" href="#<?= $typeKey ?>" role="tab">
                        <i class="fas fa-<?= $typeKey == 'location' ? 'map-marker-alt' : ($typeKey == 'specialization' ? 'star' : 'user-tag') ?>"></i>
                        <?= $typeName ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content" id="categoryTabsContent">
                <!-- All Categories -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên nhóm</th>
                                    <th>Loại</th>
                                    <th>Số HDV</th>
                                    <th>HDV đang hoạt động</th>
                                    <th>Màu sắc</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= $category['category_id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($category['icon'])): ?>
                                            <i class="fas fa-<?= $category['icon'] ?> mr-2" style="color: <?= $category['color_code'] ?>"></i>
                                            <?php endif; ?>
                                            <strong><?= htmlspecialchars($category['category_name']) ?></strong>
                                        </div>
                                        <?php if (!empty($category['description'])): ?>
                                        <small class="text-muted d-block"><?= htmlspecialchars($category['description']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $type_badges = [
                                            'location' => 'primary',
                                            'specialization' => 'success',
                                            'client_type' => 'warning'
                                        ];
                                        ?>
                                        <span class="badge badge-<?= $type_badges[$category['category_type']] ?? 'secondary' ?>">
                                            <?= $categoryTypes[$category['category_type']] ?? $category['category_type'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?= $category['guide_count'] ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success"><?= $category['active_guides'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 20px; height: 20px; background: <?= $category['color_code'] ?>; border-radius: 3px; margin-right: 5px;"></div>
                                            <small><?= $category['color_code'] ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $category['is_active'] ? 'success' : 'secondary' ?>">
                                            <?= $category['is_active'] ? 'Đang sử dụng' : 'Không sử dụng' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="?act=admin_guides&category=<?= $category['category_id'] ?>" 
                                               class="btn btn-info" title="Xem HDV">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?act=admin_guide_category_edit&id=<?= $category['category_id'] ?>" 
                                               class="btn btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?act=admin_guide_category_delete&id=<?= $category['category_id'] ?>" 
                                               class="btn btn-danger" title="Xóa"
                                               onclick="return confirm('Xóa nhóm này? Các HDV sẽ được chuyển về không có nhóm.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open fa-2x mb-3"></i><br>
                                        Chưa có nhóm HDV nào
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Categories by Type -->
                <?php foreach ($categoryTypes as $typeKey => $typeName): ?>
                <div class="tab-pane fade" id="<?= $typeKey ?>" role="tabpanel">
                    <div class="row">
                        <?php 
                        $typeCategories = array_filter($categories, function($cat) use ($typeKey) {
                            return $cat['category_type'] == $typeKey;
                        });
                        ?>
                        <?php foreach ($typeCategories as $category): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow h-100 border-left-3" style="border-left-color: <?= $category['color_code'] ?>;">
                                <div class="card-header d-flex justify-content-between align-items-center" 
                                     style="background: linear-gradient(135deg, <?= $category['color_code'] ?>20, transparent);">
                                    <h6 class="m-0 font-weight-bold" style="color: <?= $category['color_code'] ?>">
                                        <?php if (!empty($category['icon'])): ?>
                                        <i class="fas fa-<?= $category['icon'] ?> mr-2"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($category['category_name']) ?>
                                    </h6>
                                    <span class="badge badge-<?= $category['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $category['guide_count'] ?> HDV
                                    </span>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($category['description'])): ?>
                                    <p class="card-text"><?= htmlspecialchars($category['description']) ?></p>
                                    <?php endif; ?>
                                    <div class="mt-3">
                                        <span class="badge badge-light">
                                            <i class="fas fa-user-check text-success mr-1"></i>
                                            <?= $category['active_guides'] ?> đang hoạt động
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between">
                                        <a href="?act=admin_guides&category=<?= $category['category_id'] ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-users"></i> Xem HDV
                                        </a>
                                        <div>
                                            <a href="?act=admin_guide_category_edit&id=<?= $category['category_id'] ?>" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?act=admin_guide_category_delete&id=<?= $category['category_id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Xóa nhóm này?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($typeCategories)): ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-folder-plus fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có nhóm nào trong loại "<?= $typeName ?>"</p>
                            <a href="?act=admin_guide_category_create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tạo nhóm mới
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-3 {
    border-left-width: 3px !important;
}
.nav-tabs .nav-link {
    color: #6c757d;
}
.nav-tabs .nav-link.active {
    color: #495057;
    font-weight: 600;
}
.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}
</style>

<?php require_once './views/admin/footer.php'; ?>