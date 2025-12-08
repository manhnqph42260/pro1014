<?php
$page_title = "Tạo Tour mới";
require_once '../header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
    <div>
        <h1 class="h2">Tạo Tour mới</h1>
        <p class="mb-0">Thêm tour du lịch mới vào hệ thống với đầy đủ thông tin chi tiết</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="?act=admin_tours" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<!-- Hiển thị lỗi nếu có -->
<?php if (isset($error_message)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error_message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

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
                                           placeholder="Tên tour du lịch" id="tourName" value="<?php echo $_POST['tour_name'] ?? ''; ?>">
                                    <div class="form-text">Có thể chỉnh sửa tên tour nếu cần</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng người lớn <span class="text-danger">*</span></label>
                                            <input type="number" name="adult_count" class="form-control" required 
                                                   min="1" value="<?php echo $_POST['adult_count'] ?? 2; ?>" id="adultCount">
                                            <div class="form-text">Từ 16 tuổi trở lên</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng trẻ em</label>
                                            <input type="number" name="child_count" class="form-control" 
                                                   min="0" value="<?php echo $_POST['child_count'] ?? 0; ?>" id="childCount">
                                            <div class="form-text">Từ 5 - 15 tuổi (dưới 5 tuổi miễn phí)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ngày khởi hành <span class="text-danger">*</span></label>
                                    <input type="date" name="departure_date" class="form-control" required 
                                           value="<?php echo $_POST['departure_date'] ?? ''; ?>" id="departureDate"
                                           min="<?php echo date('Y-m-d'); ?>">
                                    <div class="form-text">Chọn ngày bắt đầu tour</div>
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

                        <div class="mb-3">
                            <label class="form-label">Mô tả tour</label>
                            <textarea name="description" class="form-control" rows="4" 
                                      placeholder="Mô tả về tour, điểm nổi bật, trải nghiệm..." id="descriptionTextarea"><?php echo $_POST['description'] ?? ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lộ trình tổng quan</label>
                            <textarea name="route" class="form-control" rows="3" 
                                      placeholder="Mô tả lộ trình tour..." id="routeTextarea"><?php echo $_POST['route'] ?? ''; ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <div></div>
                            <button type="button" class="btn btn-primary next-step" data-next="2">
                                Tiếp theo: Lịch trình <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Lịch trình chi tiết -->
                    <div class="form-step" id="step2">
                        <h5 class="mb-4 text-primary">
                            <i class="bi bi-calendar-week me-2"></i>Lịch trình chi tiết
                        </h5>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>Chi tiết từng ngày</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addDayBtn">
                                    <i class="bi bi-plus-circle me-1"></i>Thêm ngày
                                </button>
                            </div>
                            
                            <div id="itineraryDays">
                                <!-- Days will be added here dynamically -->
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-prev="1">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="3">
                                Tiếp theo: Hình ảnh <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Hình ảnh -->
                    <div class="form-step" id="step3">
                        <h5 class="mb-4 text-primary">
                            <i class="bi bi-images me-2"></i>Hình ảnh tour
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Ảnh đại diện <span class="text-danger">*</span></label>
                                    <input type="file" name="featured_image" class="form-control" accept="image/*" required>
                                    <div class="form-text">Ảnh chính hiển thị cho tour (jpg, png, max 5MB)</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ảnh gallery</label>
                                    <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                                    <div class="form-text">Có thể chọn nhiều ảnh (tối đa 10 ảnh)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light">
                                    <h6>Hướng dẫn upload ảnh</h6>
                                    <ul class="small mb-0">
                                        <li>Ảnh đại diện: Tỷ lệ 16:9, độ phân giải tối thiểu 1200x675</li>
                                        <li>Ảnh gallery: Các góc nhìn khác nhau về tour</li>
                                        <li>Định dạng: JPG, PNG, WebP</li>
                                        <li>Kích thước tối đa: 5MB/ảnh</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-prev="2">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="4">
                                Tiếp theo: Giá & Chính sách <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Giá & Chính sách -->
                    <div class="form-step" id="step4">
                        <h5 class="mb-4 text-primary">
                            <i class="bi bi-currency-dollar me-2"></i>Giá & Chính sách
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
                                        <div class="form-text">Giá mặc định: 400,000₫/trẻ em</div>
                                    </div>
                                    <div class="p-2 bg-white rounded border">
                                        <small class="text-muted"><strong>Tổng giá dự kiến:</strong> <span id="totalPrice">0</span> ₫</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6>Chính sách hủy tour</h6>
                                    <textarea name="cancellation_policy" class="form-control" rows="3" 
                                              placeholder="Chính sách hủy tour, hoàn tiền..."><?php echo $_POST['cancellation_policy'] ?? 'Hủy trước 7 ngày: hoàn 100%
Hủy trước 3-7 ngày: hoàn 50%
Hủy dưới 3 ngày: không hoàn tiền'; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6>Điều khoản & Điều kiện</h6>
                                    <textarea name="terms_conditions" class="form-control" rows="5" 
                                              placeholder="Các điều khoản và điều kiện của tour..."><?php echo $_POST['terms_conditions'] ?? '1. Khách hàng cần mang theo CMND/Passport
2. Trẻ em cần có giấy khai sinh bản sao
3. Không hoàn tiền cho các dịch vụ không sử dụng
4. Công ty có quyền thay đổi lịch trình nếu cần thiết'; ?></textarea>
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
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú nhà cung cấp</label>
                            <textarea name="supplier_notes" class="form-control" rows="2" 
                                      placeholder="Ghi chú về nhà cung cấp, hợp đồng..."><?php echo $_POST['supplier_notes'] ?? ''; ?></textarea>
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

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-prev="4">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </button>
                            <div>
                                <a href="?act=admin_tours" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-x-circle me-1"></i>Hủy bỏ
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-check-circle me-1"></i>Hoàn thành & Tạo Tour
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