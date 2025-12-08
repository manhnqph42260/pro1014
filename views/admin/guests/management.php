<?php
$page_title = "Danh sách khách hàng theo Tour";
require_once  './views/admin/header.php';
?>

<div class="container-fluid">
    <!-- Filter Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-funnel me-2"></i>Lọc thông tin khách hàng</h5>
            <form method="GET" class="row g-3">
                <input type="hidden" name="act" value="admin_guest_management">
                
                <div class="col-md-4">
                    <label for="tour_id" class="form-label">Chọn Tour</label>
                    <select class="form-select" id="tour_id" name="tour_id" onchange="loadDepartures(this.value)">
                        <option value="">-- Chọn Tour --</option>
                        <?php foreach ($tours as $tour): ?>
                        <option value="<?php echo $tour['tour_id']; ?>" <?php echo $tour_id == $tour['tour_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tour['tour_code'] . ' - ' . $tour['tour_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="departure_id" class="form-label">Chọn Lịch khởi hành</label>
                    <select class="form-select" id="departure_id" name="departure_id" onchange="this.form.submit()" <?php echo empty($departures) ? 'disabled' : ''; ?>>
                        <option value="">-- Chọn Lịch --</option>
                        <?php foreach ($departures as $dep): ?>
                        <option value="<?php echo $dep['departure_id']; ?>" <?php echo $departure_id == $dep['departure_id'] ? 'selected' : ''; ?>>
                            <?php echo date('d/m/Y', strtotime($dep['departure_date'])) . ' - ' . htmlspecialchars($dep['meeting_point']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Tìm kiếm khách</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Tên, SĐT, CMND, mã booking...">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if (!empty($search) || $departure_id > 0): ?>
                        <a href="?act=admin_guest_management" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($departure_id > 0 && isset($departure)): ?>
    <!-- Departure Info -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-1"><?php echo htmlspecialchars($departure['tour_name']); ?></h4>
                    <p class="text-muted mb-2">
                        <i class="bi bi-tag"></i> Mã tour: <?php echo htmlspecialchars($departure['tour_code']); ?> | 
                        <i class="bi bi-calendar"></i> Ngày khởi hành: <?php echo date('d/m/Y', strtotime($departure['departure_date'])); ?> | 
                        <i class="bi bi-clock"></i> Giờ: <?php echo date('H:i', strtotime($departure['departure_time'])); ?>
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-geo-alt"></i> Điểm hẹn: <?php echo htmlspecialchars($departure['meeting_point']); ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <a href="?act=showGuestList&departure_id=<?php echo $departure_id; ?>" 
                           class="btn btn-outline-primary" target="_blank">
                            <i class="bi bi-printer me-1"></i>In danh sách đoàn
                        </a>
                        <a href="?act=showRoomList&departure_id=<?php echo $departure_id; ?>" 
                           class="btn btn-outline-success" target="_blank">
                            <i class="bi bi-house-door me-1"></i>In danh sách phòng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-people fs-1 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="card-title mb-0"><?php echo $guest_stats['total_guests'] ?? 0; ?></h3>
                            <p class="card-text text-muted mb-0">Tổng số khách</p>
                            <small class="text-muted">
                                <?php echo ($guest_stats['adults'] ?? 0) . ' NL, ' . 
                                       ($guest_stats['children'] ?? 0) . ' TE, ' . 
                                       ($guest_stats['infants'] ?? 0) . ' EB'; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="card-title mb-0"><?php echo $guest_stats['checked_in'] ?? 0; ?></h3>
                            <p class="card-text text-muted mb-0">Đã check-in</p>
                            <small class="text-muted">Sẵn sàng khởi hành</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock fs-1 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="card-title mb-0"><?php echo $guest_stats['not_checked'] ?? 0; ?></h3>
                            <p class="card-text text-muted mb-0">Chưa check-in</p>
                            <small class="text-muted">Cần xác nhận</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-house-door fs-1 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="card-title mb-0"><?php echo count($rooms); ?></h3>
                            <p class="card-text text-muted mb-0">Phòng đã phân</p>
                            <small class="text-muted">Đã sắp xếp chỗ ở</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Guest List SIMPLIFIED VERSION -->
<!-- Guest List Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title">
            <i class="bi bi-list-ul me-2"></i>Danh sách khách hàng
            <span class="badge bg-primary ms-2"><?php echo count($guests); ?> khách</span>
        </h5>
        
        <?php if (count($guests) > 0): ?>
            <!-- Trong phần bảng, thêm cột "Phòng" và nút "Trả phòng" -->
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Họ tên</th>
                <th>Giới tính</th>
                <th>Booking</th>
                <th>Trạng thái</th>
                <th>Phòng</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($guests as $index => $guest): 
                // Kiểm tra xem khách đã được phân phòng chưa
                $room_assigned = false;
                $room_info = '';
                $assignment_id = 0;
                
                foreach ($rooms as $room) {
                    if ($room['guest_id'] == $guest['guest_id']) {
                        $room_assigned = true;
                        $room_info = $room['room_number'] . ' - ' . $room['hotel_name'];
                        $assignment_id = $room['assignment_id'];
                        break;
                    }
                }
            ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($guest['full_name']); ?></strong>
                    <?php if (!empty($guest['dietary_restrictions']) || !empty($guest['medical_notes']) || !empty($guest['special_requests'])): ?>
                        <span class="badge bg-warning ms-2" title="Có ghi chú đặc biệt">
                            <i class="bi bi-exclamation-triangle"></i>
                        </span>
                    <?php endif; ?>
                </td>
                <td><?php echo $guest['gender'] == 'male' ? 'Nam' : 'Nữ'; ?></td>
                <td><?php echo htmlspecialchars($guest['booking_code']); ?></td>
                <td>
                    <span class="badge bg-<?php echo $guest['booking_status'] == 'confirmed' ? 'success' : 'warning'; ?>">
                        <?php echo $guest['booking_status']; ?>
                    </span>
                </td>
                <td>
                    <?php if ($room_assigned): ?>
                        <span class="badge bg-info" title="<?php echo htmlspecialchars($room_info); ?>">
                            <i class="bi bi-house-door me-1"></i><?php echo htmlspecialchars($room_info); ?>
                        </span>
                    <?php else: ?>
                        <span class="text-muted">Chưa phân phòng</span>
                    <?php endif; ?>
                </td>
                <td>
    <!-- NÚT XEM CHI TIẾT -->
    <a href="?act=admin_guest_detail&guest_id=<?php echo $guest['guest_id']; ?>&departure_id=<?php echo $departure_id; ?>" 
       class="btn btn-sm btn-info" 
       title="Xem chi tiết">
        <i class="bi bi-eye"></i>
    </a>
    
    <?php if ($room_assigned): ?>
        <!-- NÚT SỬA PHÒNG -->
        <a href="?act=edit_room&id=<?php echo $assignment_id; ?>&departure_id=<?php echo $departure_id; ?>" 
           class="btn btn-sm btn-warning" 
           title="Sửa phòng">
            <i class="bi bi-pencil"></i>
        </a>
        
        <!-- NÚT TRẢ PHÒNG (Xóa) -->
        <a href="?act=delete_room&id=<?php echo $assignment_id; ?>&departure_id=<?php echo $departure_id; ?>" 
           class="btn btn-sm btn-danger" 
           title="Xóa phòng"
           onclick="return confirm('Bạn có chắc muốn xóa thông tin phòng này?')">
            <i class="bi bi-trash"></i>
        </a>
    <?php else: ?>
        <!-- NÚT PHÂN PHÒNG (nếu chưa có phòng) -->
        <button class="btn btn-sm btn-outline-primary assign-room" 
                data-guest-id="<?php echo $guest['guest_id']; ?>"
                data-guest-name="<?php echo htmlspecialchars($guest['full_name']); ?>"
                data-departure-id="<?php echo $departure_id; ?>"
                title="Phân phòng">
            <i class="bi bi-house-door"></i>
        </button>
    <?php endif; ?>
</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Không có khách hàng nào cho lịch khởi hành này
            </div>
        <?php endif; ?>
    </div>
</div>
    <?php elseif ($tour_id > 0): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Vui lòng chọn một lịch khởi hành để xem danh sách khách hàng
    </div>
    <?php else: ?>
    <div class="alert alert-light">
        <i class="bi bi-info-circle me-2"></i>
        Vui lòng chọn một tour để xem danh sách lịch khởi hành
    </div>
    <?php endif; ?>
</div>

<!-- Assign Room Modal -->
<div class="modal fade" id="assignRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-house-door me-2"></i>Phân phòng khách sạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignRoomForm">
                    <input type="hidden" id="room_guest_id" name="guest_id">
                    <input type="hidden" id="room_departure_id" name="departure_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Khách sạn *</label>
                        <input type="text" class="form-control" id="hotel_name" name="hotel_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số phòng *</label>
                            <input type="text" class="form-control" id="room_number" name="room_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Loại phòng</label>
                            <select class="form-select" id="room_type" name="room_type">
                                <option value="single">Phòng đơn</option>
                                <option value="double" selected>Phòng đôi</option>
                                <option value="triple">Phòng ba</option>
                                <option value="family">Phòng gia đình</option>
                                <option value="suite">Suite</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày check-in</label>
                            <input type="date" class="form-control" id="check_in_date" name="check_in_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày check-out</label>
                            <input type="date" class="form-control" id="check_out_date" name="check_out_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="room_notes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveRoomAssignment()">
                    <i class="bi bi-save me-1"></i>Lưu phòng
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Return Room Modal -->
<div class="modal fade" id="returnRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-house-door-x me-2"></i>Xác nhận trả phòng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn trả phòng cho khách hàng:</p>
                <p class="fw-bold" id="returnGuestName"></p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Thao tác này sẽ xóa thông tin phân phòng của khách hàng này.
                </div>
                
                <form id="returnRoomForm">
                    <input type="hidden" id="return_assignment_id" name="assignment_id">
                    <input type="hidden" id="return_guest_id" name="guest_id">
                    <input type="hidden" id="return_departure_id" name="departure_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Lý do trả phòng (tùy chọn)</label>
                        <textarea class="form-control" id="return_reason" name="reason" rows="2" placeholder="Ví dụ: Chuyển phòng, Hủy tour..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="confirmReturnRoom()">
                    <i class="bi bi-check-circle me-1"></i>Xác nhận trả phòng
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Guest Modal -->
<div class="modal fade" id="editGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Chỉnh sửa thông tin khách</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="guestEditFormContainer">
                <!-- Dynamic content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Check-in Modal for multiple guests -->
<div class="modal fade" id="checkInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Check-in nhiều khách</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn đang chuẩn bị check-in cho <span id="selectedCount">0</span> khách.</p>
                <div class="mb-3">
                    <label class="form-label">Trạng thái check-in</label>
                    <select class="form-select" id="bulkCheckStatus">
                        <option value="checked_in">Đã check-in</option>
                        <option value="checked_out">Đã check-out</option>
                        <option value="no_show">Không tham gia</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ghi chú chung</label>
                    <textarea class="form-control" id="bulkCheckNotes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="performBulkCheckIn()">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
// Load departures when tour is selected
function loadDepartures(tourId) {
    if (!tourId) {
        $('#departure_id').html('<option value="">-- Chọn Lịch --</option>').prop('disabled', true);
        return;
    }
    
    $.get('?act=ajax_get_departures&tour_id=' + tourId, function(data) {
        var options = '<option value="">-- Chọn Lịch --</option>';
        $.each(data, function(index, dep) {
            options += '<option value="' + dep.departure_id + '">' + 
                      dep.formatted_date + ' - ' + dep.meeting_point + '</option>';
        });
        $('#departure_id').html(options).prop('disabled', false);
    }).fail(function() {
        $('#departure_id').html('<option value="">-- Lỗi tải dữ liệu --</option>').prop('disabled', true);
    });
}

// Update check status for individual guest
$(document).on('change', '.check-status', function() {
    var guestId = $(this).data('guest-id');
    var status = $(this).val();
    
    if (!status) return;
    
    $.post('?act=updateCheckStatus', {
        guest_id: guestId,
        check_status: status,
        notes: ''
    }, function(response) {
        if (response.success) {
            showToast('Thành công', 'Cập nhật trạng thái thành công', 'success');
            
            // Update row color
            var row = $('#guest-row-' + guestId);
            row.removeClass('table-success table-danger');
            if (status === 'checked_in') {
                row.addClass('table-success');
            } else if (status === 'no_show') {
                row.addClass('table-danger');
            }
        } else {
            showToast('Lỗi', response.message, 'error');
            // Revert selection
            $(this).val('');
        }
    });
});

// Assign room modal
$(document).on('click', '.assign-room', function() {
    var guestId = $(this).data('guest-id');
    var departureId = $(this).data('departure-id');
    
    $('#room_guest_id').val(guestId);
    $('#room_departure_id').val(departureId);
    
    // Clear form
    $('#hotel_name').val('');
    $('#room_number').val('');
    $('#room_type').val('double');
    $('#check_in_date').val('');
    $('#check_out_date').val('');
    $('#room_notes').val('');
    
    $('#assignRoomModal').modal('show');
});

// Save room assignment
function saveRoomAssignment() {
    var formData = $('#assignRoomForm').serialize();
    
    $.post('?act=assignRoom', formData, function(response) {
        if (response.success) {
            $('#assignRoomModal').modal('hide');
            showToast('Thành công', 'Phân phòng thành công', 'success');
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            showToast('Lỗi', response.message, 'error');
        }
    });
}

// Edit guest info
$(document).on('click', '.edit-guest', function() {
    var guestId = $(this).data('guest-id');
    
    $.get('?act=ajax_get_guest_info&guest_id=' + guestId, function(guest) {
        if (!guest || !guest.guest_id) {
            showToast('Lỗi', 'Không thể tải thông tin khách', 'error');
            return;
        }
        
        var formHtml = `
            <form id="editGuestForm">
                <input type="hidden" name="guest_id" value="${guest.guest_id}">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Họ tên *</label>
                        <input type="text" class="form-control" name="full_name" value="${guest.full_name || ''}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" class="form-control" name="date_of_birth" value="${guest.date_of_birth || ''}">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Giới tính</label>
                        <select class="form-select" name="gender">
                            <option value="">-- Chọn --</option>
                            <option value="male" ${guest.gender == 'male' ? 'selected' : ''}>Nam</option>
                            <option value="female" ${guest.gender == 'female' ? 'selected' : ''}>Nữ</option>
                            <option value="other" ${guest.gender == 'other' ? 'selected' : ''}>Khác</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số CMND/CCCD</label>
                        <input type="text" class="form-control" name="id_number" value="${guest.id_number || ''}">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Số passport</label>
                        <input type="text" class="form-control" name="passport_number" value="${guest.passport_number || ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày hết hạn passport</label>
                        <input type="date" class="form-control" name="passport_expiry" value="${guest.passport_expiry || ''}">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Quốc tịch</label>
                        <input type="text" class="form-control" name="nationality" value="${guest.nationality || ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Liên hệ khẩn cấp</label>
                        <input type="text" class="form-control" name="emergency_contact" value="${guest.emergency_contact || ''}">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Yêu cầu ăn kiêng</label>
                    <input type="text" class="form-control" name="dietary_restrictions" value="${guest.dietary_restrictions || ''}">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Ghi chú sức khỏe</label>
                    <textarea class="form-control" name="medical_notes" rows="2">${guest.medical_notes || ''}</textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Yêu cầu đặc biệt</label>
                    <textarea class="form-control" name="special_requests" rows="2">${guest.special_requests || ''}</textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        `;
        
        $('#guestEditFormContainer').html(formHtml);
        $('#editGuestModal').modal('show');
    }).fail(function() {
        showToast('Lỗi', 'Không thể tải thông tin khách', 'error');
    });
});

// Save guest info
$(document).on('submit', '#editGuestForm', function(e) {
    e.preventDefault();
    
    var formData = $(this).serializeArray();
    var guestId = formData.find(f => f.name === 'guest_id').value;
    var updatedCount = 0;
    var errorCount = 0;
    
    // Update each field
    formData.forEach(function(field) {
        if (field.name !== 'guest_id') {
            $.post('?act=updateGuestInfo', {
                guest_id: guestId,
                field: field.name,
                value: field.value
            }, function(response) {
                if (response.success) {
                    updatedCount++;
                } else {
                    errorCount++;
                }
                
                // When all updates are done
                if (updatedCount + errorCount === formData.length - 1) {
                    if (errorCount === 0) {
                        showToast('Thành công', 'Cập nhật thông tin thành công', 'success');
                        $('#editGuestModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast('Có lỗi', `Cập nhật ${updatedCount} trường, lỗi ${errorCount} trường`, 'warning');
                    }
                }
            });
        }
    });
});

// Bulk operations
function toggleSelectAll() {
    var isChecked = $('#selectAll').prop('checked');
    $('.guest-checkbox').prop('checked', isChecked);
    updateSelectedCount();
}

function selectAll() {
    $('.guest-checkbox').prop('checked', true);
    $('#selectAll').prop('checked', true);
    updateSelectedCount();
}

function updateSelectedCount() {
    var count = $('.guest-checkbox:checked').length;
    $('#selectedCount').text(count);
    return count;
}

function checkInSelected() {
    var count = updateSelectedCount();
    if (count === 0) {
        showToast('Thông báo', 'Vui lòng chọn ít nhất một khách', 'warning');
        return;
    }
    $('#checkInModal').modal('show');
}

function performBulkCheckIn() {
    var guestIds = [];
    $('.guest-checkbox:checked').each(function() {
        guestIds.push($(this).val());
    });
    
    var status = $('#bulkCheckStatus').val();
    var notes = $('#bulkCheckNotes').val();
    
    // Update each guest
    var completed = 0;
    var errors = 0;
    
    guestIds.forEach(function(guestId) {
        $.post('?act=updateCheckStatus', {
            guest_id: guestId,
            check_status: status,
            notes: notes
        }, function(response) {
            if (response.success) {
                completed++;
                // Update row color
                var row = $('#guest-row-' + guestId);
                row.removeClass('table-success table-danger');
                if (status === 'checked_in') {
                    row.addClass('table-success');
                } else if (status === 'no_show') {
                    row.addClass('table-danger');
                }
                // Update select dropdown
                row.find('.check-status').val(status);
            } else {
                errors++;
            }
            
            // When all done
            if (completed + errors === guestIds.length) {
                $('#checkInModal').modal('hide');
                if (errors === 0) {
                    showToast('Thành công', `Đã check-in ${completed} khách`, 'success');
                } else {
                    showToast('Có lỗi', `Thành công: ${completed}, Lỗi: ${errors}`, 'warning');
                }
                // Uncheck all
                $('.guest-checkbox').prop('checked', false);
                $('#selectAll').prop('checked', false);
                updateSelectedCount();
            }
        });
    });
}

// Export guest list
function exportGuestList() {
    var departureId = <?php echo $departure_id ?: 0; ?>;
    if (!departureId) {
        showToast('Lỗi', 'Vui lòng chọn lịch khởi hành trước', 'error');
        return;
    }
    
    // Open print page
    window.open('?act=showGuestList&departure_id=' + departureId);
}

// Toast notification
function showToast(title, message, type) {
    // Remove existing toasts
    $('.toast-container').remove();
    
    // Create new toast
    var toast = `
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}:</strong> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(toast);
    $('.toast').toast('show');
    
    // Auto hide after 3 seconds
    setTimeout(function() {
        $('.toast').toast('hide');
    }, 3000);
}

// Initialize page
$(document).ready(function() {
    // If tour is selected, load departures
    var tourId = $('#tour_id').val();
    if (tourId) {
        loadDepartures(tourId);
    }
});
// Assign room modal - FIXED VERSION
$(document).on('click', '.assign-room', function() {
    var guestId = $(this).data('guest-id');
    var departureId = $(this).data('departure-id');
    
    console.log("Assign room clicked - Guest ID:", guestId, "Departure ID:", departureId);
    
    // Set values
    $('#room_guest_id').val(guestId);
    $('#room_departure_id').val(departureId);
    
    // Clear form
    $('#hotel_name').val('');
    $('#room_number').val('');
    $('#room_type').val('double');
    $('#room_notes').val('');
    
    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('assignRoomModal'));
    modal.show();
});

// Save room assignment - FIXED VERSION
function saveRoomAssignment() {
    var formData = $('#assignRoomForm').serialize();
    
    console.log("Saving room assignment:", formData);
    
    $.ajax({
        url: '?act=assignRoom',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log("Response:", response);
            if (response.success) {
                $('#assignRoomModal').modal('hide');
                showToast('Thành công', 'Phân phòng thành công', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showToast('Lỗi', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
            showToast('Lỗi', 'Không thể kết nối đến server', 'error');
        }
    });
}

// Toast notification function
function showToast(title, message, type) {
    // Remove existing toasts
    $('.toast-container').remove();
    
    // Create new toast
    var toast = `
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}:</strong> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(toast);
    $('.toast').toast('show');
    
    // Auto hide after 3 seconds
    setTimeout(function() {
        $('.toast').toast('hide');
    }, 3000);
}
// Return room modal
$(document).on('click', '.return-room', function() {
    var assignmentId = $(this).data('assignment-id');
    var guestId = $(this).data('guest-id');
    var guestName = $(this).data('guest-name');
    var departureId = $(this).data('departure-id');
    
    console.log("Return room clicked - Assignment ID:", assignmentId);
    
    // Set values
    $('#return_assignment_id').val(assignmentId);
    $('#return_guest_id').val(guestId);
    $('#return_departure_id').val(departureId);
    $('#returnGuestName').text(guestName);
    
    // Clear form
    $('#return_reason').val('');
    
    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('returnRoomModal'));
    modal.show();
});

// Confirm return room
function confirmReturnRoom() {
    var formData = $('#returnRoomForm').serialize();
    
    console.log("Returning room:", formData);
    
    $.ajax({
        url: '?act=returnRoom',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log("Response:", response);
            if (response.success) {
                $('#returnRoomModal').modal('hide');
                showToast('Thành công', 'Đã trả phòng thành công', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showToast('Lỗi', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
            showToast('Lỗi', 'Không thể kết nối đến server', 'error');
        }
    });
}
</script>

<style>
.stat-card {
    transition: transform 0.2s;
    border-left: 4px solid #007bff;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}
.check-status {
    cursor: pointer;
}
</style>

<?php require_once './views/admin/footer.php';
?>
