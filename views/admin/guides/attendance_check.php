<?php 
// Đặt tiêu đề trang để Header hiển thị
$page_title = "Quản Lý Điểm Danh";
require_once 'header.php'; // Đảm bảo file này nằm cùng cấp hoặc chỉnh đường dẫn

// ============================================================================
// 1. DỮ LIỆU MẪU (NẾU KHÔNG CÓ TỪ CONTROLLER)
// ============================================================================
// Đoạn này giúp file chạy độc lập được để test giao diện
if (!isset($tourInfo)) {
    $tourInfo = [
        'tour_id' => 1,
        'tour_name' => 'Tour Demo Sapa (Dữ liệu giả lập)',
        'tour_code' => 'T-DEMO-01',
        'meeting_point' => 'Sân bay Nội Bài',
        'expected_slots' => 5,
        'departure_id' => 999
    ];
}
// Nếu $passengers chưa có, tạo dữ liệu giả để hiển thị bảng
if (!isset($passengers) || empty($passengers)) {
    $passengers = [
        ['guest_id'=>101, 'full_name'=>'Nguyễn Văn A', 'booking_code'=>'BOOK-01', 'room_number'=>'101', 'special_request'=>'Ăn chay', 'customer_phone'=>'0901234567', 'attendance_status'=>'present', 'attendance_note'=>''],
        ['guest_id'=>102, 'full_name'=>'Trần Thị B', 'booking_code'=>'BOOK-01', 'room_number'=>'101', 'special_request'=>'', 'customer_phone'=>'0901234568', 'attendance_status'=>'pending', 'attendance_note'=>''],
        ['guest_id'=>103, 'full_name'=>'Lê Văn C', 'booking_code'=>'BOOK-02', 'room_number'=>'205', 'special_request'=>'Say xe', 'customer_phone'=>'0909998887', 'attendance_status'=>'late', 'attendance_note'=>'Tắc đường'],
    ];
}
// ============================================================================

// --- LOGIC TÍNH TOÁN ---
$total_guests = count($passengers);
$present_count = 0;
$absent_count = 0;
$late_count = 0;

// Mapping dữ liệu cho Tab Lịch sử
$participants_history = []; 

foreach($passengers as $p) {
    $st = $p['attendance_status'] ?? 'pending';
    if($st == 'present') $present_count++;
    elseif($st == 'absent' || $st == 'absent_start') $absent_count++;
    elseif($st == 'late') $late_count++;
    
    // Chuẩn bị data cho tab Lịch sử
    $participants_history[] = [
        'id' => $p['guest_id'],
        'name' => $p['full_name'],
        'status' => $st,
        'check_time' => ($st != 'pending') ? date('H:i') : '',
        'group' => $p['booking_code'],
        'room' => $p['room_number'] ?? 'N/A'
    ];
}

// Cấu trúc dữ liệu cho Tab Lịch Sử
$attendance_records = [
    0 => [ 
        'check_points' => [[
            'location' => $tourInfo['meeting_point'],
            'time' => date('H:i'),
            'type' => 'Hiện tại',
            'participants' => $participants_history
        ]]
    ],
    1 => ['check_points' => []] // Hôm qua rỗng
];
$participants = $participants_history; // Dùng cho biểu đồ

?>

<div class="pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="?act=guide-dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="?act=guide-schedule">Lịch trình</a></li>
                    <li class="breadcrumb-item active">Điểm danh</li>
                </ol>
            </nav>
            <h4 class="text-gray-800 fw-bold m-0">
                <i class="bi bi-bus-front-fill me-2 text-primary"></i><?= htmlspecialchars($tourInfo['tour_name']) ?>
            </h4>
        </div>
        <a href="?act=guide-dashboard" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Quay lại Dashboard
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 pt-3 px-3">
            <ul class="nav nav-tabs card-header-tabs" id="mainTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button">
                        <i class="bi bi-clipboard-check me-1"></i> Bảng Điểm Danh
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
                        <i class="bi bi-clock-history me-1"></i> Lịch Sử
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body bg-light">
            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="list" role="tabpanel">
                    <form action="?act=guide-attendance-save" method="POST" id="attendanceForm">
                        <input type="hidden" name="departure_id" value="<?= $tourInfo['departure_id'] ?>">

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body d-flex align-items-center flex-wrap gap-3">
                                <div class="flex-grow-1">
                                    <label class="form-label small fw-bold text-muted mb-1">ĐỊA ĐIỂM CHECK-IN:</label>
                                    <select class="form-select border-primary fw-bold text-primary" name="itinerary_id" required>
                                        <option value="" disabled selected>-- Chọn điểm check-in --</option>
                                        <?php if(!empty($itinerary)): ?>
                                            <?php foreach($itinerary as $pt): ?>
                                                <option value="<?= $pt['itinerary_id'] ?>">
                                                    <?= $pt['ordering'] ?>. <?= htmlspecialchars($pt['location_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="0" selected>Điểm danh chung (Mặc định)</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="text-end">
                                    <label class="form-label d-block small fw-bold text-muted mb-1">&nbsp;</label>
                                    <button type="button" class="btn btn-primary" onclick="showQRScanner()">
                                        <i class="bi bi-qr-code-scan me-1"></i> Quét QR
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-secondary small text-uppercase">
                                        <tr>
                                            <th class="ps-4">Khách hàng</th>
                                            <th>Nhóm</th>
                                            <th>Phòng</th>
                                            <th>Lưu ý</th>
                                            <th>Trạng thái</th>
                                            <th class="text-center">Thao tác</th>
                                            <th class="pe-4">Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <?php foreach($passengers as $pax): 
                                            $gId = $pax['guest_id'];
                                            $st = $pax['attendance_status'] ?? 'pending';
                                            $note = $pax['special_request'] ?? '';
                                            $paxJson = htmlspecialchars(json_encode($pax), ENT_QUOTES, 'UTF-8');
                                            
                                            // Badge logic
                                            if($st == 'present') { $bCls = 'bg-success'; $bTxt = 'Có mặt'; }
                                            elseif($st == 'absent') { $bCls = 'bg-danger'; $bTxt = 'Vắng'; }
                                            elseif($st == 'late') { $bCls = 'bg-warning text-dark'; $bTxt = 'Trễ'; }
                                            else { $bCls = 'bg-secondary'; $bTxt = 'Chờ'; }
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center" onclick="showCustomerDetail(<?= $paxJson ?>)" style="cursor:pointer">
                                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-2 border" style="width:36px; height:36px;">
                                                        <?= substr($pax['full_name'], 0, 1) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= htmlspecialchars($pax['full_name']) ?></div>
                                                        <small class="text-muted"><?= $pax['customer_phone'] ?></small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td><span class="badge bg-light text-dark border fw-normal"><?= $pax['booking_code'] ?></span></td>

                                            <td><span class="fw-bold text-secondary"><?= $pax['room_number'] ?? '-' ?></span></td>

                                            <td>
                                                <?php if($note): ?>
                                                    <span class="badge bg-warning text-dark" data-bs-toggle="tooltip" title="<?= $note ?>">
                                                        <i class="bi bi-exclamation-circle"></i> Có lưu ý
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td><span class="badge <?= $bCls ?>" id="badge-<?= $gId ?>"><?= $bTxt ?></span></td>

                                            <td class="text-center">
                                                <div class="d-none">
                                                    <input type="radio" name="status[<?= $gId ?>]" value="present" id="rad_p_<?= $gId ?>" <?= $st=='present'?'checked':'' ?>>
                                                    <input type="radio" name="status[<?= $gId ?>]" value="late" id="rad_l_<?= $gId ?>" <?= $st=='late'?'checked':'' ?>>
                                                    <input type="radio" name="status[<?= $gId ?>]" value="absent" id="rad_a_<?= $gId ?>" <?= $st=='absent'?'checked':'' ?>>
                                                </div>
                                                <div class="btn-group btn-group-sm shadow-sm">
                                                    <button type="button" class="btn btn-outline-success" onclick="setStatus(<?= $gId ?>, 'present')" title="Có mặt"><i class="bi bi-check-lg"></i></button>
                                                    <button type="button" class="btn btn-outline-warning" onclick="setStatus(<?= $gId ?>, 'late')" title="Trễ"><i class="bi bi-clock"></i></button>
                                                    <button type="button" class="btn btn-outline-danger" onclick="setStatus(<?= $gId ?>, 'absent')" title="Vắng"><i class="bi bi-x-lg"></i></button>
                                                </div>
                                            </td>

                                            <td class="pe-4">
                                                <input type="text" name="note[<?= $gId ?>]" id="note_<?= $gId ?>" 
                                                       class="form-control form-control-sm border-0 bg-light" 
                                                       placeholder="..." value="<?= $pax['attendance_note'] ?? '' ?>">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="card-footer bg-white p-3 d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="checkAll()">
                                    <i class="bi bi-check-all"></i> Tất cả có mặt
                                </button>
                                <button type="submit" class="btn btn-primary fw-bold px-4">
                                    <i class="bi bi-save me-2"></i>LƯU ĐIỂM DANH
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade" id="history" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Lịch sử hoạt động</h6>
                            <button class="btn btn-sm btn-outline-primary" onclick="alert('Đang phát triển xuất Excel')"><i class="bi bi-download"></i> Xuất báo cáo</button>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills mb-3" id="histTabs">
                                <li class="nav-item"><a class="nav-link active py-1 px-3" data-bs-toggle="tab" href="#today">Hôm nay</a></li>
                                <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#yesterday">Hôm qua</a></li>
                                <li class="nav-item"><a class="nav-link py-1 px-3" data-bs-toggle="tab" href="#all">Tất cả</a></li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="today">
                                    <?php if(!empty($attendance_records[0]['check_points'])): ?>
                                        <?php foreach($attendance_records[0]['check_points'] as $cp): ?>
                                            <div class="card mb-3 border">
                                                <div class="card-header bg-light py-2">
                                                    <strong class="text-dark"><i class="bi bi-geo-alt-fill text-danger"></i> <?= $cp['location'] ?></strong>
                                                    <span class="badge bg-primary ms-2"><?= $cp['time'] ?></span>
                                                </div>
                                                <div class="card-body p-2">
                                                    <div class="row g-2">
                                                        <?php foreach($cp['participants'] as $pt): 
                                                            $color = ($pt['status']=='present')?'success':(($pt['status']=='absent')?'danger':'warning');
                                                        ?>
                                                        <div class="col-md-4 col-sm-6">
                                                            <div class="border rounded p-2 d-flex align-items-center">
                                                                <div class="rounded-circle bg-<?= $color ?>" style="width:10px; height:10px;"></div>
                                                                <div class="ms-2 flex-grow-1 lh-1">
                                                                    <div class="fw-bold small"><?= $pt['name'] ?></div>
                                                                    <small class="text-muted" style="font-size:10px"><?= $pt['check_time'] ?: '--:--' ?></small>
                                                                </div>
                                                                <span class="badge bg-<?= $color ?>-subtle text-<?= $color ?> border border-<?= $color ?>" style="font-size:10px">
                                                                    <?= ($pt['status']=='present')?'Có mặt':(($pt['status']=='absent')?'Vắng':'Trễ') ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4 text-muted">Chưa có dữ liệu hôm nay</div>
                                    <?php endif; ?>
                                </div>
                                <div class="tab-pane fade" id="yesterday">
                                    <div class="text-center py-4 text-muted">Không có dữ liệu hôm qua</div>
                                </div>
                                <div class="tab-pane fade" id="all">
                                    <div style="height: 250px;">
                                        <canvas id="attendanceChart"></canvas>
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

<div class="modal fade" id="customerDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center pt-4">
                <div class="mb-3">
                    <div class="avatar-placeholder mx-auto bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">
                        <span id="m-avatar">A</span>
                    </div>
                </div>
                <h5 class="fw-bold mb-1" id="m-name">Tên Khách</h5>
                <span class="badge bg-light text-dark border mb-3" id="m-code">CODE</span>
                
                <div class="text-start px-3 py-2 bg-light rounded mb-3">
                    <div class="d-flex justify-content-between mb-1"><small>SĐT:</small> <strong id="m-phone">...</strong></div>
                    <div class="d-flex justify-content-between mb-1"><small>Phòng:</small> <strong id="m-room">...</strong></div>
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted d-block">Lưu ý đặc biệt:</small>
                        <strong class="text-danger small" id="m-note">Không có</strong>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary w-100 btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qrScannerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <p>Đang mở camera...</p>
                <small class="text-muted">(Tính năng mô phỏng)</small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- 1. Xử lý nút Thao tác ---
    function setStatus(id, status) {
        // Check radio
        let radioId = (status=='present') ? `rad_p_${id}` : ((status=='late') ? `rad_l_${id}` : `rad_a_${id}`);
        document.getElementById(radioId).checked = true;
        
        // Update Badge UI
        let badge = document.getElementById(`badge-${id}`);
        if(status == 'present') { badge.className = 'badge bg-success'; badge.innerText = 'Có mặt'; }
        else if(status == 'late') { badge.className = 'badge bg-warning text-dark'; badge.innerText = 'Trễ'; }
        else { badge.className = 'badge bg-danger'; badge.innerText = 'Vắng'; }

        // Note Focus if not present
        let note = document.getElementById(`note_${id}`);
        if(status != 'present') {
            note.classList.add('bg-white', 'border', 'border-danger');
            note.placeholder = "Lý do...";
        } else {
            note.classList.remove('bg-white', 'border', 'border-danger');
            note.placeholder = "...";
        }
    }

    function checkAll() {
        document.querySelectorAll('button[onclick*="\'present\'"]').forEach(btn => btn.click());
    }

    // --- 2. Modal Info ---
    function showCustomerDetail(data) {
        document.getElementById('m-name').innerText = data.full_name;
        document.getElementById('m-avatar').innerText = data.full_name.charAt(0);
        document.getElementById('m-code').innerText = data.booking_code;
        document.getElementById('m-phone').innerText = data.customer_phone || '---';
        document.getElementById('m-room').innerText = data.room_number || '---';
        document.getElementById('m-note').innerText = data.special_request || 'Không có';
        new bootstrap.Modal(document.getElementById('customerDetailModal')).show();
    }
    
    function showQRScanner() {
        new bootstrap.Modal(document.getElementById('qrScannerModal')).show();
    }

    // --- 3. Chart JS (Lịch sử) ---
    document.addEventListener("DOMContentLoaded", function() {
        // Validate form
        document.getElementById('attendanceForm').addEventListener('submit', function(e){
            if(!this.itinerary_id.value) {
                e.preventDefault();
                alert('Vui lòng chọn Địa điểm Check-in!');
                this.itinerary_id.focus();
            }
        });

        // Chart
        const ctx = document.getElementById('attendanceChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Có mặt', 'Vắng', 'Trễ'],
                    datasets: [{
                        label: 'Tổng quan',
                        data: [<?= $present_count ?>, <?= $absent_count ?>, <?= $late_count ?>],
                        backgroundColor: ['#198754', '#dc3545', '#ffc107']
                    }]
                },
                options: { maintainAspectRatio: false }
            });
        }
    });
</script>

<?php require_once 'footer.php'; ?>