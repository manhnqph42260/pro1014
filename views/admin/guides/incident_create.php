<?php require_once './views/admin/guides/header.php'; ?>

<div class="container-fluid guide-content">
    <div class="d-flex justify-content-between align-items-center pt-4 pb-2 mb-3 border-bottom">
        <h1 class="h3">Tạo Báo Cáo Sự Cố</h1>
        <a href="?act=guide-dashboard" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold"><i class="bi bi-exclamation-triangle-fill"></i> Thông tin sự cố</h6>
                </div>
                <div class="card-body">
                    <form action="?act=guide-incident-report" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn chuyến đi <span class="text-danger">*</span></label>
                            <select class="form-select" name="departure_id" required>
                                <option value="">-- Vui lòng chọn --</option>
                                <?php if(!empty($myTours)): ?>
                                    <?php foreach ($myTours as $tour): ?>
                                        <option value="<?= $tour['departure_id'] ?>">
                                            <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['tour_name']) ?> (<?= date('d/m', strtotime($tour['departure_date'])) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Loại sự cố</label>
                                <select class="form-select" name="incident_type" required>
                                    <option value="medical">Y tế / Sức khỏe</option>
                                    <option value="lost_property">Mất tài sản</option>
                                    <option value="delay">Trễ giờ / Delay</option>
                                    <option value="vehicle">Phương tiện / Xe cộ</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Mức độ</label>
                                <select class="form-select" name="severity" required>
                                    <option value="low" class="text-success">Thấp (Ghi nhận)</option>
                                    <option value="medium" class="text-warning">Trung bình (Cần hỗ trợ)</option>
                                    <option value="high" class="text-danger fw-bold">Cao (Khẩn cấp)</option>
                                    <option value="critical" class="text-danger fw-bold text-uppercase">Nghiêm trọng (Nguy hiểm)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề báo cáo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" placeholder="VD: Khách Nguyễn Văn A bị trật chân..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả chi tiết sự việc</label>
                            <textarea class="form-control" name="description" rows="5" placeholder="Mô tả diễn biến, thời gian, địa điểm xảy ra sự việc..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hành động đã thực hiện (nếu có)</label>
                            <textarea class="form-control" name="action_taken" rows="3" placeholder="VD: Đã sơ cứu, đã liên hệ bảo hiểm, đã báo điều hành..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="bi bi-send-fill"></i> GỬI BÁO CÁO NGAY
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once './views/admin/guides/footer.php'; ?>