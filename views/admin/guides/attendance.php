<?php
$page_title = "Điểm danh khách hàng";
$breadcrumb = [
    ['title' => 'Chi tiết Tour', 'link' => 'tour_detail.php'],
    ['title' => 'Điểm danh', 'active' => true]
];
require_once __DIR__ . '/header.php';

// Giả lập dữ liệu điểm danh
$tour_info = [
    'tour_id' => 1,
    'tour_name' => 'Tour Sapa 3 ngày 2 đêm',
    'tour_code' => 'T001',
    'departure_date' => date('Y-m-d'),
    'current_day' => 2,
    'meeting_point' => 'Sảnh khách sạn XYZ',
    'meeting_time' => '07:30',
    'next_activity' => 'Đi cáp treo Fansipan',
    'next_activity_time' => '08:30'
];

$attendance_records = [
    [
        'date' => date('Y-m-d'),
        'check_points' => [
            [
                'time' => '07:30',
                'location' => 'Sảnh khách sạn',
                'type' => 'sáng',
                'participants' => [
                    ['id' => 1, 'name' => 'Nguyễn Văn An', 'status' => 'present', 'check_time' => '07:28'],
                    ['id' => 2, 'name' => 'Trần Thị Bình', 'status' => 'present', 'check_time' => '07:25'],
                    ['id' => 3, 'name' => 'Lê Minh Cường', 'status' => 'absent', 'check_time' => null],
                    ['id' => 4, 'name' => 'Phạm Thị Dung', 'status' => 'late', 'check_time' => '07:45'],
                    ['id' => 5, 'name' => 'John Smith', 'status' => 'present', 'check_time' => '07:29']
                ]
            ]
        ]
    ],
    [
        'date' => date('Y-m-d', strtotime('-1 day')),
        'check_points' => [
            [
                'time' => '06:00',
                'location' => 'Điểm đón Hà Nội',
                'type' => 'khởi hành',
                'participants' => [
                    ['id' => 1, 'name' => 'Nguyễn Văn An', 'status' => 'present', 'check_time' => '05:55'],
                    ['id' => 2, 'name' => 'Trần Thị Bình', 'status' => 'present', 'check_time' => '05:58'],
                    ['id' => 3, 'name' => 'Lê Minh Cường', 'status' => 'present', 'check_time' => '06:00'],
                    ['id' => 4, 'name' => 'Phạm Thị Dung', 'status' => 'late', 'check_time' => '06:15'],
                    ['id' => 5, 'name' => 'John Smith', 'status' => 'present', 'check_time' => '06:00']
                ]
            ],
            [
                'time' => '19:00',
                'location' => 'Khách sạn Sapa',
                'type' => 'tối',
                'participants' => [
                    ['id' => 1, 'name' => 'Nguyễn Văn An', 'status' => 'present', 'check_time' => '19:00'],
                    ['id' => 2, 'name' => 'Trần Thị Bình', 'status' => 'present', 'check_time' => '19:02'],
                    ['id' => 3, 'name' => 'Lê Minh Cường', 'status' => 'present', 'check_time' => '19:00'],
                    ['id' => 4, 'name' => 'Phạm Thị Dung', 'status' => 'present', 'check_time' => '19:05'],
                    ['id' => 5, 'name' => 'John Smith', 'status' => 'present', 'check_time' => '19:00']
                ]
            ]
        ]
    ]
];

$participants = [
    ['id' => 1, 'name' => 'Nguyễn Văn An', 'group' => 'Gia đình A', 'room' => '201'],
    ['id' => 2, 'name' => 'Trần Thị Bình', 'group' => 'Gia đình A', 'room' => '201'],
    ['id' => 3, 'name' => 'Lê Minh Cường', 'group' => 'Nhóm bạn', 'room' => '205'],
    ['id' => 4, 'name' => 'Phạm Thị Dung', 'group' => 'Nhóm bạn', 'room' => '205'],
    ['id' => 5, 'name' => 'John Smith', 'group' => 'Khách lẻ', 'room' => '301']
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card guide-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4><?php echo $tour_info['tour_name']; ?></h4>
                        <p class="text-muted mb-2">Mã tour: <?php echo $tour_info['tour_code']; ?></p>
                    </div>
                    <div class="text-end">
                        <div class="display-6">Ngày <?php echo $tour_info['current_day']; ?></div>
                        <small class="text-muted"><?php echo date('d/m/Y'); ?></small>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><i class="bi bi-clock"></i> <strong>Giờ tập trung:</strong> <?php echo $tour_info['meeting_time']; ?></p>
                        <p><i class="bi bi-geo-alt"></i> <strong>Địa điểm:</strong> <?php echo $tour_info['meeting_point']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><i class="bi bi-calendar-check"></i> <strong>Hoạt động tiếp theo:</strong> <?php echo $tour_info['next_activity']; ?></p>
                        <p><i class="bi bi-clock-history"></i> <strong>Thời gian:</strong> <?php echo $tour_info['next_activity_time']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card guide-card">
            <div class="card-body">
                <h6 class="text-center mb-3">Thống kê điểm danh hôm nay</h6>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="display-6 text-success">3</div>
                        <small>Có mặt</small>
                    </div>
                    <div class="col-4">
                        <div class="display-6 text-danger">1</div>
                        <small>Vắng</small>
                    </div>
                    <div class="col-4">
                        <div class="display-6 text-warning">1</div>
                        <small>Trễ</small>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: 60%"></div>
                        <div class="progress-bar bg-warning" style="width: 20%"></div>
                        <div class="progress-bar bg-danger" style="width: 20%"></div>
                    </div>
                    <small class="text-muted">3/5 đã điểm danh</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Check-in -->
<div class="card guide-card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-qr-code-scan me-2"></i>Điểm danh nhanh - <?php echo date('H:i'); ?></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card border">
                    <div class="card-body text-center">
                        <i class="bi bi-qr-code display-1 text-primary"></i>
                        <h5 class="mt-3">Quét QR Code</h5>
                        <p class="text-muted">Khách quét mã để tự điểm danh</p>
                        <button class="btn btn-primary w-100" onclick="showQRScanner()">
                            <i class="bi bi-camera me-1"></i>Mở máy quét QR
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <h6><i class="bi bi-list-check me-2"></i>Điểm danh thủ công</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Khách hàng</th>
                                <th>Nhóm</th>
                                <th>Phòng</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $participant): 
                                // Find today's status
                                $today_status = 'pending';
                                $check_time = null;
                                
                                foreach ($attendance_records[0]['check_points'][0]['participants'] as $record) {
                                    if ($record['id'] === $participant['id']) {
                                        $today_status = $record['status'];
                                        $check_time = $record['check_time'];
                                        break;
                                    }
                                }
                            ?>
                            <tr id="participant-<?php echo $participant['id']; ?>">
                                <td>
                                    <strong><?php echo $participant['name']; ?></strong>
                                    <?php if ($check_time): ?>
                                    <br><small class="text-muted"><?php echo $check_time; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $participant['group']; ?></td>
                                <td><?php echo $participant['room']; ?></td>
                                <td>
                                    <span class="badge 
                                        <?php echo $today_status === 'present' ? 'bg-success' : 
                                               ($today_status === 'absent' ? 'bg-danger' : 
                                               ($today_status === 'late' ? 'bg-warning' : 'bg-secondary')); ?>"
                                          id="status-<?php echo $participant['id']; ?>">
                                        <?php echo $today_status === 'present' ? 'Có mặt' : 
                                               ($today_status === 'absent' ? 'Vắng' : 
                                               ($today_status === 'late' ? 'Trễ' : 'Chờ')); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-success" 
                                                onclick="checkIn(<?php echo $participant['id']; ?>, 'present')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" 
                                                onclick="checkIn(<?php echo $participant['id']; ?>, 'late')">
                                            <i class="bi bi-clock"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" 
                                                onclick="checkIn(<?php echo $participant['id']; ?>, 'absent')">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-outline-secondary" onclick="checkAll('present')">
                        <i class="bi bi-check-all me-1"></i>Điểm danh tất cả
                    </button>
                    <button class="btn btn-primary" onclick="saveAttendance()">
                        <i class="bi bi-save me-1"></i>Lưu điểm danh
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance History -->
<div class="card guide-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Lịch sử điểm danh</h5>
        <button class="btn btn-sm btn-outline-primary" onclick="exportAttendance()">
            <i class="bi bi-download me-1"></i>Xuất báo cáo
        </button>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="attendanceTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="today-tab" data-bs-toggle="tab" data-bs-target="#today">
                    Hôm nay
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="yesterday-tab" data-bs-toggle="tab" data-bs-target="#yesterday">
                    Hôm qua
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all">
                    Tất cả
                </button>
            </li>
        </ul>
        
        <div class="tab-content mt-3" id="attendanceTabContent">
            <!-- Today's Attendance -->
            <div class="tab-pane fade show active" id="today">
                <?php if (!empty($attendance_records[0]['check_points'])): ?>
                    <?php foreach ($attendance_records[0]['check_points'] as $check_point): ?>
                    <div class="card border mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-geo-alt"></i> <?php echo $check_point['location']; ?>
                                <span class="badge bg-primary ms-2"><?php echo $check_point['time']; ?></span>
                                <small class="text-muted ms-2">(<?php echo $check_point['type']; ?>)</small>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($check_point['participants'] as $participant): ?>
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="participant-status status-<?php echo $participant['status']; ?>"></div>
                                        <div class="flex-grow-1 ms-2">
                                            <div class="fw-bold"><?php echo $participant['name']; ?></div>
                                            <small class="text-muted">
                                                <?php if ($participant['check_time']): ?>
                                                <?php echo $participant['check_time']; ?>
                                                <?php else: ?>
                                                Chưa điểm danh
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <span class="badge 
                                            <?php echo $participant['status'] === 'present' ? 'bg-success' : 
                                                   ($participant['status'] === 'absent' ? 'bg-danger' : 'bg-warning'); ?>">
                                            <?php echo $participant['status'] === 'present' ? 'Có mặt' : 
                                                   ($participant['status'] === 'absent' ? 'Vắng' : 'Trễ'); ?>
                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted my-4">Chưa có điểm danh nào hôm nay</p>
                <?php endif; ?>
            </div>
            
            <!-- Yesterday's Attendance -->
            <div class="tab-pane fade" id="yesterday">
                <?php if (!empty($attendance_records[1]['check_points'])): ?>
                    <?php foreach ($attendance_records[1]['check_points'] as $check_point): ?>
                    <div class="card border mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-geo-alt"></i> <?php echo $check_point['location']; ?>
                                <span class="badge bg-primary ms-2"><?php echo $check_point['time']; ?></span>
                                <small class="text-muted ms-2">(<?php echo $check_point['type']; ?>)</small>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Khách hàng</th>
                                            <th>Trạng thái</th>
                                            <th>Thời gian</th>
                                            <th>Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($check_point['participants'] as $participant): ?>
                                        <tr>
                                            <td><?php echo $participant['name']; ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?php echo $participant['status'] === 'present' ? 'bg-success' : 
                                                           ($participant['status'] === 'absent' ? 'bg-danger' : 'bg-warning'); ?>">
                                                    <?php echo $participant['status'] === 'present' ? 'Có mặt' : 
                                                           ($participant['status'] === 'absent' ? 'Vắng' : 'Trễ'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $participant['check_time'] ?: '--:--'; ?></td>
                                            <td>
                                                <?php if ($participant['status'] === 'late'): ?>
                                                <small class="text-warning">Đến muộn 15 phút</small>
                                                <?php elseif ($participant['status'] === 'absent'): ?>
                                                <small class="text-danger">Không tham gia</small>
                                                <?php else: ?>
                                                <small class="text-success">Đúng giờ</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted my-4">Không có dữ liệu điểm danh ngày hôm qua</p>
                <?php endif; ?>
            </div>
            
            <!-- All Attendance -->
            <div class="tab-pane fade" id="all">
                <div class="card border">
                    <div class="card-body">
                        <h6><i class="bi bi-bar-chart me-2"></i>Biểu đồ điểm danh</h6>
                        <canvas id="attendanceChart" height="100"></canvas>
                        
                        <div class="table-responsive mt-4">
                            <table class="table table-sm" id="attendanceSummary">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Ngày 1</th>
                                        <th>Ngày 2</th>
                                        <th>Ngày 3</th>
                                        <th>Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participants as $participant): ?>
                                    <tr>
                                        <td><?php echo $participant['name']; ?></td>
                                        <td><span class="badge bg-success">✔</span></td>
                                        <td><span class="badge bg-warning">⌚</span></td>
                                        <td><span class="badge bg-secondary">?</span></td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" style="width: 67%"></div>
                                            </div>
                                            <small>67%</small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="fixed-bottom bg-white border-top p-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div>
                <a href="tour_detail.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại chi tiết tour
                </a>
            </div>
            <div class="btn-group">
                <button class="btn btn-success" onclick="createCheckPoint()">
                    <i class="bi bi-plus-circle me-1"></i>Tạo điểm danh mới
                </button>
                <button class="btn btn-primary" onclick="printAttendance()">
                    <i class="bi bi-printer me-1"></i>In báo cáo
                </button>
                <button class="btn btn-info" onclick="syncAttendance()">
                    <i class="bi bi-cloud-arrow-up me-1"></i>Đồng bộ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quét QR Code điểm danh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrScanner" class="border rounded p-3" style="height: 300px; background: #000;">
                    <!-- QR Scanner will be here -->
                </div>
                <p class="mt-3">Đưa mã QR của khách vào khung hình để quét</p>
                
                <div class="mt-3">
                    <button class="btn btn-outline-primary" onclick="toggleCamera()">
                        <i class="bi bi-camera-video me-1"></i>Bật/Tắt camera
                    </button>
                    <button class="btn btn-outline-secondary" onclick="manualQR()">
                        <i class="bi bi-keyboard me-1"></i>Nhập thủ công
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Check Point Modal -->
<div class="modal fade" id="checkPointModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo điểm danh mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="checkPointForm">
                    <div class="mb-3">
                        <label class="form-label">Tên điểm danh *</label>
                        <input type="text" class="form-control" placeholder="VD: Điểm danh sáng, Điểm danh tối..." required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Thời gian *</label>
                            <input type="time" class="form-control" value="<?php echo date('H:i'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Địa điểm *</label>
                            <input type="text" class="form-control" placeholder="Sảnh khách sạn, Bãi đỗ xe..." required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Loại điểm danh</label>
                        <select class="form-select">
                            <option value="morning">Buổi sáng</option>
                            <option value="afternoon">Buổi chiều</option>
                            <option value="evening">Buổi tối</option>
                            <option value="departure">Khởi hành</option>
                            <option value="arrival">Đến nơi</option>
                            <option value="meal">Bữa ăn</option>
                            <option value="activity">Hoạt động</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" rows="2" placeholder="Ghi chú đặc biệt..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveCheckPoint()">Tạo điểm danh</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let attendanceData = {};
let qrScanner = null;

// Initialize attendance data
<?php foreach ($participants as $p): ?>
attendanceData[<?php echo $p['id']; ?>] = 'pending';
<?php endforeach; ?>

// Update participant status
function checkIn(participantId, status) {
    const badge = document.getElementById(`status-${participantId}`);
    const row = document.getElementById(`participant-${participantId}`);
    
    attendanceData[participantId] = status;
    
    // Update badge
    badge.className = `badge bg-${status === 'present' ? 'success' : (status === 'late' ? 'warning' : 'danger')}`;
    badge.textContent = status === 'present' ? 'Có mặt' : (status === 'late' ? 'Trễ' : 'Vắng');
    
    // Add timestamp
    const now = new Date();
    const timeStr = now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    
    const timeElement = row.querySelector('small');
    if (timeElement) {
        timeElement.textContent = timeStr;
    }
    
    // Show notification
    const participantName = row.querySelector('strong').textContent;
    showToast(`${participantName}: ${status === 'present' ? 'Đã điểm danh' : (status === 'late' ? 'Đến muộn' : 'Vắng mặt')}`, 
              status === 'present' ? 'success' : (status === 'late' ? 'warning' : 'danger'));
}

function checkAll(status) {
    if (confirm(`Điểm danh tất cả là "${status === 'present' ? 'Có mặt' : (status === 'late' ? 'Đến muộn' : 'Vắng mặt')}"?`)) {
        <?php foreach ($participants as $p): ?>
        checkIn(<?php echo $p['id']; ?>, status);
        <?php endforeach; ?>
    }
}

function saveAttendance() {
    const presentCount = Object.values(attendanceData).filter(s => s === 'present').length;
    const lateCount = Object.values(attendanceData).filter(s => s === 'late').length;
    const absentCount = Object.values(attendanceData).filter(s => s === 'absent').length;
    
    if (confirm(`Lưu điểm danh?\nCó mặt: ${presentCount}, Trễ: ${lateCount}, Vắng: ${absentCount}`)) {
        // In real app, send to server
        alert('Đã lưu điểm danh');
        
        // Update statistics
        updateStatistics(presentCount, lateCount, absentCount);
    }
}

function updateStatistics(present, late, absent) {
    const total = present + late + absent;
    
    // Update numbers
    document.querySelectorAll('.display-6')[0].textContent = present;
    document.querySelectorAll('.display-6')[1].textContent = absent;
    document.querySelectorAll('.display-6')[2].textContent = late;
    
    // Update progress bar
    const progressBar = document.querySelector('.progress-bar');
    const presentPercent = (present / total) * 100;
    const latePercent = (late / total) * 100;
    
    progressBar.style.width = `${presentPercent}%`;
    progressBar.nextElementSibling.style.width = `${latePercent}%`;
}

function showQRScanner() {
    $('#qrScannerModal').modal('show');
    
    // In real app, initialize QR scanner
    setTimeout(() => {
        document.getElementById('qrScanner').innerHTML = `
            <div class="text-center text-white">
                <i class="bi bi-qr-code-scan display-1"></i>
                <p class="mt-2">Đang khởi động máy quét QR...</p>
            </div>
        `;
        
        // Simulate QR scan after 2 seconds
        setTimeout(() => {
            simulateQRScan();
        }, 2000);
    }, 500);
}

function simulateQRScan() {
    const fakeQRData = {
        participantId: 3,
        name: 'Lê Minh Cường',
        code: 'CUST003'
    };
    
    document.getElementById('qrScanner').innerHTML = `
        <div class="text-center text-success">
            <i class="bi bi-check-circle display-1"></i>
            <p class="mt-2">Đã quét mã thành công!</p>
            <p class="mb-0">${fakeQRData.name}</p>
            <small>Mã: ${fakeQRData.code}</small>
        </div>
    `;
    
    // Auto check-in after scan
    setTimeout(() => {
        checkIn(fakeQRData.participantId, 'present');
        $('#qrScannerModal').modal('hide');
    }, 1500);
}

function toggleCamera() {
    alert('Chức năng bật/tắt camera (trong ứng dụng thực tế sẽ điều khiển camera)');
}

function manualQR() {
    const manualCode = prompt('Nhập mã QR thủ công:');
    if (manualCode) {
        alert(`Đã nhập mã: ${manualCode}\nTìm khách hàng tương ứng...`);
        // In real app, lookup participant by code
    }
}

function createCheckPoint() {
    $('#checkPointModal').modal('show');
}

function saveCheckPoint() {
    const form = document.getElementById('checkPointForm');
    if (form.checkValidity()) {
        alert('Đã tạo điểm danh mới');
        $('#checkPointModal').modal('hide');
        form.reset();
        
        // In real app, refresh attendance list
    } else {
        form.reportValidity();
    }
}

function exportAttendance() {
    const format = prompt('Chọn định dạng xuất (pdf/excel):', 'excel');
    if (format) {
        const fileName = `diem_danh_<?php echo $tour_info["tour_code"]; ?>_<?php echo date('Y-m-d'); ?>.${format}`;
        alert(`Đang xuất file: ${fileName}`);
        // In real app, generate and download file
    }
}

function printAttendance() {
    const printContent = document.querySelector('.guide-content').innerHTML;
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Báo cáo điểm danh - <?php echo $tour_info["tour_name"]; ?></title>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { text-align: center; margin-bottom: 30px; }
                .table { width: 100%; border-collapse: collapse; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; }
                .table th { background-color: #f2f2f2; }
                .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; }
                .bg-success { background-color: #28a745; color: white; }
                .bg-warning { background-color: #ffc107; color: black; }
                .bg-danger { background-color: #dc3545; color: white; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>BÁO CÁO ĐIỂM DANH</h2>
                <h4><?php echo $tour_info["tour_name"]; ?> (<?php echo $tour_info["tour_code"]; ?>)</h4>
                <p>Ngày: <?php echo date('d/m/Y'); ?></p>
                <p>HDV: <?php echo $_SESSION['guide_name'] ?? 'Hướng dẫn viên'; ?></p>
            </div>
            ${printContent}
        </body>
        </html>
    `;
    
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}

function syncAttendance() {
    if (confirm('Đồng bộ dữ liệu điểm danh lên server?')) {
        alert('Đang đồng bộ...');
        // In real app, sync with server
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.style.zIndex = 1050;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize Chart.js
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Nguyễn Văn An', 'Trần Thị Bình', 'Lê Minh Cường', 'Phạm Thị Dung', 'John Smith'],
            datasets: [
                {
                    label: 'Có mặt',
                    data: [2, 2, 1, 1, 2],
                    backgroundColor: '#28a745'
                },
                {
                    label: 'Trễ',
                    data: [0, 0, 0, 1, 0],
                    backgroundColor: '#ffc107'
                },
                {
                    label: 'Vắng',
                    data: [0, 0, 1, 0, 0],
                    backgroundColor: '#dc3545'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    max: 3
                }
            }
        }
    });
});
</script>

<style>
.participant-status {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}
.status-present { background-color: #28a745; }
.status-absent { background-color: #dc3545; }
.status-late { background-color: #ffc107; }
.status-pending { background-color: #6c757d; }
</style>

<?php require_once __DIR__ . '/footer.php'; ?>