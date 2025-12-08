<?php
$page_title = "Dashboard HDV";
$breadcrumb = [
    ['title' => 'Dashboard', 'active' => true]
];
require_once __DIR__ . '/header.php';

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo $_SESSION['success'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo $_SESSION['error'];
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['error']);
}


// Giả lập dữ liệu (trong thực tế sẽ lấy từ database)
$current_tours = [
    [
        'tour_id' => 1,
        'tour_name' => 'Tour Sapa 3N2Đ',
        'tour_code' => 'T001',
        'destination' => 'Sapa, Lào Cai',
        'departure_date' => date('Y-m-d'),
        'end_date' => date('Y-m-d', strtotime('+3 days')),
        'participants' => 18,
        'checked_in' => 15,
        'status' => 'in_progress'
    ],
    [
        'tour_id' => 2,
        'tour_name' => 'Tour Hạ Long 2N1Đ',
        'tour_code' => 'T002',
        'destination' => 'Hạ Long, Quảng Ninh',
        'departure_date' => date('Y-m-d', strtotime('+2 days')),
        'end_date' => date('Y-m-d', strtotime('+4 days')),
        'participants' => 12,
        'checked_in' => 0,
        'status' => 'upcoming'
    ]
];

$upcoming_schedule = [
    ['date' => date('d/m'), 'time' => '06:00', 'activity' => 'Đón khách điểm A', 'tour' => 'Sapa'],
    ['date' => date('d/m'), 'time' => '12:00', 'activity' => 'Ăn trưa tại nhà hàng X', 'tour' => 'Sapa'],
    ['date' => date('d/m'), 'time' => '14:00', 'activity' => 'Tham quan bản Cát Cát', 'tour' => 'Sapa'],
    ['date' => date('d/m', strtotime('+1 day')), 'time' => '08:00', 'activity' => 'Đi cáp treo Fansipan', 'tour' => 'Sapa'],
];

$pending_tasks = [
    ['task' => 'Điểm danh sáng nay', 'priority' => 'high', 'due' => 'Hôm nay'],
    ['task' => 'Ghi nhật ký ngày 1', 'priority' => 'medium', 'due' => 'Hôm nay'],
    ['task' => 'Kiểm tra yêu cầu đặc biệt', 'priority' => 'medium', 'due' => 'Hôm nay'],
    ['task' => 'Chuẩn bị checklist ngày mai', 'priority' => 'low', 'due' => 'Ngày mai'],
];

$weather_data = [
    'temperature' => '24°C',
    'condition' => 'Nắng nhẹ',
    'humidity' => '75%',
    'wind' => '10 km/h'
];
?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card guide-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Tour hiện tại</h5>
                        <p class="text-muted mb-0">Đang hướng dẫn</p>
                    </div>
                    <div class="bg-primary text-white rounded-circle p-3">
                        <i class="bi bi-compass fs-4"></i>
                    </div>
                </div>
                <h2 class="mt-3 mb-0">2</h2>
                <small class="text-success"><i class="bi bi-arrow-up"></i> 1 active</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card guide-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Khách hàng</h5>
                        <p class="text-muted mb-0">Tổng số khách</p>
                    </div>
                    <div class="bg-success text-white rounded-circle p-3">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
                <h2 class="mt-3 mb-0">30</h2>
                <small class="text-success"><i class="bi bi-check-circle"></i> 15 đã check-in</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card guide-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Nhiệm vụ</h5>
                        <p class="text-muted mb-0">Cần hoàn thành</p>
                    </div>
                    <div class="bg-warning text-white rounded-circle p-3">
                        <i class="bi bi-list-check fs-4"></i>
                    </div>
                </div>
                <h2 class="mt-3 mb-0">4</h2>
                <small class="text-danger"><i class="bi bi-exclamation-circle"></i> 1 gấp</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card guide-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Thời tiết</h5>
                        <p class="text-muted mb-0">Sapa hôm nay</p>
                    </div>
                    <div class="bg-info text-white rounded-circle p-3">
                        <i class="bi bi-sun fs-4"></i>
                    </div>
                </div>
                <h2 class="mt-3 mb-0"><?php echo $weather_data['temperature']; ?></h2>
                <small class="text-info"><?php echo $weather_data['condition']; ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Current Tours -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card guide-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-compass me-2"></i>Tour đang hướng dẫn</h5>
                <a href="schedule.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($current_tours as $tour): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card border <?php echo $tour['status'] == 'in_progress' ? 'border-primary' : 'border-secondary'; ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title mb-1"><?php echo $tour['tour_name']; ?></h6>
                                        <small class="text-muted"><?php echo $tour['tour_code']; ?></small>
                                    </div>
                                    <span class="badge <?php echo $tour['status'] == 'in_progress' ? 'bg-primary' : 'bg-secondary'; ?>">
                                        <?php echo $tour['status'] == 'in_progress' ? 'Đang chạy' : 'Sắp tới'; ?>
                                    </span>
                                </div>
                                <p class="card-text small mt-2">
                                    <i class="bi bi-geo-alt"></i> <?php echo $tour['destination']; ?><br>
                                    <i class="bi bi-calendar"></i> 
                                    <?php echo date('d/m', strtotime($tour['departure_date'])); ?> - 
                                    <?php echo date('d/m', strtotime($tour['end_date'])); ?><br>
                                    <i class="bi bi-people"></i> <?php echo $tour['checked_in']; ?>/<?php echo $tour['participants']; ?> khách
                                </p>
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="tour_detail.php?id=<?php echo $tour['tour_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                    <a href="attendance.php?tour_id=<?php echo $tour['tour_id']; ?>" 
                                       class="btn btn-sm btn-success">Điểm danh</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Schedule -->
    <div class="col-md-4">
        <div class="card guide-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Lịch trình hôm nay</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($upcoming_schedule as $schedule): ?>
                    <div class="timeline-item">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo $schedule['time']; ?></strong>
                            <small class="text-muted"><?php echo $schedule['date']; ?></small>
                        </div>
                        <p class="mb-1"><?php echo $schedule['activity']; ?></p>
                        <small class="text-primary">
                            <i class="bi bi-tag"></i> <?php echo $schedule['tour']; ?>
                        </small>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-outline-primary w-100 mt-3">
                    <i class="bi bi-plus-circle me-1"></i>Thêm lịch trình
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pending Tasks & Quick Actions -->
<div class="row">
    <div class="col-md-6">
        <div class="card guide-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Nhiệm vụ chờ xử lý</h5>
                <span class="badge bg-danger">1 gấp</span>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach ($pending_tasks as $task): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="task<?php echo $task['priority']; ?>">
                                <label class="form-check-label" for="task<?php echo $task['priority']; ?>">
                                    <?php echo $task['task']; ?>
                                </label>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> Hạn: <?php echo $task['due']; ?>
                            </small>
                        </div>
                        <span class="badge bg-<?php echo $task['priority'] == 'high' ? 'danger' : ($task['priority'] == 'medium' ? 'warning' : 'secondary'); ?>">
                            <?php echo $task['priority'] == 'high' ? 'Gấp' : ($task['priority'] == 'medium' ? 'Trung bình' : 'Thấp'); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-outline-success w-100 mt-3">
                    <i class="bi bi-check-circle me-1"></i>Đánh dấu đã hoàn thành
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card guide-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Hành động nhanh</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="journal.php" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="bi bi-journal-text fs-3 mb-2"></i>
                            <span>Ghi nhật ký</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="attendance.php" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="bi bi-clipboard-check fs-3 mb-2"></i>
                            <span>Điểm danh</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="incident_report.php" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="bi bi-exclamation-triangle fs-3 mb-2"></i>
                            <span>Báo cáo sự cố</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="special_requests.php" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                            <i class="bi bi-heart fs-3 mb-2"></i>
                            <span>Yêu cầu đặc biệt</span>
                        </a>
                    </div>
                </div>
                
                <!-- Weather Widget -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h6><i class="bi bi-cloud-sun me-2"></i>Thời tiết hiện tại</h6>
                    <div class="d-flex align-items-center">
                        <div class="display-4 me-3"><?php echo $weather_data['temperature']; ?></div>
                        <div>
                            <div><?php echo $weather_data['condition']; ?></div>
                            <small class="text-muted">
                                <i class="bi bi-droplet"></i> Độ ẩm: <?php echo $weather_data['humidity']; ?><br>
                                <i class="bi bi-wind"></i> Gió: <?php echo $weather_data['wind']; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Quick Journal -->
<div class="modal fade" id="quickJournalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ghi nhật ký nhanh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickJournalForm">
                    <div class="mb-3">
                        <label class="form-label">Tour</label>
                        <select class="form-select" required>
                            <option value="">Chọn tour</option>
                            <option value="1">T001 - Tour Sapa 3N2Đ</option>
                            <option value="2">T002 - Tour Hạ Long 2N1Đ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nội dung</label>
                        <textarea class="form-control" rows="3" placeholder="Ghi chú nhanh..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thời tiết</label>
                        <select class="form-select">
                            <option value="">Chọn thời tiết</option>
                            <option value="sunny">Nắng</option>
                            <option value="cloudy">Nhiều mây</option>
                            <option value="rain">Mưa</option>
                            <option value="storm">Mưa bão</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary">Lưu nhật ký</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Offline mode toggle
    document.getElementById('offlineToggle').addEventListener('change', function() {
        if (this.checked) {
            localStorage.setItem('guide_offline_mode', 'true');
            showOfflineNotification();
        } else {
            localStorage.removeItem('guide_offline_mode');
        }
    });
    
    // Check offline status
    if (localStorage.getItem('guide_offline_mode') === 'true') {
        document.getElementById('offlineToggle').checked = true;
    }
    
    function showOfflineNotification() {
        const alert = document.createElement('div');
        alert.className = 'alert alert-info alert-dismissible fade show';
        alert.innerHTML = `
            <i class="bi bi-wifi-off me-2"></i>
            <strong>Chế độ Offline</strong> - Dữ liệu sẽ được lưu cục bộ và đồng bộ khi có mạng.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('#mainContent').prepend(alert);
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>