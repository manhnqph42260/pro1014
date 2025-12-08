<?php
$pageTitle = "Chỉnh sửa thông tin phòng";
require_once './views/admin/header.php';
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-edit"></i> <?= $pageTitle ?>
            </h6>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Khách hàng</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($room_info['full_name']) ?> (<?= $room_info['booking_code'] ?>)" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tên khách sạn *</label>
                            <input type="text" name="hotel_name" class="form-control" 
                                   value="<?= htmlspecialchars($room_info['hotel_name']) ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Số phòng *</label>
                            <input type="text" name="room_number" class="form-control" 
                                   value="<?= htmlspecialchars($room_info['room_number']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Loại phòng</label>
                            <select name="room_type" class="form-control">
                                <option value="single" <?= $room_info['room_type'] == 'single' ? 'selected' : '' ?>>Phòng đơn</option>
                                <option value="double" <?= $room_info['room_type'] == 'double' ? 'selected' : '' ?>>Phòng đôi</option>
                                <option value="triple" <?= $room_info['room_type'] == 'triple' ? 'selected' : '' ?>>Phòng ba</option>
                                <option value="family" <?= $room_info['room_type'] == 'family' ? 'selected' : '' ?>>Phòng gia đình</option>
                                <option value="suite" <?= $room_info['room_type'] == 'suite' ? 'selected' : '' ?>>Suite</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Ngày check-in</label>
                            <input type="date" name="check_in_date" class="form-control" 
                                   value="<?= $room_info['check_in_date'] ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ngày check-out</label>
                            <input type="date" name="check_out_date" class="form-control" 
                                   value="<?= $room_info['check_out_date'] ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($room_info['notes']) ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="?act=admin_guest_management&departure_id=<?= $departure_id ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once './views/admin/footer.php'; ?>