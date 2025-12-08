<?php
$page_title = "Chi tiết Khách hàng";
require_once './views/admin/header.php';
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="?act=admin_dashboard">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="?act=admin_guest_management">Quản lý Khách hàng</a></li>
            <li class="breadcrumb-item active">Chi tiết Khách hàng</li>
        </ol>
    </nav>

    <!-- Header Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">
                        <i class="bi bi-person-badge me-2"></i><?php echo htmlspecialchars($guest['full_name']); ?>
                    </h4>
                    <p class="text-muted mb-0">
                        <i class="bi bi-tag me-1"></i>Booking: <?php echo htmlspecialchars($guest['booking_code']); ?> | 
                        <i class="bi bi-calendar me-1"></i>Tour: <?php echo htmlspecialchars($guest['tour_name']); ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="?act=admin_guest_management&departure_id=<?php echo $guest['departure_id']; ?>" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại
                    </a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>In
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Guest Information -->
        <div class="col-md-6">
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin Cơ bản</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Họ tên</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['full_name']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Giới tính</label>
                            <p class="form-control-plaintext">
                                <?php 
                                echo $guest['gender'] == 'male' ? 'Nam' : 
                                     ($guest['gender'] == 'female' ? 'Nữ' : 'Khác');
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Ngày sinh</label>
                            <p class="form-control-plaintext">
                                <?php echo $guest['date_of_birth'] ? date('d/m/Y', strtotime($guest['date_of_birth'])) : '---'; ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Loại khách</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-<?php echo $guest['guest_type'] == 'adult' ? 'primary' : 
                                                        ($guest['guest_type'] == 'child' ? 'success' : 'info'); ?>">
                                    <?php echo $guest['guest_type'] == 'adult' ? 'Người lớn' : 
                                           ($guest['guest_type'] == 'child' ? 'Trẻ em' : 'Em bé'); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Số CMND/CCCD</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['id_number'] ?? '---'); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Quốc tịch</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['nationality'] ?? 'Việt Nam'); ?></p>
                        </div>
                    </div>
                </div>
            </div>


<!-- Special Notes Card -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Ghi chú Đặc biệt</h5>
        <a href="?act=edit_special_notes&guest_id=<?php echo $guest['guest_id']; ?>&departure_id=<?php echo $guest['departure_id']; ?>" 
           class="btn btn-sm btn-outline-dark">
            <i class="bi bi-pencil"></i> Chỉnh sửa
        </a>
    </div>
    <div class="card-body">
        <!-- Dietary Restrictions -->
        <div class="mb-4">
            <h6><i class="bi bi-egg-fried me-2"></i>Yêu cầu Ăn uống</h6>
            <?php if (!empty($guest['dietary_restrictions']) || !empty($guest['food_allergies'])): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    <?php if (!empty($guest['dietary_restrictions'])): ?>
                        <div class="mb-1"><strong>Chế độ ăn:</strong> <?php echo nl2br(htmlspecialchars($guest['dietary_restrictions'])); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($guest['food_allergies'])): ?>
                        <div class="mb-1"><strong>Dị ứng:</strong> <?php echo nl2br(htmlspecialchars($guest['food_allergies'])); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($guest['medications'])): ?>
                        <div class="mb-1"><strong>Thuốc đang dùng:</strong> <?php echo nl2br(htmlspecialchars($guest['medications'])); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($guest['blood_type'])): ?>
                        <div class="mb-0"><strong>Nhóm máu:</strong> <?php echo htmlspecialchars($guest['blood_type']); ?></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">Không có yêu cầu đặc biệt</p>
            <?php endif; ?>
        </div>

        <!-- Medical Notes -->
        <div class="mb-4">
            <h6><i class="bi bi-heart-pulse me-2"></i>Thông tin Y tế</h6>
            <?php if (!empty($guest['medical_notes'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo nl2br(htmlspecialchars($guest['medical_notes'])); ?>
                    <?php if (!empty($guest['emergency_notes'])): ?>
                        <hr>
                        <div class="mt-2"><strong>Lưu ý cấp cứu:</strong> <?php echo nl2br(htmlspecialchars($guest['emergency_notes'])); ?></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">Không có thông tin y tế đặc biệt</p>
            <?php endif; ?>
        </div>

        <!-- Room & Transport Requests -->
        <?php if (!empty($guest['room_requests']) || !empty($guest['transport_requests'])): ?>
        <div class="mb-4">
            <h6><i class="bi bi-gear me-2"></i>Yêu cầu Tiện nghi</h6>
            <div class="row">
                <?php if (!empty($guest['room_requests'])): ?>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <i class="bi bi-house-door me-2"></i>
                        <strong>Phòng nghỉ:</strong>
                        <?php 
                        $room_requests = json_decode($guest['room_requests'], true);
                        if (is_array($room_requests)) {
                            echo implode(', ', $room_requests);
                        } else {
                            echo htmlspecialchars($guest['room_requests']);
                        }
                        ?>
                        <?php if (!empty($guest['room_requests_other'])): ?>
                            <br><em><?php echo htmlspecialchars($guest['room_requests_other']); ?></em>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($guest['transport_requests'])): ?>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <i class="bi bi-bus-front me-2"></i>
                        <strong>Di chuyển:</strong>
                        <?php 
                        $transport_requests = json_decode($guest['transport_requests'], true);
                        if (is_array($transport_requests)) {
                            echo implode(', ', $transport_requests);
                        } else {
                            echo htmlspecialchars($guest['transport_requests']);
                        }
                        ?>
                        <?php if (!empty($guest['transport_requests_other'])): ?>
                            <br><em><?php echo htmlspecialchars($guest['transport_requests_other']); ?></em>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Special Requests -->
        <div class="mb-4">
            <h6><i class="bi bi-star me-2"></i>Yêu cầu Đặc biệt Khác</h6>
            <?php if (!empty($guest['special_requests']) || !empty($guest['hobbies_interests']) || !empty($guest['travel_history'])): ?>
                <div class="alert alert-info">
                    <i class="bi bi-chat-left-text me-2"></i>
                    <?php if (!empty($guest['special_requests'])): ?>
                        <div class="mb-2"><strong>Yêu cầu chung:</strong> <?php echo nl2br(htmlspecialchars($guest['special_requests'])); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($guest['hobbies_interests'])): ?>
                        <div class="mb-2"><strong>Sở thích:</strong> <?php echo nl2br(htmlspecialchars($guest['hobbies_interests'])); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($guest['travel_history'])): ?>
                        <div class="mb-0"><strong>Lịch sử du lịch:</strong> <?php echo nl2br(htmlspecialchars($guest['travel_history'])); ?></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">Không có yêu cầu đặc biệt khác</p>
            <?php endif; ?>
        </div>

        <!-- Notes for Staff -->
        <?php if (!empty($guest['notes_for_guide']) || !empty($guest['notes_for_hotel']) || !empty($guest['requires_special_attention'])): ?>
        <div class="mb-4">
            <h6><i class="bi bi-chat-left-text me-2"></i>Ghi chú Nội bộ</h6>
            <div class="alert alert-secondary">
                <?php if (!empty($guest['notes_for_guide'])): ?>
                    <div class="mb-2">
                        <i class="bi bi-person-badge me-1"></i>
                        <strong>Cho HDV:</strong> <?php echo nl2br(htmlspecialchars($guest['notes_for_guide'])); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($guest['notes_for_hotel'])): ?>
                    <div class="mb-2">
                        <i class="bi bi-building me-1"></i>
                        <strong>Cho KS/NH:</strong> <?php echo nl2br(htmlspecialchars($guest['notes_for_hotel'])); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($guest['requires_special_attention'])): ?>
                    <div class="mb-0">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>
                        <strong class="text-danger">CẦN QUAN TÂM ĐẶC BIỆT</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Emergency Contact -->
        <div>
            <h6><i class="bi bi-telephone me-2"></i>Liên hệ Khẩn cấp</h6>
            <div class="alert alert-success">
                <?php if (!empty($guest['emergency_contact_name'])): ?>
                    <div class="mb-1">
                        <strong>Tên:</strong> <?php echo htmlspecialchars($guest['emergency_contact_name']); ?>
                        <?php if (!empty($guest['emergency_relationship'])): ?>
                            (<?php echo htmlspecialchars($guest['emergency_relationship']); ?>)
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($guest['emergency_contact_phone'])): ?>
                    <div class="mb-1"><strong>Điện thoại:</strong> <?php echo htmlspecialchars($guest['emergency_contact_phone']); ?></div>
                <?php endif; ?>
                <?php if (!empty($guest['emergency_contact_email'])): ?>
                    <div class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($guest['emergency_contact_email']); ?></div>
                <?php endif; ?>
                <?php if (!empty($guest['emergency_contact_address'])): ?>
                    <div class="mb-0"><strong>Địa chỉ:</strong> <?php echo nl2br(htmlspecialchars($guest['emergency_contact_address'])); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- Right Column: Tour & Booking Information -->
        <div class="col-md-6">
            <!-- Tour Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Thông tin Tour</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tour</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['tour_name']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Mã tour</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['tour_code']); ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Ngày khởi hành</label>
                            <p class="form-control-plaintext">
                                <?php echo date('d/m/Y', strtotime($guest['departure_date'])); ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Giờ khởi hành</label>
                            <p class="form-control-plaintext">
                                <?php echo date('H:i', strtotime($guest['departure_time'])); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Điểm hẹn</label>
                        <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['meeting_point']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Booking Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-ticket-detailed me-2"></i>Thông tin Booking</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Mã booking</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['booking_code']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Trạng thái</label>
                            <p>
                                <span class="badge bg-<?php 
                                    echo $guest['booking_status'] == 'confirmed' ? 'success' : 
                                         ($guest['booking_status'] == 'pending' ? 'warning' : 
                                         ($guest['booking_status'] == 'cancelled' ? 'danger' : 'secondary')); 
                                ?>">
                                    <?php echo $guest['booking_status']; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Người đặt</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['booker_name']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Điện thoại</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['customer_phone']); ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['customer_email']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Loại booking</label>
                            <p class="form-control-plaintext">
                                <?php echo $guest['booking_type'] == 'individual' ? 'Cá nhân' : 'Đoàn'; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($guest['booking_type'] == 'group'): ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tên đoàn</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['group_name']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Công ty</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['company_name']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Room Assignment -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-house-door me-2"></i>Thông tin Phòng</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($guest['room_number'])): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Khách sạn</label>
                                <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['hotel_name']); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Số phòng</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-info"><?php echo htmlspecialchars($guest['room_number']); ?></span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Loại phòng</label>
                                <p class="form-control-plaintext">
                                    <?php 
                                    $room_types = [
                                        'single' => 'Phòng đơn',
                                        'double' => 'Phòng đôi',
                                        'triple' => 'Phòng ba',
                                        'family' => 'Phòng gia đình',
                                        'suite' => 'Suite'
                                    ];
                                    echo $room_types[$guest['room_type']] ?? $guest['room_type'];
                                    ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Ngày ở</label>
                                <p class="form-control-plaintext">
                                    <?php 
                                    if ($guest['check_in_date']) {
                                        echo date('d/m/Y', strtotime($guest['check_in_date']));
                                    }
                                    if ($guest['check_out_date']) {
                                        echo ' - ' . date('d/m/Y', strtotime($guest['check_out_date']));
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        
                        <?php if (!empty($guest['room_notes'])): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Ghi chú phòng</label>
                            <p class="form-control-plaintext"><?php echo htmlspecialchars($guest['room_notes']); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Room Actions -->
                        <div class="mt-3">
                            <a href="?act=edit_room&id=<?php echo $guest['assignment_id'] ?? 0; ?>&departure_id=<?php echo $guest['departure_id']; ?>" 
                               class="btn btn-sm btn-outline-primary me-2">
                                <i class="bi bi-pencil me-1"></i>Sửa phòng
                            </a>
                            <a href="?act=delete_room&id=<?php echo $guest['assignment_id'] ?? 0; ?>&departure_id=<?php echo $guest['departure_id']; ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Bạn có chắc muốn xóa thông tin phòng này?')">
                                <i class="bi bi-trash me-1"></i>Xóa phòng
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Khách chưa được phân phòng
                        </div>
                        <a href="?act=admin_guest_management&departure_id=<?php echo $guest['departure_id']; ?>" 
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-house-door me-1"></i>Phân phòng ngay
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Trạng thái & Thời gian</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Trạng thái check-in</label>
                            <p class="form-control-plaintext">
                                <?php if ($guest['check_status'] == 'checked_in'): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Đã check-in
                                    </span>
                                <?php elseif ($guest['check_status'] == 'checked_out'): ?>
                                    <span class="badge bg-info">
                                        <i class="bi bi-box-arrow-right me-1"></i>Đã check-out
                                    </span>
                                <?php elseif ($guest['check_status'] == 'no_show'): ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Không đến
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-clock me-1"></i>Chưa check-in
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Thời gian check-in</label>
                            <p class="form-control-plaintext">
                                <?php echo $guest['check_in_time'] ? date('H:i d/m/Y', strtotime($guest['check_in_time'])) : '---'; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Ngày booking</label>
                            <p class="form-control-plaintext">
                                <?php echo $guest['booked_at'] ? date('H:i d/m/Y', strtotime($guest['booked_at'])) : '---'; ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Ngày xác nhận</label>
                            <p class="form-control-plaintext">
                                <?php echo $guest['confirmed_at'] ? date('H:i d/m/Y', strtotime($guest['confirmed_at'])) : '---'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .no-print, .breadcrumb {
        display: none !important;
    }
    .card-header {
        background-color: #f8f9fa !important;
        color: #000 !important;
        border-bottom: 2px solid #000;
    }
    .card {
        border: 1px solid #000 !important;
    }
}
</style>

<?php require_once './views/admin/footer.php'; ?>