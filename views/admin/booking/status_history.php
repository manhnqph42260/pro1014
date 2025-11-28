<?php
$title = "Lịch sử Trạng thái - " . $booking['booking_code'];
require_once './views/admin/header.php';

$status_info = [
    'pending' => ['name' => 'Chờ xác nhận', 'color' => 'warning', 'icon' => '⏳'],
    'deposited' => ['name' => 'Đã cọc', 'color' => 'info', 'icon' => '💰'],
    'confirmed' => ['name' => 'Đã xác nhận', 'color' => 'primary', 'icon' => '✅'],
    'completed' => ['name' => 'Hoàn tất', 'color' => 'success', 'icon' => '🎉'],
    'cancelled' => ['name' => 'Đã hủy', 'color' => 'danger', 'icon' => '❌']
];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Lịch sử Trạng thái Booking</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="?act=admin_bookings_view&id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại Chi tiết
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">📋 Thông tin Booking</h5>
            </div>
            <div class="card-body">
                <p><strong>Mã Booking:</strong> <?= $booking['booking_code'] ?></p>
                <p><strong>Tour:</strong> <?= $booking['tour_name'] ?></p>
                <p><strong>Khách hàng:</strong> <?= $booking['customer_name'] ?></p>
                <p><strong>Số điện thoại:</strong> <?= $booking['customer_phone'] ?></p>
                <p><strong>Trạng thái hiện tại:</strong> 
                    <span class="badge bg-<?= $status_info[$booking['status']]['color'] ?>">
                        <?= $status_info[$booking['status']]['icon'] ?> 
                        <?= $status_info[$booking['status']]['name'] ?>
                    </span>
                </p>
                <p><strong>Tổng số lần thay đổi:</strong> 
                    <span class="badge bg-secondary"><?= count($status_history) ?> lần</span>
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">📊 Thống kê</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Lần thay đổi đầu tiên:</strong><br>
                    <small class="text-muted">
                        <?= !empty($status_history) ? date('d/m/Y H:i', strtotime(end($status_history)['changed_at'])) : 'N/A' ?>
                    </small>
                </div>
                <div class="mb-2">
                    <strong>Lần thay đổi gần nhất:</strong><br>
                    <small class="text-muted">
                        <?= !empty($status_history) ? date('d/m/Y H:i', strtotime($status_history[0]['changed_at'])) : 'N/A' ?>
                    </small>
                </div>
                <div>
                    <strong>Thời gian từ lúc tạo:</strong><br>
                    <small class="text-muted">
                        <?php
                        if (!empty($booking['booked_at'])) {
                            $created_date = new DateTime($booking['booked_at']);
                            $now = new DateTime();
                            $interval = $created_date->diff($now);
                            echo $interval->format('%a ngày %h giờ');
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">🔄 Dòng thời gian Thay đổi Trạng thái</h5>
                <div>
                    <span class="badge bg-primary"><?= count($status_history) ?> bản ghi</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($status_history)): ?>
                    <div class="timeline">
                        <?php foreach ($status_history as $index => $history): ?>
                            <div class="timeline-item mb-4 position-relative">
                                <div class="timeline-badge">
                                    <span class="badge bg-<?= $status_info[$history['new_status']]['color'] ?>">
                                        <?= $status_info[$history['new_status']]['icon'] ?>
                                    </span>
                                </div>
                                <div class="timeline-content p-3 border rounded bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="badge bg-<?= $status_info[$history['old_status']]['color'] ?> me-1">
                                                    <?= $status_info[$history['old_status']]['name'] ?>
                                                </span>
                                                <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                <span class="badge bg-<?= $status_info[$history['new_status']]['color'] ?>">
                                                    <?= $status_info[$history['new_status']]['name'] ?>
                                                </span>
                                            </h6>
                                            <?php if ($history['change_reason']): ?>
                                                <p class="mb-1 mt-2">
                                                    <strong>Lý do:</strong> <?= nl2br(htmlspecialchars($history['change_reason'])) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block">
                                                <?= date('d/m/Y H:i', strtotime($history['changed_at'])) ?>
                                            </small>
                                            <small class="text-muted">
                                                Bởi: <strong><?= $history['changed_by_name'] ?? 'System' ?></strong>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <?php if ($index > 0): ?>
                                        <?php
                                        $current_time = new DateTime($history['changed_at']);
                                        $prev_time = new DateTime($status_history[$index-1]['changed_at']);
                                        $time_diff = $prev_time->diff($current_time);
                                        ?>
                                        <div class="timeline-duration mt-2 p-2 bg-white border rounded">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Cách lần trước: 
                                                <?php
                                                if ($time_diff->d > 0) echo $time_diff->d . ' ngày ';
                                                if ($time_diff->h > 0) echo $time_diff->h . ' giờ ';
                                                if ($time_diff->i > 0) echo $time_diff->i . ' phút ';
                                                if ($time_diff->d == 0 && $time_diff->h == 0 && $time_diff->i == 0) {
                                                    echo 'Vừa xong';
                                                }
                                                ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chưa có lịch sử thay đổi trạng thái</h5>
                        <p class="text-muted">Trạng thái booking chưa được thay đổi kể từ khi tạo.</p>
                        <a href="?act=admin_bookings_update_status&id=<?= $booking['booking_id'] ?>" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Cập nhật Trạng thái đầu tiên
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
}
.timeline-badge {
    position: absolute;
    left: -45px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 2px solid #dee2e6;
    z-index: 2;
}
.timeline-content {
    position: relative;
    z-index: 1;
}
.timeline:before {
    content: '';
    position: absolute;
    left: -30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-duration {
    font-size: 0.85em;
}
</style>

<?php require_once './views/admin/footer.php'; ?>