<?php
$title = "Cập nhật Trạng thái - " . $booking['booking_code'];
require_once './views/admin/header.php';

// SỬA: Dùng BookingModel để lấy thông tin trạng thái
$status_history = BookingModel::getStatusHistory($booking_id);
$current_status = $booking['status'];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Cập nhật Trạng thái Booking</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="?act=admin_bookings_view&id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">📋 Thông tin Booking</h5>
            </div>
            <div class="card-body">
                <p><strong>Mã Booking:</strong> <?= $booking['booking_code'] ?></p>
                <p><strong>Tour:</strong> <?= $booking['tour_name'] ?></p>
                <p><strong>Khách hàng:</strong> <?= $booking['customer_name'] ?></p>
                <p><strong>Số khách:</strong> <?= $booking['total_guests'] ?> người</p>
                <p><strong>Tổng tiền:</strong> <?= number_format($booking['total_amount']) ?> ₫</p>
                <p><strong>Trạng thái hiện tại:</strong> 
                    <?php
                    $current_status_info = BookingModel::getStatusInfo($current_status);
                    ?>
                    <span class="badge bg-<?= $current_status_info['color'] ?>">
                        <?= $current_status_info['icon'] ?> 
                        <?= $current_status_info['name'] ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">🔄 Cập nhật Trạng thái</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Trạng thái mới *</label>
                        <select name="new_status" class="form-select" required id="statusSelect">
                            <option value="">-- Chọn trạng thái --</option>
                            <?php 
                            $all_statuses = ['pending', 'deposited', 'confirmed', 'completed', 'cancelled'];
                            foreach ($all_statuses as $status): 
                                if ($status != $current_status && BookingModel::canChangeStatus($booking_id, $status)):
                                    $status_info = BookingModel::getStatusInfo($status);
                            ?>
                                <option value="<?= $status ?>">
                                    <?= $status_info['icon'] ?> <?= $status_info['name'] ?>
                                </option>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lý do thay đổi</label>
                        <textarea name="change_reason" class="form-control" rows="3" 
                                  placeholder="Ghi rõ lý do thay đổi trạng thái..." 
                                  id="reasonTextarea"></textarea>
                        <div class="form-text">Lý do sẽ được lưu vào lịch sử thay đổi</div>
                    </div>

                    <div class="alert alert-warning" id="statusWarning" style="display: none;">
                        <strong>⚠️ Cảnh báo:</strong> <span id="warningText"></span>
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Cập nhật Trạng thái</button>
                    <a href="?act=admin_bookings_view&id=<?= $booking['booking_id'] ?>" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">📊 Lịch sử Trạng thái</h5>
                <span class="badge bg-secondary"><?= count($status_history) ?> lần thay đổi</span>
            </div>
            <div class="card-body">
                <?php if (!empty($status_history)): ?>
                    <div class="timeline">
                        <?php foreach ($status_history as $history): ?>
                            <div class="timeline-item mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>
                                            <?php
                                            $old_status_info = BookingModel::getStatusInfo($history['old_status']);
                                            $new_status_info = BookingModel::getStatusInfo($history['new_status']);
                                            ?>
                                            <span class="badge bg-<?= $old_status_info['color'] ?> me-1">
                                                <?= $old_status_info['icon'] ?> 
                                                <?= $old_status_info['name'] ?>
                                            </span>
                                            →
                                            <span class="badge bg-<?= $new_status_info['color'] ?> ms-1">
                                                <?= $new_status_info['icon'] ?> 
                                                <?= $new_status_info['name'] ?>
                                            </span>
                                        </strong>
                                        <?php if ($history['change_reason']): ?>
                                            <div class="mt-1">
                                                <small class="text-muted">Lý do: <?= htmlspecialchars($history['change_reason']) ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($history['changed_at'])) ?>
                                        </small>
                                        <div>
                                            <small>Bởi: <?= $history['changed_by_name'] ?? 'System' ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Chưa có lịch sử thay đổi trạng thái</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ... JavaScript giữ nguyên ... -->

<?php require_once './views/admin/footer.php'; ?>