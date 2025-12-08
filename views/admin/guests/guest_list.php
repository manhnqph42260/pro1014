<?php
$pageTitle = "Danh sách đoàn - " . $departure_info['tour_name'];
require_once './views/admin/header.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> <?= $pageTitle ?>
        </h1>
        <div>
            <a href="?act=admin_guest_management&departure_id=<?= $_GET['departure_id'] ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> In trang
            </button>
        </div>
    </div>

    <!-- Tour Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-info-circle"></i> Thông tin Tour
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Tour:</strong> <?= htmlspecialchars($departure_info['tour_name']) ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Mã tour:</strong> <?= htmlspecialchars($departure_info['tour_code']) ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($departure_info['departure_date'])) ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Số chỗ:</strong> <?= $departure_info['expected_slots'] ?> chỗ</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Guides Info -->
    <?php if (!empty($guides)): ?>
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-user-tie"></i> Hướng dẫn viên
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($guides as $guide): ?>
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3">
                        <h6><strong><?= htmlspecialchars($guide['person_name']) ?></strong></h6>
                        <p class="mb-1"><small>Vai trò: <?= htmlspecialchars($guide['role']) ?></small></p>
                        <p class="mb-0"><small>Liên hệ: <?= htmlspecialchars($guide['contact_info']) ?></small></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Tổng khách</h5>
                    <h2 class="mb-0"><?= $stats['total'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Người lớn</h5>
                    <h2 class="mb-0"><?= $stats['adults'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Trẻ em</h5>
                    <h2 class="mb-0"><?= $stats['children'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Đã check-in</h5>
                    <h2 class="mb-0"><?= $stats['checked_in'] ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Guest List Table -->
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-list"></i> Danh sách chi tiết khách hàng
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>STT</th>
                            <th>Họ tên</th>
                            <th>Loại</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                            <th>Số giấy tờ</th>
                            <th>Phòng</th>
                            <th>Trạng thái</th>
                            <th>Booking</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        foreach ($guests as $guest): 
                        ?>
                        <tr>
                            <td class="text-center"><?= $counter++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($guest['full_name']) ?></strong>
                                <?php if (!empty($guest['medical_notes'])): ?>
                                    <br><small class="text-danger">⚠️ Y tế: <?= htmlspecialchars(substr($guest['medical_notes'], 0, 50)) ?>...</small>
                                <?php endif; ?>
                                <?php if (!empty($guest['dietary_restrictions'])): ?>
                                    <br><small class="text-warning">🍽️ Ăn uống: <?= htmlspecialchars(substr($guest['dietary_restrictions'], 0, 50)) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                $type_badge = [
                                    'adult' => 'primary',
                                    'child' => 'success',
                                    'infant' => 'info'
                                ];
                                ?>
                                <span class="badge bg-<?= $type_badge[$guest['guest_type']] ?? 'secondary' ?>">
                                    <?= $guest['guest_type'] == 'adult' ? 'Người lớn' : 
                                       ($guest['guest_type'] == 'child' ? 'Trẻ em' : 'Em bé') ?>
                                </span>
                            </td>
                            <td class="text-center"><?= $guest['date_of_birth'] ? date('d/m/Y', strtotime($guest['date_of_birth'])) : '---' ?></td>
                            <td class="text-center"><?= $guest['gender'] == 'male' ? 'Nam' : ($guest['gender'] == 'female' ? 'Nữ' : '---') ?></td>
                            <td>
                                <?php if (!empty($guest['id_number'])): ?>
                                    <small>CMND: <?= htmlspecialchars($guest['id_number']) ?></small><br>
                                <?php endif; ?>
                                <?php if (!empty($guest['passport_number'])): ?>
                                    <small>Passport: <?= htmlspecialchars($guest['passport_number']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($guest['room_number'])): ?>
                                    <span class="badge bg-info"><?= htmlspecialchars($guest['room_number']) ?></span><br>
                                    <small><?= htmlspecialchars($guest['hotel_name']) ?></small>
                                <?php else: ?>
                                    ---
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($guest['check_status'] == 'checked_in'): ?>
                                    <span class="badge bg-success">✓ Đã check-in</span>
                                    <?php if ($guest['check_in_time']): ?>
                                        <br><small><?= date('H:i', strtotime($guest['check_in_time'])) ?></small>
                                    <?php endif; ?>
                                <?php elseif ($guest['check_status'] == 'no_show'): ?>
                                    <span class="badge bg-danger">✗ Không đến</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">⏳ Chờ check-in</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <small><?= htmlspecialchars($guest['booking_code']) ?></small><br>
                                <small><?= htmlspecialchars($guest['booker_name']) ?></small>
                            </td>
                            <td>
                                <?php if (!empty($guest['special_requests'])): ?>
                                    <button class="btn btn-sm btn-outline-dark" 
                                            data-toggle="collapse" 
                                            data-target="#note_<?= $guest['guest_id'] ?>">
                                        <i class="fas fa-eye"></i> Xem
                                    </button>
                                    <div class="collapse mt-2" id="note_<?= $guest['guest_id'] ?>">
                                        <div class="card card-body p-2">
                                            <small><?= nl2br(htmlspecialchars($guest['special_requests'])) ?></small>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    ---
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Footer Info -->
    <div class="mt-4 p-3 bg-light rounded">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Ngày xuất danh sách:</strong> <?= date('d/m/Y H:i') ?></p>
            </div>
            <div class="col-md-6 text-right">
                <p><strong>Người xuất:</strong> <?= $_SESSION['full_name'] ?? 'Admin' ?></p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .no-print {
        display: none !important;
    }
    .card-header {
        background-color: #f8f9fa !important;
        color: #000 !important;
        border-bottom: 2px solid #000;
    }
    table {
        border: 1px solid #000;
    }
    th {
        background-color: #e9ecef !important;
        color: #000 !important;
    }
}
</style>

<?php require_once './views/admin/footer.php'; ?>