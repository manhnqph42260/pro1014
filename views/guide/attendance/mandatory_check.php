<?php
$page_title = "Điểm danh bắt buộc - " . ($stopInfo['name'] ?? 'Điểm dừng');
$breadcrumb = [
    ['title' => 'Dashboard HDV', 'link' => '/?route=guide/dashboard'],
    ['title' => 'Chi tiết Tour', 'link' => '/?route=guide/tours/detail&id=' . $tourInfo['id']],
    ['title' => 'Điểm dừng', 'link' => '/?route=guide/tour_stops/list&tour_id=' . $tourInfo['id']],
    ['title' => 'Điểm danh bắt buộc', 'active' => true]
];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm border-danger border-3">
            <div class="card-header bg-danger text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-warning mb-1">ĐIỂM DANH BẮT BUỘC</span>
                        <h5 class="m-0 fw-bold"><?= htmlspecialchars($stopInfo['name'] ?? 'Điểm dừng') ?></h5>
                        <p class="m-0 small">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($stopInfo['location'] ?? '') ?>
                            | Tour: <?= htmlspecialchars($tourInfo['name']) ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="display-6"><?= date('H:i') ?></div>
                        <small class="text-white-50">Thời gian yêu cầu: <?= $stopInfo['required_time'] ?? '07:30' ?></small>
                    </div>
                </div>
            </div>
            
            <!-- Thông báo quan trọng -->
            <div class="card-body bg-warning bg-opacity-10">
                <div class="alert alert-warning border-warning">
                    <h5><i class="bi bi-exclamation-triangle-fill me-2"></i>QUAN TRỌNG: ĐIỂM DANH BẮT BUỘC</h5>
                    <ul class="mb-0">
                        <li>Đây là điểm dừng bắt buộc trước khi tiếp tục tour</li>
                        <li>Tất cả khách phải được điểm danh trước khi có thể khởi hành</li>
                        <li>Khách đến trễ: <strong>Phải nhập ghi chú</strong></li>
                        <li>Khách vắng mặt: <strong>Phải có lý do báo cáo</strong> và chờ duyệt</li>
                        <li>Không thể tiếp tục nếu chưa hoàn thành điểm danh</li>
                    </ul>
                </div>
                
                <!-- Thống kê điểm danh -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-body p-3">
                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="display-4 fw-bold text-success" id="presentCount">0</div>
                                        <small>Có mặt</small>
                                    </div>
                                    <div class="col-3">
                                        <div class="display-4 fw-bold text-warning" id="lateCount">0</div>
                                        <small>Trễ</small>
                                    </div>
                                    <div class="col-3">
                                        <div class="display-4 fw-bold text-danger" id="absentCount">0</div>
                                        <small>Vắng</small>
                                    </div>
                                    <div class="col-3">
                                        <div class="display-4 fw-bold text-secondary" id="pendingCount"><?= count($guests) ?></div>
                                        <small>Chờ điểm danh</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form điểm danh bắt buộc -->
<form method="POST" action="/?route=guide/attendance/process" id="mandatoryAttendanceForm">
    <input type="hidden" name="tour_id" value="<?= $tourInfo['id'] ?>">
    <input type="hidden" name="stop_id" value="<?= $stopInfo['id'] ?>">
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Danh sách khách - Điểm danh bắt buộc</h5>
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
                            <th width="25%">
                                <span class="text-danger">*</span> Ghi chú/Lý do 
                                <small class="text-muted">(Bắt buộc nếu trễ/vắng)</small>
                            </th>
                            <th width="10%">Trạng thái</th>
                            <th width="10%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guests as $index => $guest): ?>
                        <tr id="mandatory-guest-<?= $guest['id'] ?>" 
                            class="<?= $guest['special_notes'] ? 'table-info' : '' ?>">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($guest['full_name']) ?></strong>
                                <?php if (!empty($guest['room_number'])): ?>
                                <br><small class="text-muted">Phòng: <?= $guest['room_number'] ?></small>
                                <?php endif; ?>
                                <?php if (!empty($guest['special_notes'])): ?>
                                <br><small class="text-danger">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    <?= htmlspecialchars($guest['special_notes']) ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($guest['phone'] ?? 'N/A') ?>
                                <?php if (!empty($guest['emergency_contact'])): ?>
                                <br><small class="text-danger">
                                    <i class="bi bi-telephone"></i> 
                                    <?= htmlspecialchars($guest['emergency_contact']) ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?= $guest['group_type'] === 'family' ? 'Gia đình' : 
                                       ($guest['group_type'] === 'group' ? 'Nhóm bạn' : 
                                       ($guest['group_type'] === 'couple' ? 'Cặp đôi' : 'Khách lẻ')) ?>
                                </span>
                            </td>
                            <td>
                                <!-- Ghi chú cho khách đến trễ -->
                                <div class="late-note" style="display: none;">
                                    <label class="form-label small text-warning">
                                        <i class="bi bi-clock"></i> Ghi chú đến trễ:
                                    </label>
                                    <textarea name="attendance[<?= $guest['id'] ?>][note]" 
                                              class="form-control form-control-sm" 
                                              rows="2"
                                              placeholder="Lý do đến trễ, thời gian dự kiến..."></textarea>
                                </div>
                                
                                <!-- Lý do báo cáo cho khách vắng -->
                                <div class="absent-report" style="display: none;">
                                    <label class="form-label small text-danger">
                                        <i class="bi bi-exclamation-triangle"></i> Lý do vắng mặt:
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="attendance[<?= $guest['id'] ?>][report_reason]" 
                                              class="form-control form-control-sm" 
                                              rows="3"
                                              placeholder="Lý do vắng mặt, biện pháp xử lý..."
                                              required></textarea>
                                    <small class="text-muted">Báo cáo này sẽ được gửi cho Admin</small>
                                </div>
                                
                                <div class="present-note text-success" style="display: none;">
                                    <i class="bi bi-check-circle"></i> Khách có mặt
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary status-badge" id="mandatory-status-<?= $guest['id'] ?>">
                                    CHỜ ĐIỂM DANH
                                </span>
                                <input type="hidden" name="attendance[<?= $guest['id'] ?>][status]" 
                                       id="mandatory-status-input-<?= $guest['id'] ?>" value="pending">
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-success" 
                                            onclick="setMandatoryStatus(<?= $guest['id'] ?>, 'present')">
                                        <i class="bi bi-check-lg"></i> Có mặt
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" 
                                            onclick="setMandatoryStatus(<?= $guest['id'] ?>, 'late')">
                                        <i class="bi bi-clock"></i> Trễ
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="setMandatoryStatus(<?= $guest['id'] ?>, 'absent')">
                                        <i class="bi bi-x"></i> Vắng
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Tổng kết và cảnh báo -->
            <div class="alert alert-info mt-4">
                <h6><i class="bi bi-info-circle me-2"></i>Hướng dẫn điểm danh bắt buộc:</h6>
                <ol class="mb-0">
                    <li>Điểm danh <strong>TẤT CẢ</strong> khách trước khi có thể tiếp tục</li>
                    <li>Khách <span class="text-warning">đến trễ</span>: Phải có ghi chú lý do</li>
                    <li>Khách <span class="text-danger">vắng mặt</span>: Phải có báo cáo lý do và chờ duyệt từ Admin</li>
                    <li>Tour sẽ bị <strong>KHÓA</strong> cho đến khi hoàn thành điểm danh</li>
                    <li>Thông tin sẽ được đồng bộ lên server ngay lập tức</li>
                </ol>
            </div>
        </div>
        
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="/?route=guide/tour_stops/list&tour_id=<?= $tourInfo['id'] ?>" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại điểm dừng
                    </a>
                </div>
                <div class="text-end">
                    <div class="mb-2">
                        <span id="completionStatus" class="badge bg-danger">CHƯA HOÀN THÀNH</span>
                        <small class="text-muted ms-2">Tất cả khách phải được điểm danh</small>
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning me-2" onclick="resetMandatoryAll()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Đặt lại tất cả
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i class="bi bi-save me-1"></i>HOÀN THÀNH ĐIỂM DANH
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Biến lưu trạng thái điểm danh bắt buộc
let mandatoryAttendance = {};
let totalGuests = <?= count($guests) ?>;
let completedCount = 0;

// Khởi tạo trạng thái
<?php foreach ($guests as $guest): ?>
mandatoryAttendance[<?= $guest['id'] ?>] = 'pending';
<?php endforeach; ?>

// Cập nhật số lượng thống kê
function updateStatistics() {
    let present = 0, late = 0, absent = 0, pending = 0;
    
    for (const guestId in mandatoryAttendance) {
        switch(mandatoryAttendance[guestId]) {
            case 'present': present++; break;
            case 'late': late++; break;
            case 'absent': absent++; break;
            default: pending++;
        }
    }
    
    // Cập nhật số lượng
    document.getElementById('presentCount').textContent = present;
    document.getElementById('lateCount').textContent = late;
    document.getElementById('absentCount').textContent = absent;
    document.getElementById('pendingCount').textContent = pending;
    
    // Cập nhật số lượng hoàn thành
    completedCount = present + late + absent;
    
    // Kiểm tra xem đã hoàn thành chưa
    const allCompleted = (completedCount === totalGuests);
    const submitBtn = document.getElementById('submitBtn');
    const statusBadge = document.getElementById('completionStatus');
    
    if (allCompleted) {
        submitBtn.disabled = false;
        statusBadge.className = 'badge bg-success';
        statusBadge.textContent = 'ĐÃ HOÀN THÀNH';
        
        // Kiểm tra xem có khách vắng không (cần báo cáo)
        const absentGuests = document.querySelectorAll('textarea[name*="[report_reason]"]');
        let hasEmptyAbsentReport = false;
        
        for (const field of absentGuests) {
            if (field.closest('.absent-report').style.display !== 'none' && !field.value.trim()) {
                hasEmptyAbsentReport = true;
                break;
            }
        }
        
        if (hasEmptyAbsentReport) {
            submitBtn.disabled = true;
            statusBadge.className = 'badge bg-warning';
            statusBadge.textContent = 'THIẾU BÁO CÁO';
        }
    } else {
        submitBtn.disabled = true;
        statusBadge.className = 'badge bg-danger';
        statusBadge.textContent = 'CHƯA HOÀN THÀNH';
    }
}

// Thiết lập trạng thái cho điểm danh bắt buộc
function setMandatoryStatus(guestId, status) {
    const row = document.getElementById(`mandatory-guest-${guestId}`);
    const badge = document.getElementById(`mandatory-status-${guestId}`);
    const statusInput = document.getElementById(`mandatory-status-input-${guestId}`);
    const lateNote = row.querySelector('.late-note');
    const absentReport = row.querySelector('.absent-report');
    const presentNote = row.querySelector('.present-note');
    
    // Cập nhật trạng thái
    mandatoryAttendance[guestId] = status;
    statusInput.value = status;
    
    // Cập nhật badge
    let badgeClass, badgeText;
    switch(status) {
        case 'present':
            badgeClass = 'bg-success';
            badgeText = 'CÓ MẶT';
            break;
        case 'late':
            badgeClass = 'bg-warning';
            badgeText = 'ĐẾN TRỄ';
            break;
        case 'absent':
            badgeClass = 'bg-danger';
            badgeText = 'VẮNG MẶT';
            break;
        default:
            badgeClass = 'bg-secondary';
            badgeText = 'CHỜ ĐIỂM DANH';
    }
    
    badge.className = `badge ${badgeClass} status-badge`;
    badge.textContent = badgeText;
    
    // Hiển thị/ẩn các trường nhập
    if (status === 'late') {
        lateNote.style.display = 'block';
        absentReport.style.display = 'none';
        presentNote.style.display = 'none';
    } else if (status === 'absent') {
        lateNote.style.display = 'none';
        absentReport.style.display = 'block';
        presentNote.style.display = 'none';
        
        // Đánh dấu bắt buộc nhập lý do
        const reportField = absentReport.querySelector('textarea');
        reportField.required = true;
    } else if (status === 'present') {
        lateNote.style.display = 'none';
        absentReport.style.display = 'none';
        presentNote.style.display = 'block';
    } else {
        lateNote.style.display = 'none';
        absentReport.style.display = 'none';
        presentNote.style.display = 'none';
    }
    
    // Cập nhật thống kê
    updateStatistics();
    
    // Hiển thị thông báo
    const guestName = row.querySelector('strong').textContent;
    showMandatoryToast(`${guestName}: ${badgeText}`, 
                      status === 'present' ? 'success' : 
                      (status === 'late' ? 'warning' : 'danger'));
}

// Reset tất cả
function resetMandatoryAll() {
    if (confirm('Đặt lại TẤT CẢ điểm danh? Thao tác này sẽ xóa tất cả trạng thái hiện tại.')) {
        <?php foreach ($guests as $guest): ?>
        setMandatoryStatus(<?= $guest['id'] ?>, 'pending');
        <?php endforeach; ?>
        showMandatoryToast('Đã đặt lại tất cả điểm danh', 'info');
    }
}

// Xử lý form submit
document.getElementById('mandatoryAttendanceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Kiểm tra tất cả đã điểm danh chưa
    if (completedCount !== totalGuests) {
        alert('LỖI: Chưa điểm danh tất cả khách. Vui lòng điểm danh tất cả khách trước khi tiếp tục.');
        return;
    }
    
    // Kiểm tra khách vắng có báo cáo không
    const absentReports = document.querySelectorAll('textarea[name*="[report_reason]"]');
    let missingReports = [];
    
    absentReports.forEach((field, index) => {
        if (field.closest('.absent-report').style.display !== 'none') {
            if (!field.value.trim()) {
                const guestId = field.name.match(/\[(\d+)\]/)[1];
                missingReports.push(guestId);
            }
        }
    });
    
    if (missingReports.length > 0) {
        alert('LỖI: Khách vắng mặt phải có lý do báo cáo. Vui lòng nhập lý do cho tất cả khách vắng.');
        
        // Highlight các trường bị thiếu
        missingReports.forEach(guestId => {
            const row = document.getElementById(`mandatory-guest-${guestId}`);
            row.classList.add('table-danger');
            setTimeout(() => row.classList.remove('table-danger'), 3000);
        });
        
        return;
    }
    
    // Hiển thị xác nhận cuối cùng
    if (confirm('XÁC NHẬN HOÀN THÀNH ĐIỂM DANH BẮT BUỘC\n\n' +
                'Sau khi xác nhận:\n' +
                '1. Điểm dừng sẽ được đánh dấu hoàn thành\n' +
                '2. Tour sẽ được mở khóa để tiếp tục\n' +
                '3. Báo cáo sẽ được gửi cho Admin (nếu có khách vắng)\n' +
                '4. Không thể chỉnh sửa điểm danh sau khi xác nhận\n\n' +
                'Bạn có chắc chắn?')) {
        
        // Hiển thị loading
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>ĐANG XỬ LÝ...';
        
        // Submit form
        this.submit();
    }
});

// Hiển thị thông báo
function showMandatoryToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
    toast.style.zIndex = 1050;
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi ${type === 'success' ? 'bi-check-circle' : 
                               type === 'warning' ? 'bi-exclamation-triangle' : 
                               type === 'danger' ? 'bi-x-circle' : 'bi-info-circle'} me-2"></i>
                ${message}
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

// Khởi tạo thống kê
updateStatistics();
</script>

<style>
.status-badge {
    min-width: 120px;
    padding: 8px 12px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr.table-info {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.table tbody tr.table-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { background-color: rgba(220, 53, 69, 0.1); }
    50% { background-color: rgba(220, 53, 69, 0.3); }
    100% { background-color: rgba(220, 53, 69, 0.1); }
}

#submitBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>