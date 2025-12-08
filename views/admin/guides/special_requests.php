<?php
$page_title = "Yêu cầu đặc biệt";
$breadcrumb = [
    ['title' => 'Chi tiết Tour', 'link' => 'tour_detail.php'],
    ['title' => 'Yêu cầu đặc biệt', 'active' => true]
];
require_once __DIR__ . '/header.php';

// Giả lập dữ liệu yêu cầu đặc biệt
$special_requests = [
    [
        'id' => 1,
        'participant_id' => 1,
        'participant_name' => 'Nguyễn Văn An',
        'group' => 'Gia đình A',
        'request_type' => 'dietary',
        'request_category' => 'ăn chay',
        'details' => 'Ăn chay trường, không ăn mặn',
        'priority' => 'high',
        'status' => 'active',
        'notes' => 'Đã thông báo cho nhà hàng',
        'created_at' => '2024-02-19',
        'updated_at' => '2024-02-19'
    ],
    [
        'id' => 2,
        'participant_id' => 2,
        'participant_name' => 'Trần Thị Bình',
        'group' => 'Gia đình A',
        'request_type' => 'medical',
        'request_category' => 'dị ứng',
        'details' => 'Dị ứng hải sản nặng, không được ăn bất kỳ món nào có hải sản',
        'priority' => 'high',
        'status' => 'active',
        'notes' => 'Mang theo thuốc dị ứng, thông báo tất cả nhà hàng',
        'created_at' => '2024-02-19',
        'updated_at' => '2024-02-19'
    ],
    [
        'id' => 3,
        'participant_id' => 3,
        'participant_name' => 'Lê Minh Cường',
        'group' => 'Nhóm bạn',
        'request_type' => 'dietary',
        'request_category' => 'kiêng thịt bò',
        'details' => 'Không ăn thịt bò vì lý do tôn giáo',
        'priority' => 'medium',
        'status' => 'active',
        'notes' => 'Đã ghi nhận',
        'created_at' => '2024-02-19',
        'updated_at' => '2024-02-19'
    ],
    [
        'id' => 4,
        'participant_id' => 4,
        'participant_name' => 'Phạm Thị Dung',
        'group' => 'Nhóm bạn',
        'request_type' => 'medical',
        'request_category' => 'tiểu đường',
        'details' => 'Tiểu đường type 2, cần ăn đúng giờ, hạn chế đường',
        'priority' => 'high',
        'status' => 'active',
        'notes' => 'Mang theo insulin, nhắc ăn đúng giờ',
        'created_at' => '2024-02-19',
        'updated_at' => '2024-02-19'
    ],
    [
        'id' => 5,
        'participant_id' => 5,
        'participant_name' => 'John Smith',
        'group' => 'Khách lẻ',
        'request_type' => 'dietary',
        'request_category' => 'vegetarian',
        'details' => 'Vegetarian (ovo-lacto), no meat, fish, but eggs and dairy are OK',
        'priority' => 'medium',
        'status' => 'active',
        'notes' => 'English speaker, need English menu',
        'created_at' => '2024-02-19',
        'updated_at' => '2024-02-19'
    ],
    [
        'id' => 6,
        'participant_id' => 1,
        'participant_name' => 'Nguyễn Văn An',
        'group' => 'Gia đình A',
        'request_type' => 'other',
        'request_category' => 'phòng ở',
        'details' => 'Yêu cầu phòng tầng thấp (dưới tầng 3) vì sợ độ cao',
        'priority' => 'low',
        'status' => 'completed',
        'notes' => 'Đã bố trí phòng 201 tầng 2',
        'created_at' => '2024-02-18',
        'updated_at' => '2024-02-19'
    ]
];

$request_types = [
    'dietary' => ['icon' => 'bi-egg-fried', 'color' => 'primary', 'label' => 'Ẩm thực'],
    'medical' => ['icon' => 'bi-heart-pulse', 'color' => 'danger', 'label' => 'Y tế'],
    'accommodation' => ['icon' => 'bi-house', 'color' => 'info', 'label' => 'Chỗ ở'],
    'transport' => ['icon' => 'bi-bus-front', 'color' => 'warning', 'label' => 'Di chuyển'],
    'other' => ['icon' => 'bi-three-dots', 'color' => 'secondary', 'label' => 'Khác']
];

$priorities = [
    'high' => ['color' => 'danger', 'label' => 'Cao'],
    'medium' => ['color' => 'warning', 'label' => 'Trung bình'],
    'low' => ['color' => 'info', 'label' => 'Thấp']
];

$statuses = [
    'active' => ['color' => 'primary', 'label' => 'Đang xử lý'],
    'completed' => ['color' => 'success', 'label' => 'Đã hoàn thành'],
    'cancelled' => ['color' => 'secondary', 'label' => 'Đã hủy']
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card guide-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4>Yêu cầu đặc biệt</h4>
                        <p class="text-muted mb-2">Quản lý các yêu cầu đặc biệt của khách hàng</p>
                    </div>
                    <div class="text-end">
                        <div class="display-6"><?php echo count($special_requests); ?></div>
                        <small class="text-muted">Tổng yêu cầu</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card guide-card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="display-6 text-danger"><?php echo count(array_filter($special_requests, fn($r) => $r['priority'] === 'high')); ?></div>
                        <small>Ưu tiên cao</small>
                    </div>
                    <div class="col-4">
                        <div class="display-6 text-primary"><?php echo count(array_filter($special_requests, fn($r) => $r['request_type'] === 'medical')); ?></div>
                        <small>Y tế</small>
                    </div>
                    <div class="col-4">
                        <div class="display-6 text-warning"><?php echo count(array_filter($special_requests, fn($r) => $r['request_type'] === 'dietary')); ?></div>
                        <small>Ẩm thực</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <?php foreach ($request_types as $type => $config): 
        $count = count(array_filter($special_requests, fn($r) => $r['request_type'] === $type));
        if ($count > 0):
    ?>
    <div class="col-md-2 col-6 mb-3">
        <div class="card border-<?php echo $config['color']; ?>">
            <div class="card-body text-center">
                <i class="bi <?php echo $config['icon']; ?> fs-1 text-<?php echo $config['color']; ?>"></i>
                <div class="mt-2">
                    <h5 class="mb-0"><?php echo $count; ?></h5>
                    <small><?php echo $config['label']; ?></small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; endforeach; ?>
</div>

<!-- Filter and Search -->
<div class="card guide-card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select" id="filterType">
                    <option value="">Tất cả loại</option>
                    <?php foreach ($request_types as $type => $config): ?>
                    <option value="<?php echo $type; ?>"><?php echo $config['label']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterPriority">
                    <option value="">Tất cả ưu tiên</option>
                    <?php foreach ($priorities as $priority => $config): ?>
                    <option value="<?php echo $priority; ?>"><?php echo $config['label']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterStatus">
                    <option value="">Tất cả trạng thái</option>
                    <?php foreach ($statuses as $status => $config): ?>
                    <option value="<?php echo $status; ?>"><?php echo $config['label']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Tìm theo tên khách..." id="searchRequest">
            </div>
        </div>
    </div>
</div>

<!-- Special Requests List -->
<div class="card guide-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Danh sách yêu cầu</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRequestModal">
            <i class="bi bi-plus-circle me-1"></i>Thêm yêu cầu
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Khách hàng</th>
                        <th>Loại yêu cầu</th>
                        <th>Chi tiết</th>
                        <th width="100">Ưu tiên</th>
                        <th width="120">Trạng thái</th>
                        <th width="120">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($special_requests as $request): 
                        $type_config = $request_types[$request['request_type']] ?? $request_types['other'];
                        $priority_config = $priorities[$request['priority']];
                        $status_config = $statuses[$request['status']];
                    ?>
                    <tr class="request-row" 
                        data-type="<?php echo $request['request_type']; ?>"
                        data-priority="<?php echo $request['priority']; ?>"
                        data-status="<?php echo $request['status']; ?>"
                        data-name="<?php echo strtolower($request['participant_name']); ?>">
                        <td><?php echo $request['id']; ?></td>
                        <td>
                            <div>
                                <strong><?php echo $request['participant_name']; ?></strong>
                                <br>
                                <small class="text-muted"><?php echo $request['group']; ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $type_config['color']; ?>">
                                <i class="bi <?php echo $type_config['icon']; ?> me-1"></i>
                                <?php echo $type_config['label']; ?>
                            </span>
                            <br>
                            <small class="text-muted"><?php echo $request['request_category']; ?></small>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 250px;" 
                                 data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($request['details']); ?>">
                                <?php echo htmlspecialchars($request['details']); ?>
                            </div>
                            <?php if (!empty($request['notes'])): ?>
                            <small class="text-info">
                                <i class="bi bi-info-circle"></i> <?php echo htmlspecialchars($request['notes']); ?>
                            </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $priority_config['color']; ?>">
                                <?php echo $priority_config['label']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $status_config['color']; ?>">
                                <?php echo $status_config['label']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" 
                                        onclick="viewRequest(<?php echo htmlspecialchars(json_encode($request)); ?>)"
                                        data-bs-toggle="tooltip" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" 
                                        onclick="editRequest(<?php echo $request['id']; ?>)"
                                        data-bs-toggle="tooltip" title="Sửa yêu cầu">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-success" 
                                        onclick="completeRequest(<?php echo $request['id']; ?>)"
                                        data-bs-toggle="tooltip" title="Đánh dấu hoàn thành">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Medical Alerts Section -->
<?php 
$medical_requests = array_filter($special_requests, fn($r) => $r['request_type'] === 'medical' && $r['status'] === 'active');
if (count($medical_requests) > 0):
?>
<div class="card border-danger mt-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Cảnh báo y tế quan trọng</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($medical_requests as $request): ?>
            <div class="col-md-6 mb-3">
                <div class="card border border-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-danger"><?php echo $request['participant_name']; ?></h6>
                                <p class="mb-1"><?php echo $request['request_category']; ?></p>
                                <small><?php echo $request['details']; ?></small>
                            </div>
                            <span class="badge bg-danger">CẦN CHÚ Ý</span>
                        </div>
                        <?php if (!empty($request['notes'])): ?>
                        <div class="alert alert-warning mt-2 mb-0 py-2">
                            <i class="bi bi-info-circle"></i> <?php echo htmlspecialchars($request['notes']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

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
                <button class="btn btn-success" onclick="printMedicalAlerts()">
                    <i class="bi bi-printer me-1"></i>In cảnh báo y tế
                </button>
                <button class="btn btn-primary" onclick="exportRequests()">
                    <i class="bi bi-download me-1"></i>Xuất danh sách
                </button>
                <button class="btn btn-info" onclick="sendReminders()">
                    <i class="bi bi-bell me-1"></i>Gửi nhắc nhở
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="viewRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết yêu cầu đặc biệt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="requestDetailContent">
                <!-- Content will be filled by JavaScript -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm yêu cầu đặc biệt mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addRequestForm">
                    <div class="mb-3">
                        <label class="form-label">Khách hàng *</label>
                        <select class="form-select" required>
                            <option value="">Chọn khách hàng</option>
                            <option value="1">Nguyễn Văn An (Gia đình A)</option>
                            <option value="2">Trần Thị Bình (Gia đình A)</option>
                            <option value="3">Lê Minh Cường (Nhóm bạn)</option>
                            <option value="4">Phạm Thị Dung (Nhóm bạn)</option>
                            <option value="5">John Smith (Khách lẻ)</option>
                        </select>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Loại yêu cầu *</label>
                            <select class="form-select" id="requestType" required>
                                <option value="">Chọn loại</option>
                                <?php foreach ($request_types as $type => $config): ?>
                                <option value="<?php echo $type; ?>"><?php echo $config['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Danh mục *</label>
                            <input type="text" class="form-control" id="requestCategory" 
                                   placeholder="VD: Ăn chay, Dị ứng..." required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Chi tiết yêu cầu *</label>
                        <textarea class="form-control" rows="3" required 
                                  placeholder="Mô tả chi tiết yêu cầu..."></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mức độ ưu tiên</label>
                            <select class="form-select">
                                <?php foreach ($priorities as $priority => $config): ?>
                                <option value="<?php echo $priority; ?>"><?php echo $config['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select">
                                <?php foreach ($statuses as $status => $config): ?>
                                <option value="<?php echo $status; ?>"><?php echo $config['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú xử lý</label>
                        <textarea class="form-control" rows="2" 
                                  placeholder="Ghi chú về cách xử lý, nhắc nhở..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveNewRequest()">Lưu yêu cầu</button>
            </div>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.getElementById('filterType').addEventListener('change', filterRequests);
document.getElementById('filterPriority').addEventListener('change', filterRequests);
document.getElementById('filterStatus').addEventListener('change', filterRequests);
document.getElementById('searchRequest').addEventListener('input', filterRequests);

function filterRequests() {
    const type = document.getElementById('filterType').value;
    const priority = document.getElementById('filterPriority').value;
    const status = document.getElementById('filterStatus').value;
    const search = document.getElementById('searchRequest').value.toLowerCase();
    
    document.querySelectorAll('.request-row').forEach(row => {
        const rowType = row.dataset.type;
        const rowPriority = row.dataset.priority;
        const rowStatus = row.dataset.status;
        const rowName = row.dataset.name;
        
        const matchType = !type || rowType === type;
        const matchPriority = !priority || rowPriority === priority;
        const matchStatus = !status || rowStatus === status;
        const matchSearch = !search || rowName.includes(search);
        
        row.style.display = (matchType && matchPriority && matchStatus && matchSearch) ? '' : 'none';
    });
}

function viewRequest(request) {
    const content = document.getElementById('requestDetailContent');
    const typeConfig = <?php echo json_encode($request_types); ?>[request.request_type] || <?php echo json_encode($request_types['other']); ?>;
    const priorityConfig = <?php echo json_encode($priorities); ?>[request.priority];
    const statusConfig = <?php echo json_encode($statuses); ?>[request.status];
    
    let html = `
        <div class="row">
            <div class="col-md-8">
                <table class="table table-sm">
                    <tr>
                        <td width="150"><strong>Khách hàng:</strong></td>
                        <td>${request.participant_name}</td>
                    </tr>
                    <tr>
                        <td><strong>Nhóm:</strong></td>
                        <td>${request.group}</td>
                    </tr>
                    <tr>
                        <td><strong>Loại yêu cầu:</strong></td>
                        <td>
                            <span class="badge bg-${typeConfig.color}">
                                <i class="bi ${typeConfig.icon} me-1"></i>${typeConfig.label}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Danh mục:</strong></td>
                        <td>${request.request_category}</td>
                    </tr>
                    <tr>
                        <td><strong>Chi tiết:</strong></td>
                        <td>${request.details}</td>
                    </tr>
                    <tr>
                        <td><strong>Ghi chú:</strong></td>
                        <td>${request.notes || 'Không có'}</td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-4">
                <div class="card border">
                    <div class="card-body">
                        <h6>Thông tin xử lý</h6>
                        <div class="mb-3">
                            <label class="form-label">Mức độ ưu tiên</label>
                            <div>
                                <span class="badge bg-${priorityConfig.color}">${priorityConfig.label}</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <div>
                                <span class="badge bg-${statusConfig.color}">${statusConfig.label}</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ngày tạo</label>
                            <div>${formatDate(request.created_at)}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cập nhật cuối</label>
                            <div>${formatDate(request.updated_at)}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <button class="btn btn-warning me-2" onclick="editRequest(${request.id})">
                <i class="bi bi-pencil me-1"></i>Sửa yêu cầu
            </button>
            <button class="btn btn-success" onclick="completeRequest(${request.id})">
                <i class="bi bi-check-circle me-1"></i>Đánh dấu hoàn thành
            </button>
        </div>
    `;
    
    content.innerHTML = html;
    $('#viewRequestModal').modal('show');
}

function editRequest(requestId) {
    // In real app, load request data and show edit form
    alert(`Sửa yêu cầu #${requestId}\n(Chức năng này sẽ tải dữ liệu và hiển thị form chỉnh sửa)`);
}

function completeRequest(requestId) {
    if (confirm(`Đánh dấu yêu cầu #${requestId} là đã hoàn thành?`)) {
        // Update UI
        const row = document.querySelector(`[onclick="completeRequest(${requestId})"]`).closest('tr');
        const statusBadge = row.querySelector('td:nth-child(6) .badge');
        statusBadge.className = 'badge bg-success';
        statusBadge.textContent = 'Đã hoàn thành';
        row.dataset.status = 'completed';
        
        alert('Đã đánh dấu hoàn thành');
        
        // In real app, send to server
        // fetch(`/api/requests/${requestId}/complete`, { method: 'POST' })
    }
}

function saveNewRequest() {
    const form = document.getElementById('addRequestForm');
    if (form.checkValidity()) {
        alert('Đã thêm yêu cầu mới');
        $('#addRequestModal').modal('hide');
        form.reset();
        
        // In real app, refresh list
        // location.reload();
    } else {
        form.reportValidity();
    }
}

function printMedicalAlerts() {
    const medicalRequests = <?php echo json_encode($medical_requests); ?>;
    
    if (medicalRequests.length === 0) {
        alert('Không có cảnh báo y tế nào');
        return;
    }
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Cảnh báo y tế - Tour <?php echo $tour_info['tour_code'] ?? ''; ?></title>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { text-align: center; margin-bottom: 30px; }
                .alert-card { border: 2px solid #dc3545; padding: 15px; margin-bottom: 15px; }
                .alert-title { color: #dc3545; font-weight: bold; }
                .timestamp { text-align: right; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>CẢNH BÁO Y TẾ</h2>
                <h4>Tour <?php echo $tour_info['tour_name'] ?? ''; ?> (<?php echo $tour_info['tour_code'] ?? ''; ?>)</h4>
                <p>Ngày: ${new Date().toLocaleDateString('vi-VN')}</p>
                <p>HDV: <?php echo $_SESSION['guide_name'] ?? 'Hướng dẫn viên'; ?></p>
            </div>
            
            <div class="timestamp">In lúc: ${new Date().toLocaleTimeString('vi-VN')}</div>
            
            ${medicalRequests.map((req, index) => `
                <div class="alert-card">
                    <div class="alert-title">CẢNH BÁO ${index + 1}: ${req.participant_name}</div>
                    <p><strong>Danh mục:</strong> ${req.request_category}</p>
                    <p><strong>Chi tiết:</strong> ${req.details}</p>
                    <p><strong>Ghi chú xử lý:</strong> ${req.notes || 'Không có'}</p>
                    <p><strong>Mức độ ưu tiên:</strong> <span style="color: #dc3545; font-weight: bold;">CAO</span></p>
                </div>
            `).join('')}
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                <p><strong>Lưu ý quan trọng:</strong></p>
                <ul>
                    <li>Luôn mang theo danh sách cảnh báo y tế này</li>
                    <li>Thông báo cho nhà hàng, khách sạn về các yêu cầu đặc biệt</li>
                    <li>Liên hệ cấp cứu: 115</li>
                    <li>Liên hệ công ty: 0243 123 4567 (24/7)</li>
                </ul>
            </div>
        </body>
        </html>
    `;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

function exportRequests() {
    const format = prompt('Chọn định dạng xuất (pdf/excel):', 'pdf');
    if (format) {
        const fileName = `yeu_cau_dac_biet_<?php echo date('Y-m-d'); ?>.${format}`;
        alert(`Đang xuất file: ${fileName}`);
        // In real app, generate and download file
    }
}

function sendReminders() {
    if (confirm('Gửi nhắc nhở về các yêu cầu đặc biệt cho đội ngũ hỗ trợ?')) {
        alert('Đã gửi nhắc nhở');
        // In real app, send notifications/emails
    }
}

// Helper function
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('vi-VN');
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>