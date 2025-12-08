<?php
$page_title = "Danh sách khách hàng";
$breadcrumb = [
    ['title' => 'Chi tiết Tour', 'link' => 'tour_detail.php'],
    ['title' => 'Danh sách khách', 'active' => true]
];
require_once __DIR__ . '/header.php';

// Giả lập dữ liệu khách hàng
$tour_info = [
    'tour_id' => 1,
    'tour_name' => 'Tour Sapa 3 ngày 2 đêm',
    'tour_code' => 'T001',
    'departure_date' => date('Y-m-d'),
    'total_participants' => 18,
    'checked_in' => 15
];

$participants = [
    [
        'id' => 1,
        'full_name' => 'Nguyễn Văn An',
        'phone' => '0912345678',
        'email' => 'an.nguyen@example.com',
        'birth_date' => '1990-05-15',
        'gender' => 'male',
        'nationality' => 'Vietnamese',
        'group' => 'Gia đình A',
        'special_requests' => 'Ăn chay',
        'medical_notes' => 'Dị ứng hải sản',
        'attendance_status' => 'present',
        'emergency_contact' => '0987654321 (Vợ)',
        'room_assignment' => 'Phòng 201'
    ],
    [
        'id' => 2,
        'full_name' => 'Trần Thị Bình',
        'phone' => '0923456789',
        'email' => 'binh.tran@example.com',
        'birth_date' => '1985-08-20',
        'gender' => 'female',
        'nationality' => 'Vietnamese',
        'group' => 'Gia đình A',
        'special_requests' => 'Không có',
        'medical_notes' => 'Huyết áp cao',
        'attendance_status' => 'present',
        'emergency_contact' => '0976543210 (Chồng)',
        'room_assignment' => 'Phòng 201'
    ],
    [
        'id' => 3,
        'full_name' => 'Lê Minh Cường',
        'phone' => '0934567890',
        'email' => 'cuong.le@example.com',
        'birth_date' => '1995-03-10',
        'gender' => 'male',
        'nationality' => 'Vietnamese',
        'group' => 'Nhóm bạn',
        'special_requests' => 'Không ăn thịt bò',
        'medical_notes' => 'Không',
        'attendance_status' => 'absent',
        'emergency_contact' => '0965432109 (Mẹ)',
        'room_assignment' => 'Phòng 205'
    ],
    [
        'id' => 4,
        'full_name' => 'Phạm Thị Dung',
        'phone' => '0945678901',
        'email' => 'dung.pham@example.com',
        'birth_date' => '2000-11-25',
        'gender' => 'female',
        'nationality' => 'Vietnamese',
        'group' => 'Nhóm bạn',
        'special_requests' => 'Chế độ ăn Keto',
        'medical_notes' => 'Tiểu đường type 2',
        'attendance_status' => 'late',
        'emergency_contact' => '0954321098 (Anh trai)',
        'room_assignment' => 'Phòng 205'
    ],
    [
        'id' => 5,
        'full_name' => 'John Smith',
        'phone' => '+1 234 567 8900',
        'email' => 'john.smith@example.com',
        'birth_date' => '1988-07-30',
        'gender' => 'male',
        'nationality' => 'American',
        'group' => 'Khách lẻ',
        'special_requests' => 'Vegetarian',
        'medical_notes' => 'Peanut allergy',
        'attendance_status' => 'present',
        'emergency_contact' => '+1 987 654 3210',
        'room_assignment' => 'Phòng 301'
    ]
];

$groups = [];
foreach ($participants as $participant) {
    $group = $participant['group'];
    if (!isset($groups[$group])) {
        $groups[$group] = [];
    }
    $groups[$group][] = $participant;
}
?>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card guide-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4><?php echo $tour_info['tour_name']; ?></h4>
                        <p class="text-muted mb-2">Mã tour: <?php echo $tour_info['tour_code']; ?> • 
                           Ngày khởi hành: <?php echo date('d/m/Y', strtotime($tour_info['departure_date'])); ?></p>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-0"><?php echo $tour_info['checked_in']; ?>/<?php echo $tour_info['total_participants']; ?></h5>
                        <small class="text-muted">Đã điểm danh</small>
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
                        <div class="display-6 text-success"><?php echo count(array_filter($participants, fn($p) => $p['attendance_status'] === 'present')); ?></div>
                        <small>Có mặt</small>
                    </div>
                    <div class="col-4">
                        <div class="display-6 text-danger"><?php echo count(array_filter($participants, fn($p) => $p['attendance_status'] === 'absent')); ?></div>
                        <small>Vắng</small>
                    </div>
                    <div class="col-4">
                        <div class="display-6 text-warning"><?php echo count(array_filter($participants, fn($p) => $p['attendance_status'] === 'late')); ?></div>
                        <small>Đến muộn</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card guide-card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Tìm theo tên, điện thoại..." id="searchParticipants">
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterGroup">
                    <option value="">Tất cả nhóm</option>
                    <?php foreach (array_keys($groups) as $group): ?>
                    <option value="<?php echo htmlspecialchars($group); ?>"><?php echo htmlspecialchars($group); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filterStatus">
                    <option value="">Tất cả trạng thái</option>
                    <option value="present">Có mặt</option>
                    <option value="absent">Vắng</option>
                    <option value="late">Đến muộn</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" onclick="exportParticipants()">
                    <i class="bi bi-download me-1"></i>Xuất file
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Participants by Groups -->
<?php foreach ($groups as $group_name => $group_participants): ?>
<div class="card guide-card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i><?php echo htmlspecialchars($group_name); ?></h5>
        <span class="badge bg-primary"><?php echo count($group_participants); ?> thành viên</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">STT</th>
                        <th>Thông tin khách</th>
                        <th width="120">Liên hệ</th>
                        <th width="150">Yêu cầu đặc biệt</th>
                        <th width="120">Phòng</th>
                        <th width="100">Trạng thái</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($group_participants as $index => $participant): 
                        $status_config = [
                            'present' => ['class' => 'bg-success', 'text' => 'Có mặt', 'icon' => 'bi-check-circle'],
                            'absent' => ['class' => 'bg-danger', 'text' => 'Vắng', 'icon' => 'bi-x-circle'],
                            'late' => ['class' => 'bg-warning', 'text' => 'Đến muộn', 'icon' => 'bi-clock']
                        ];
                        $status = $status_config[$participant['attendance_status']];
                    ?>
                    <tr class="participant-row" 
                        data-name="<?php echo strtolower($participant['full_name']); ?>"
                        data-group="<?php echo htmlspecialchars($group_name); ?>"
                        data-status="<?php echo $participant['attendance_status']; ?>">
                        <td><?php echo $index + 1; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-person fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($participant['full_name']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo $participant['gender'] == 'male' ? 'Nam' : 'Nữ'; ?> • 
                                        <?php echo date('d/m/Y', strtotime($participant['birth_date'])); ?> • 
                                        <?php echo htmlspecialchars($participant['nationality']); ?>
                                        <?php if (!empty($participant['medical_notes'])): ?>
                                        <br><i class="bi bi-heart-pulse text-danger"></i> <?php echo htmlspecialchars($participant['medical_notes']); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($participant['phone']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($participant['email']); ?></small>
                                <?php if (!empty($participant['emergency_contact'])): ?>
                                <div class="mt-1">
                                    <small><i class="bi bi-telephone-plus text-danger"></i> <?php echo htmlspecialchars($participant['emergency_contact']); ?></small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($participant['special_requests'])): ?>
                            <span class="badge bg-info" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($participant['special_requests']); ?>">
                                <i class="bi bi-heart"></i> Có yêu cầu
                            </span>
                            <?php else: ?>
                            <span class="text-muted">Không có</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($participant['room_assignment']); ?></span>
                        </td>
                        <td>
                            <span class="badge <?php echo $status['class']; ?>">
                                <i class="<?php echo $status['icon']; ?> me-1"></i><?php echo $status['text']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" 
                                        onclick="showParticipantDetail(<?php echo htmlspecialchars(json_encode($participant)); ?>)"
                                        data-bs-toggle="tooltip" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-success" 
                                        onclick="quickCheckIn(<?php echo $participant['id']; ?>)"
                                        data-bs-toggle="tooltip" title="Check-in nhanh">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-outline-warning" 
                                        onclick="editSpecialRequest(<?php echo $participant['id']; ?>)"
                                        data-bs-toggle="tooltip" title="Sửa yêu cầu">
                                    <i class="bi bi-pencil"></i>
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
<?php endforeach; ?>

<!-- Summary Statistics -->
<div class="row">
    <div class="col-md-6">
        <div class="card guide-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Thống kê nhóm</h5>
            </div>
            <div class="card-body">
                <canvas id="groupChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card guide-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Yêu cầu đặc biệt</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php 
                    $special_requests = [];
                    foreach ($participants as $p) {
                        if (!empty($p['special_requests'])) {
                            $special_requests[] = $p;
                        }
                    }
                    ?>
                    <?php if (count($special_requests) > 0): ?>
                        <?php foreach ($special_requests as $req): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($req['full_name']); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($req['group']); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($req['special_requests']); ?></p>
                            <?php if (!empty($req['medical_notes'])): ?>
                            <small class="text-danger"><i class="bi bi-heart-pulse"></i> <?php echo htmlspecialchars($req['medical_notes']); ?></small>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center my-3">Không có yêu cầu đặc biệt</p>
                    <?php endif; ?>
                </div>
                <button class="btn btn-outline-primary w-100 mt-3" onclick="addSpecialRequest()">
                    <i class="bi bi-plus-circle me-1"></i>Thêm yêu cầu mới
                </button>
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
                <button class="btn btn-success" onclick="bulkCheckIn()">
                    <i class="bi bi-clipboard-check me-1"></i>Điểm danh tất cả
                </button>
                <button class="btn btn-primary" onclick="printParticipantList()">
                    <i class="bi bi-printer me-1"></i>In danh sách
                </button>
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                    <i class="bi bi-person-plus me-1"></i>Thêm khách
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="participantDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="participantDetailContent">
                <!-- Content will be filled by JavaScript -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addParticipantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm khách hàng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addParticipantForm">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên *</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại *</label>
                            <input type="tel" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nhóm</label>
                            <select class="form-select">
                                <option value="">Chọn nhóm</option>
                                <?php foreach (array_keys($groups) as $group): ?>
                                <option value="<?php echo htmlspecialchars($group); ?>"><?php echo htmlspecialchars($group); ?></option>
                                <?php endforeach; ?>
                                <option value="new">Tạo nhóm mới</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phòng</label>
                            <input type="text" class="form-control" placeholder="VD: Phòng 201">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yêu cầu đặc biệt / Ghi chú sức khỏe</label>
                        <textarea class="form-control" rows="3" placeholder="Ăn chay, dị ứng, bệnh lý..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveNewParticipant()">Thêm khách</button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let allParticipants = <?php echo json_encode($participants); ?>;

// Search and filter functionality
document.getElementById('searchParticipants').addEventListener('input', filterParticipants);
document.getElementById('filterGroup').addEventListener('change', filterParticipants);
document.getElementById('filterStatus').addEventListener('change', filterParticipants);

function filterParticipants() {
    const search = document.getElementById('searchParticipants').value.toLowerCase();
    const group = document.getElementById('filterGroup').value;
    const status = document.getElementById('filterStatus').value;
    
    document.querySelectorAll('.participant-row').forEach(row => {
        const name = row.dataset.name;
        const rowGroup = row.dataset.group;
        const rowStatus = row.dataset.status;
        
        const matchSearch = name.includes(search) || search === '';
        const matchGroup = group === '' || rowGroup === group;
        const matchStatus = status === '' || rowStatus === status;
        
        row.style.display = (matchSearch && matchGroup && matchStatus) ? '' : 'none';
    });
}

function showParticipantDetail(participant) {
    const content = document.getElementById('participantDetailContent');
    const age = calculateAge(participant.birth_date);
    
    let html = `
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                     style="width: 100px; height: 100px;">
                    <i class="bi bi-person fs-1"></i>
                </div>
                <h5 class="mt-3">${participant.full_name}</h5>
                <p class="text-muted">${participant.group}</p>
            </div>
            <div class="col-md-8">
                <table class="table table-sm">
                    <tr>
                        <td width="150"><strong>Giới tính:</strong></td>
                        <td>${participant.gender === 'male' ? 'Nam' : 'Nữ'}</td>
                    </tr>
                    <tr>
                        <td><strong>Ngày sinh:</strong></td>
                        <td>${formatDate(participant.birth_date)} (${age} tuổi)</td>
                    </tr>
                    <tr>
                        <td><strong>Quốc tịch:</strong></td>
                        <td>${participant.nationality}</td>
                    </tr>
                    <tr>
                        <td><strong>Số điện thoại:</strong></td>
                        <td>${participant.phone}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>${participant.email}</td>
                    </tr>
                    <tr>
                        <td><strong>Liên hệ khẩn cấp:</strong></td>
                        <td>${participant.emergency_contact}</td>
                    </tr>
                    <tr>
                        <td><strong>Phòng:</strong></td>
                        <td><span class="badge bg-secondary">${participant.room_assignment}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Yêu cầu đặc biệt:</strong></td>
                        <td>${participant.special_requests || 'Không có'}</td>
                    </tr>
                    <tr>
                        <td><strong>Ghi chú sức khỏe:</strong></td>
                        <td>${participant.medical_notes || 'Không có'}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-warning me-2" onclick="editParticipant(${participant.id})">
                <i class="bi bi-pencil me-1"></i>Sửa thông tin
            </button>
            <button class="btn btn-info" onclick="sendMessage('${participant.phone}')">
                <i class="bi bi-chat me-1"></i>Nhắn tin
            </button>
        </div>
    `;
    
    content.innerHTML = html;
    $('#participantDetailModal').modal('show');
}

function quickCheckIn(participantId) {
    if (confirm('Xác nhận check-in cho khách này?')) {
        // Update status in UI
        const row = document.querySelector(`[onclick="quickCheckIn(${participantId})"]`).closest('tr');
        const statusBadge = row.querySelector('.badge');
        statusBadge.className = 'badge bg-success';
        statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Có mặt';
        row.dataset.status = 'present';
        
        alert('Đã check-in thành công');
        
        // In real app, send to server
        // fetch('/api/checkin', { method: 'POST', body: JSON.stringify({ participantId }) })
    }
}

function editSpecialRequest(participantId) {
    const participant = allParticipants.find(p => p.id === participantId);
    if (participant) {
        const request = prompt('Nhập yêu cầu đặc biệt mới:', participant.special_requests);
        if (request !== null) {
            // Update in UI
            const row = document.querySelector(`[onclick="editSpecialRequest(${participantId})"]`).closest('tr');
            const badge = row.querySelector('.bg-info');
            if (request.trim()) {
                if (!badge) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'badge bg-info';
                    newBadge.innerHTML = '<i class="bi bi-heart"></i> Có yêu cầu';
                    newBadge.title = request;
                    newBadge.setAttribute('data-bs-toggle', 'tooltip');
                    row.querySelector('td:nth-child(4)').innerHTML = '';
                    row.querySelector('td:nth-child(4)').appendChild(newBadge);
                } else {
                    badge.title = request;
                }
            } else {
                if (badge) {
                    row.querySelector('td:nth-child(4)').innerHTML = '<span class="text-muted">Không có</span>';
                }
            }
            alert('Đã cập nhật yêu cầu');
        }
    }
}

function bulkCheckIn() {
    if (confirm('Điểm danh tất cả khách hàng?')) {
        document.querySelectorAll('.participant-row').forEach(row => {
            if (row.dataset.status !== 'present') {
                const statusBadge = row.querySelector('.badge');
                statusBadge.className = 'badge bg-success';
                statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Có mặt';
                row.dataset.status = 'present';
            }
        });
        alert('Đã điểm danh tất cả khách hàng');
    }
}

function printParticipantList() {
    const originalContent = document.body.innerHTML;
    const printContent = document.querySelector('.guide-content').innerHTML;
    
    document.body.innerHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Danh sách khách hàng - ${tourName}</title>
            <style>
                body { font-family: Arial, sans-serif; }
                .table { width: 100%; border-collapse: collapse; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; }
                .table th { background-color: #f2f2f2; }
                .header { text-align: center; margin-bottom: 20px; }
                .timestamp { text-align: right; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>DANH SÁCH KHÁCH HÀNG</h2>
                <h4>${tourName}</h4>
                <p>Ngày khởi hành: ${departureDate}</p>
            </div>
            <div class="timestamp">In ngày: ${new Date().toLocaleDateString('vi-VN')} ${new Date().toLocaleTimeString('vi-VN')}</div>
            ${printContent}
        </body>
        </html>
    `;
    
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}

function exportParticipants() {
    const format = prompt('Chọn định dạng xuất (csv/excel/pdf):', 'excel');
    if (format) {
        const fileName = `danh_sach_khach_${tourCode}_${new Date().toISOString().slice(0,10)}.${format}`;
        alert(`Đang xuất file: ${fileName}`);
        // In real app, generate and download file
    }
}

function saveNewParticipant() {
    const form = document.getElementById('addParticipantForm');
    if (form.checkValidity()) {
        alert('Đã thêm khách hàng mới');
        $('#addParticipantModal').modal('hide');
        form.reset();
    } else {
        form.reportValidity();
    }
}

function addSpecialRequest() {
    const name = prompt('Nhập tên khách hàng:');
    const request = prompt('Nhập yêu cầu đặc biệt:');
    if (name && request) {
        alert(`Đã thêm yêu cầu cho ${name}`);
        // In real app, update UI and send to server
    }
}

function sendMessage(phone) {
    const message = prompt('Nhập nội dung tin nhắn:');
    if (message) {
        alert(`Đã gửi tin nhắn đến ${phone}\n\nNội dung: ${message}`);
        // In real app, integrate with SMS API
    }
}

// Helper functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('vi-VN');
}

function calculateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

// Initialize Chart.js
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('groupChart').getContext('2d');
    const groupData = <?php echo json_encode(array_map('count', $groups)); ?>;
    const groupLabels = <?php echo json_encode(array_keys($groups)); ?>;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: groupLabels,
            datasets: [{
                data: Object.values(groupData),
                backgroundColor: [
                    '#0d6efd',
                    '#198754', 
                    '#ffc107',
                    '#dc3545',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>