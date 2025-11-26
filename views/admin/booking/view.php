<?php
$title = "Chi tiết Booking - " . $booking['booking_code'];
require_once './views/admin/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Booking: <?= $booking['booking_code'] ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="?act=admin_bookings" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <a href="?act=admin_bookings_edit&id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Sửa
            </a>
            <?php if ($booking['status'] == 'pending'): ?>
                <a href="?act=admin_bookings_confirm&id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-success" 
                   onclick="return confirm('Xác nhận booking này?')">
                    <i class="fas fa-check"></i> Xác nhận
                </a>
            <?php endif; ?>
            <?php if ($booking['status'] == 'pending' || $booking['status'] == 'confirmed'): ?>
                <a href="?act=admin_bookings_cancel&id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-danger"
                   onclick="return confirm('Hủy booking này?')">
                    <i class="fas fa-times"></i> Hủy
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <!-- Thông tin chính -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông tin Booking</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Mã Booking:</strong> <?= $booking['booking_code'] ?></p>
                        <p><strong>Tour:</strong> <?= $booking['tour_name'] ?> (<?= $booking['tour_code'] ?>)</p>
                        <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($booking['departure_date'])) ?></p>
                        <?php if ($booking['departure_time']): ?>
                            <p><strong>Giờ khởi hành:</strong> <?= date('H:i', strtotime($booking['departure_time'])) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Trạng thái:</strong> 
                            <span class="badge bg-<?= 
                                $booking['status'] == 'pending' ? 'warning' : 
                                ($booking['status'] == 'confirmed' ? 'success' : 'danger')
                            ?>">
                                <?= $booking['status'] ?>
                            </span>
                        </p>
                        <p><strong>Loại booking:</strong> <?= $booking['booking_type'] == 'individual' ? 'Khách lẻ' : 'Đoàn/Group' ?></p>
                        <?php if ($booking['group_name']): ?>
                            <p><strong>Tên đoàn:</strong> <?= $booking['group_name'] ?></p>
                        <?php endif; ?>
                        <?php if ($booking['company_name']): ?>
                            <p><strong>Công ty:</strong> <?= $booking['company_name'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin khách hàng -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông tin khách hàng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Họ tên:</strong> <?= $booking['customer_name'] ?></p>
                        <p><strong>Số điện thoại:</strong> <?= $booking['customer_phone'] ?></p>
                        <p><strong>Email:</strong> <?= $booking['customer_email'] ?: 'N/A' ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Địa chỉ:</strong> <?= $booking['customer_address'] ?: 'N/A' ?></p>
                        <p><strong>Người đặt:</strong> <?= $booking['booked_by_name'] ?></p>
                        <p><strong>Thời gian đặt:</strong> <?= date('d/m/Y H:i', strtotime($booking['booked_at'])) ?></p>
                        <?php if ($booking['confirmed_by_name']): ?>
                            <p><strong>Người xác nhận:</strong> <?= $booking['confirmed_by_name'] ?></p>
                            <p><strong>Thời gian xác nhận:</strong> <?= date('d/m/Y H:i', strtotime($booking['confirmed_at'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($booking['special_requests']): ?>
                    <div class="mt-3">
                        <strong>Yêu cầu đặc biệt:</strong>
                        <p class="mt-1"><?= nl2br(htmlspecialchars($booking['special_requests'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Danh sách khách -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Danh sách khách (<?= $booking['total_guests'] ?> người)</h5>
                <span class="badge bg-primary">
                    <?= $booking['adult_count'] ?> người lớn, 
                    <?= $booking['child_count'] ?> trẻ em, 
                    <?= $booking['infant_count'] ?> em bé
                </span>
            </div>
            <div class="card-body">
                <?php if (empty($guests)): ?>
                    <p class="text-muted">Chưa có thông tin khách chi tiết</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Họ tên</th>
                                    <th>Ngày sinh</th>
                                    <th>Giới tính</th>
                                    <th>Loại</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($guests as $guest): ?>
                                    <tr>
                                        <td><?= $guest['full_name'] ?></td>
                                        <td><?= $guest['date_of_birth'] ? date('d/m/Y', strtotime($guest['date_of_birth'])) : 'N/A' ?></td>
                                        <td>
                                            <?= $guest['gender'] == 'male' ? 'Nam' : ($guest['gender'] == 'female' ? 'Nữ' : 'N/A') ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $guest['guest_type'] == 'adult' ? 'primary' : 
                                                ($guest['guest_type'] == 'child' ? 'warning' : 'info')
                                            ?>">
                                                <?= $guest['guest_type'] == 'adult' ? 'Người lớn' : 
                                                   ($guest['guest_type'] == 'child' ? 'Trẻ em' : 'Em bé') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Thông tin thanh toán -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Thông tin thanh toán</h5>
                <a href="?act=admin_bookings_add_payment&id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Thêm
                </a>
            </div>
            <div class="card-body">
                <p><strong>Tổng tiền:</strong> <?= number_format($booking['total_amount']) ?> đ</p>
                <p><strong>Đặt cọc:</strong> <?= number_format($booking['deposit_amount']) ?> đ</p>
                <p><strong>Đã thanh toán:</strong> <span class="text-success"><?= number_format($total_paid) ?> đ</span></p>
                <p><strong>Còn lại:</strong> <span class="text-danger"><?= number_format($booking['total_amount'] - $total_paid) ?> đ</span></p>
                
                <?php if (!empty($payments)): ?>
                    <hr>
                    <h6>Lịch sử thanh toán:</h6>
                    <?php foreach ($payments as $payment): ?>
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong><?= number_format($payment['amount']) ?> đ</strong>
                                <span class="badge bg-<?= $payment['status'] == 'completed' ? 'success' : 'warning' ?>">
                                    <?= $payment['status'] ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <?= $payment['payment_method'] ?> - <?= date('d/m/Y', strtotime($payment['payment_date'])) ?>
                                <?php if ($payment['transaction_code']): ?>
                                    <br>Mã: <?= $payment['transaction_code'] ?>
                                <?php endif; ?>
                            </small>
                            <?php if ($payment['notes']): ?>
                                <div class="mt-1"><small><?= $payment['notes'] ?></small></div>
                            <?php endif; ?>
                            <div class="mt-1">
                                <a href="?act=admin_bookings_delete_payment&payment_id=<?= $payment['payment_id'] ?>&booking_id=<?= $booking['booking_id'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Xóa thanh toán này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thông tin điểm hẹn -->
        <?php if ($booking['meeting_point']): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Điểm hẹn</h5>
                </div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($booking['meeting_point'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once './views/admin/footer.php'; ?>