<?php
$page_title = "Tạo Tour mới";
require_once '../header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
    <div>
        <h1 class="h2">Tạo Tour mới</h1>
        <p class="mb-0">Thêm tour du lịch mới vào hệ thống</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="?act=admin_tours" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <!-- Thông tin cơ bản -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">
                                <i class="bi bi-info-circle me-2"></i>Thông tin cơ bản
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Mã Tour <span class="text-danger">*</span></label>
                                <input type="text" name="tour_code" class="form-control" required 
                                       placeholder="VD: TOUR001" value="<?php echo $_POST['tour_code'] ?? ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tên Tour <span class="text-danger">*</span></label>
                                <input type="text" name="tour_name" class="form-control" required 
                                       placeholder="Tên tour du lịch" value="<?php echo $_POST['tour_name'] ?? ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Điểm đến <span class="text-danger">*</span></label>
                                <input type="text" name="destination" class="form-control" required 
                                       placeholder="VD: Sapa, Lào Cai" value="<?php echo $_POST['destination'] ?? ''; ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số ngày <span class="text-danger">*</span></label>
                                        <input type="number" name="duration_days" class="form-control" required 
                                               min="1" value="<?php echo $_POST['duration_days'] ?? 1; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số chỗ tối đa</label>
                                        <input type="number" name="max_participants" class="form-control" 
                                               value="<?php echo $_POST['max_participants'] ?? 20; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Giá & Trạng thái -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">
                                <i class="bi bi-currency-dollar me-2"></i>Giá & Trạng thái
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Giá người lớn <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="price_adult" class="form-control" required 
                                                   value="<?php echo $_POST['price_adult'] ?? ''; ?>">
                                            <span class="input-group-text">₫</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Giá trẻ em</label>
                                        <div class="input-group">
                                            <input type="number" name="price_child" class="form-control" 
                                                   value="<?php echo $_POST['price_child'] ?? ''; ?>">
                                            <span class="input-group-text">₫</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Độ khó</label>
                                <select name="difficulty" class="form-select">
                                    <option value="easy">Dễ</option>
                                    <option value="medium" selected>Trung bình</option>
                                    <option value="hard">Khó</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select name="status" class="form-select">
                                    <option value="draft">Bản nháp</option>
                                    <option value="published">Đã xuất bản</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ảnh đại diện</label>
                                <input type="file" name="featured_image" class="form-control" accept="image/*">
                                <div class="form-text">Chọn ảnh đại diện cho tour</div>
                            </div>
                        </div>

                        <!-- Mô tả -->
                        <div class="col-12">
                            <h5 class="mb-3 text-primary">
                                <i class="bi bi-text-paragraph me-2"></i>Mô tả tour
                            </h5>
                            <div class="mb-3">
                                <label class="form-label">Mô tả chi tiết</label>
                                <textarea name="description" class="form-control" rows="4" 
                                          placeholder="Mô tả về tour, điểm nổi bật, trải nghiệm..."><?php echo $_POST['description'] ?? ''; ?></textarea>
                            </div>
                        </div>

                        <!-- Tuyến đường -->
                        <div class="col-12">
                            <h5 class="mb-3 text-primary">
                                <i class="bi bi-geo-alt me-2"></i>Tuyến đường
                            </h5>
                            <div class="mb-3">
                                <label class="form-label">Lộ trình</label>
                                <textarea name="route" class="form-control" rows="3" 
                                          placeholder="Mô tả lộ trình tour..."><?php echo $_POST['route'] ?? ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="?act=admin_tours" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Hủy bỏ
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Tạo Tour
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>