<?php
$title = "Quản lý Booking";
require_once './views/admin/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Quản lý Booking</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="?act=admin_bookings_create" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Tạo Booking mới
        </a>
    </div>
</div>

<!-- Thống kê -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><?= $stats['total_bookings'] ?></h5>
                <p class="card-text">Tổng Booking</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title"><?= $stats['pending'] ?></h5>
                <p class="card-text">Chờ xác nhận</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title"><?= $stats['confirmed'] ?></h5>
                <p class="card-text">Đã xác nhận</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5 class="card-title"><?= $stats['cancelled'] ?></h5>
                <p class="card-text">Đã hủy</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title"><?= $stats['total_guests'] ?></h5>
                <p class="card-text">Tổng khách</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter và Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="act" value="admin_bookings">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm theo mã, tên, số điện thoại..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                    <option value="confirmed" <?= $status_filter == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                    <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            </div>
        </form>
    </div>
</div>

<!-- Danh sách booking -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Mã Booking</th>
                        <th>Tour</th>
                        <th>Khách hàng</th>
                        <th>Ngày khởi hành</th>
                        <th>Số khách</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có booking nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><strong><?= $booking['booking_code'] ?></strong></td>
                                <td><?= $booking['tour_name'] ?></td>
                                <td>
                                    <div><?= $booking['customer_name'] ?></div>
                                    <small class="text-muted"><?= $booking['customer_phone'] ?></small>
                                </td>
                                <td><?= date('d/m/Y', strtotime($booking['departure_date'])) ?></td>
                                <td><?= $booking['total_guests'] ?> khách</td>
                                <td><?= number_format($booking['total_amount']) ?> đ</td>
                                <td>
                                    <?php
                                    $status_badge = [
                                        'pending' => 'warning',
                                        'confirmed' => 'success', 
                                        'cancelled' => 'danger',
                                        'completed' => 'info'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $status_badge[$booking['status']] ?>">
                                        <?= $booking['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?act=admin_bookings_view&id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?act=admin_bookings_edit&id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once './views/admin/footer.php'; ?>