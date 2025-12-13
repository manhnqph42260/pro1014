<?php
$page_title = "Điểm danh khách hàng";
$breadcrumb = [
    ['title' => 'Dashboard HDV', 'link' => '/?route=guides/dashboard'],
    ['title' => 'Chi tiết Tour', 'link' => '/?route=guides/tours/detail&id=' . $tourInfo['id']],
    ['title' => 'Điểm danh', 'active' => true]
];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-warning mb-1">ĐIỂM DANH - NGÀY <?= date('d/m/Y') ?></span>
                        <h5 class="m-0 fw-bold"><?= htmlspecialchars($tourInfo['name']) ?></h5>
                        <p class="m-0 small">Mã tour: <?= htmlspecialchars($tourInfo['code']) ?></p>
                    </div>
                    <div class="text-end">
                        <div class="display-6">Ngày <?= $tourInfo['current_day'] ?? '1' ?>/<?= $tourInfo['total_days'] ?? '1' ?></div>
                        <small class="text-white-50"><?= date('H:i') ?></small>
                    </div>
                </div>
            </div>
            
            <!-- Thống kê điểm danh hôm nay - Dải line -->
            <div class="card-body border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="mb-2">
                            <span class="fw-bold">Sĩ số hôm nay: </span>
                            <span class="badge bg-primary"><?= count($guests) ?> khách</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <?php
                            $stats = $this->attendanceModel->getTodayStatistics($tourInfo['id']);
                            $presentPercent = $stats ? ($stats['present_count'] / $stats['total_guests']) * 100 : 0;
                            $latePercent = $stats ? ($stats['late_count'] / $stats['total_guests']) * 100 : 0;
                            $absentPercent = $stats ? ($stats['absent_count'] / $stats['total_guests']) * 100 : 0;
                            ?>
                            <div class="progress-bar bg-success" style="width: <?= $presentPercent ?>%">
                                <span class="progress-text">Có mặt: <?= $stats['present_count'] ?? 0 ?></span>
                            </div>
                            <div class="progress-bar bg-warning" style="width: <?= $latePercent ?>%">
                                <span class="progress-text">Trễ: <?= $stats['late_count'] ?? 0 ?></span>
                            </div>
                            <div class="progress-bar bg-danger" style="width: <?= $absentPercent ?>%">
                                <span class="progress-text">Vắng: <?= $stats['absent_count'] ?? 0 ?></span>
                            </div>
                            <div class="progress-bar bg-secondary" style="width: <?= 100 - ($presentPercent + $latePercent + $absentPercent) ?>%">
                                <span class="progress-text">Chờ: <?= ($stats['total_guests'] ?? 0) - ($stats['present_count'] + $stats['late_count'] + $stats['absent_count'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-success" onclick="checkAll('present')">
                            <i class="bi bi-check-all me-1"></i>Điểm danh tất cả
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form điểm danh -->
<form method="POST" action="/?route=guide/attendance/process" id="attendanceForm">
    <input type="hidden" name="tour_id" value="<?= $tourInfo['id'] ?>">
    <input type="hidden" name="stop_id" value="<?= $stopId ?? '' ?>">
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Điểm danh thủ công</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">STT</th>
                            <th width="20%">Khách hàng</th>
                            <th width="15%">Liên hệ</th>
                            <th width="15%">Nhóm</th>
                            <th width="20%">Ghi chú đặc biệt</th>
                            <th width="15%">Trạng thái</th>
                            <th width="10%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guests as $index => $guest): ?>
                        <tr id="guest-<?= $guest['id'] ?>">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($guest['full_name']) ?></strong>
                                <?php if (!empty($guest['room_number'])): ?>
                                <br><small class="text-muted">Phòng: <?= $guest['room_number'] ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($guest['phone'] ?? 'N/A') ?>
                                <?php if (!empty($guest['email'])): ?>
                                <br><small class="text-muted"><?= htmlspecialchars($guest['email']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $groupTypes = [
                                    'family' => 'Gia đình',
                                    'group' => 'Nhóm bạn',
                                    'couple' => 'Cặp đôi',
                                    'individual' => 'Khách lẻ',
                                    'company' => 'Công ty'
                                ];
                                ?>
                                <span class="badge bg-info">
                                    <?= $groupTypes[$guest['group_type']] ?? $guest['group_type'] ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars($guest['special_notes'] ?? '') ?></small>
                                <textarea name="attendance[<?= $guest['id'] ?>][note]" 
                                          class="form-control form-control-sm mt-1" 
                                          rows="1" 
                                          placeholder="Ghi chú điểm danh..."
                                          style="display: none;"></textarea>
                                <textarea name="attendance[<?= $guest['id'] ?>][report_reason]" 
                                          class="form-control form-control-sm mt-1" 
                                          rows="2" 
                                          placeholder="Lý do vắng mặt (bắt buộc nếu vắng)..."
                                          style="display: none;"
                                          required></textarea>
                            </td>
                            <td>
                                <span class="badge bg-secondary status-badge" id="status-<?= $guest['id'] ?>">
                                    Chờ điểm danh
                                </span>
                                <input type="hidden" name="attendance[<?= $guest['id'] ?>][status]" 
                                       id="status-input-<?= $guest['id'] ?>" value="pending">
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-success" 
                                            onclick="setStatus(<?= $guest['id'] ?>, 'present')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" 
                                            onclick="setStatus(<?= $guest['id'] ?>, 'late', true)">
                                        <i class="bi bi-clock"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="setStatus(<?= $guest['id'] ?>, 'absent', false, true)">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Thêm khách mới -->
            <div class="mt-4 border-top pt-3">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addNewGuest()">
                    <i class="bi bi-person-plus me-1"></i>Thêm khách mới vào tour
                </button>
                <small class="text-muted ms-2">(Trường hợp có khách đi cùng nhưng không có trong danh sách)</small>
            </div>
        </div>
        
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="/?route=guide/tours/detail&id=<?= $tourInfo['id'] ?>" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary me-2" onclick="resetAll()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Đặt lại
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Lưu điểm danh
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal thêm khách mới -->
<div class="modal fade" id="addGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm khách mới vào tour</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newGuestForm" action="/?route=guide/guests/add" method="POST">
                <input type="hidden" name="tour_id" value="<?= $tourInfo['id'] ?>">
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Thêm khách không có trong danh sách đặt tour. Thông tin sẽ được báo cáo tự động cho Admin.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nhóm <span class="text-danger">*</span></label>
                            <select class="form-select" name="group_type" required>
                                <option value="">Chọn nhóm</option>
                                <option value="family">Gia đình</option>
                                <option value="group">Nhóm bạn</option>
                                <option value="couple">Cặp đôi</option>
                                <option value="individual">Khách lẻ</option>
                                <option value="company">Công ty</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số CMND/CCCD</label>
                            <input type="text" class="form-control" name="id_card">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" name="birth_date">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Ghi chú đặc biệt</label>
                            <textarea class="form-control" name="special_notes" rows="2" 
                                      placeholder="Ví dụ: Dị ứng thức ăn, cần hỗ trợ đặc biệt..."></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Lý do thêm khách <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="addition_reason" rows="3" required
                                      placeholder="Giải thích lý do thêm khách (VD: Bạn của khách A, tham gia cùng đoàn...)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm khách và gửi báo cáo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Biến lưu trạng thái
let attendanceStatus = {};

// Khởi tạo trạng thái cho tất cả khách
<?php foreach ($guests as $guest): ?>
attendanceStatus[<?= $guest['id'] ?>] = 'pending';
<?php endforeach; ?>

// Thiết lập trạng thái cho khách
function setStatus(guestId, status, showNote = false, showReport = false) {
    const row = document.getElementById(`guest-${guestId}`);
    const badge = document.getElementById(`status-${guestId}`);
    const statusInput = document.getElementById(`status-input-${guestId}`);
    const noteField = row.querySelector('textarea[name*="[note]"]');
    const reportField = row.querySelector('textarea[name*="[report_reason]"]');
    
    // Cập nhật trạng thái
    attendanceStatus[guestId] = status;
    statusInput.value = status;
    
    // Cập nhật badge
    let badgeClass, badgeText;
    switch(status) {
        case 'present':
            badgeClass = 'bg-success';
            badgeText = 'Có mặt';
            break;
        case 'late':
            badgeClass = 'bg-warning';
            badgeText = 'Đến trễ';
            break;
        case 'absent':
            badgeClass = 'bg-danger';
            badgeText = 'Vắng mặt';
            break;
        default:
            badgeClass = 'bg-secondary';
            badgeText = 'Chờ điểm danh';
    }
    
    badge.className = `badge ${badgeClass} status-badge`;
    badge.textContent = badgeText;
    
    // Hiển thị/ẩn các trường nhập
    noteField.style.display = (status === 'late' && showNote) ? 'block' : 'none';
    reportField.style.display = (status === 'absent' && showReport) ? 'block' : 'none';
    
    // Nếu là trạng thái vắng, yêu cầu nhập lý do
    if (status === 'absent') {
        reportField.required = true;
    } else {
        reportField.required = false;
    }
    
    // Thêm timestamp
    const now = new Date();
    const timeStr = now.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    
    // Hiển thị thông báo
    showToast(`Đã đánh dấu khách ${guestId}: ${badgeText}`, 
              status === 'present' ? 'success' : (status === 'late' ? 'warning' : 'danger'));
}

// Điểm danh tất cả
function checkAll(status) {
    if (confirm(`Điểm danh tất cả là "${getStatusText(status)}"?`)) {
        <?php foreach ($guests as $guest): ?>
        setStatus(<?= $guest['id'] ?>, status);
        <?php endforeach; ?>
        showToast(`Đã điểm danh tất cả là ${getStatusText(status)}`, 'info');
    }
}

// Lấy tên trạng thái
function getStatusText(status) {
    switch(status) {
        case 'present': return 'Có mặt';
        case 'late': return 'Đến trễ';
        case 'absent': return 'Vắng mặt';
        default: return 'Chờ';
    }
}

// Reset tất cả
function resetAll() {
    if (confirm('Đặt lại tất cả điểm danh?')) {
        <?php foreach ($guests as $guest): ?>
        setStatus(<?= $guest['id'] ?>, 'pending');
        <?php endforeach; ?>
    }
}

// Mở modal thêm khách
function addNewGuest() {
    const modal = new bootstrap.Modal(document.getElementById('addGuestModal'));
    modal.show();
}

// Xử lý form thêm khách
document.getElementById('newGuestForm').addEventListener('submit', function(e) {
    if (!confirm('Thêm khách mới? Thông tin sẽ được gửi báo cáo cho Admin.')) {
        e.preventDefault();
        return;
    }
});

// Kiểm tra form điểm danh
document.getElementById('attendanceForm').addEventListener('submit', function(e) {
    // Kiểm tra nếu có điểm dừng bắt buộc
    const stopId = document.querySelector('input[name="stop_id"]').value;
    const hasMandatoryStop = stopId ? true : false;
    
    if (hasMandatoryStop) {
        // Kiểm tra tất cả khách đã được điểm danh chưa
        let allChecked = true;
        for (const guestId in attendanceStatus) {
            if (attendanceStatus[guestId] === 'pending') {
                allChecked = false;
                break;
            }
        }
        
        if (!allChecked) {
            e.preventDefault();
            alert('Điểm dừng bắt buộc: Tất cả khách phải được điểm danh trước khi tiếp tục.');
            return;
        }
        
        // Kiểm tra khách vắng có lý do không
        const absentGuests = document.querySelectorAll('textarea[name*="[report_reason]"]');
        for (const field of absentGuests) {
            if (field.style.display !== 'none' && !field.value.trim()) {
                e.preventDefault();
                alert('Khách vắng mặt phải có lý do báo cáo. Vui lòng nhập lý cho tất cả khách vắng.');
                field.focus();
                return;
            }
        }
    }
});

// Hiển thị thông báo
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.style.zIndex = 1050;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-info-circle me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Lấy vị trí GPS (nếu cần)
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Thêm vào form
                document.getElementById('attendanceForm').innerHTML += `
                    <input type="hidden" name="latitude" value="${lat}">
                    <input type="hidden" name="longitude" value="${lng}">
                `;
            },
            function(error) {
                console.log('Không thể lấy vị trí:', error);
            }
        );
    }
}

// Tự động lấy vị trí khi load trang
window.addEventListener('load', getCurrentLocation);
</script>

<style>
.progress-text {
    position: absolute;
    width: 100%;
    text-align: center;
    color: white;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.status-badge {
    min-width: 100px;
    padding: 8px 12px;
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.toast {
    min-width: 300px;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>