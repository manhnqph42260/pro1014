<?php
// Kiểm tra admin đã đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header("Location: ?act=admin_login");
    exit();
}

$departure_id = $_GET['departure_id'] ?? 0;
$page_title = "Thêm Tài nguyên/Dịch vụ";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    
    <style>
        .container-fluid {
            padding: 20px;
        }
        .card {
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .btn-custom {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
            color: white;
        }
        .form-control:focus {
            border-color: #4facfe;
            box-shadow: 0 0 0 0.2rem rgba(79, 172, 254, 0.25);
        }
    </style>
</head>
<body>
    <?php require_once './views/admin/header.php'; ?>

    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="?act=admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="?act=admin_departures">Lịch khởi hành</a></li>
                <li class="breadcrumb-item"><a href="?act=admin_departure_detail&id=<?php echo $departure_id; ?>">Chi tiết</a></li>
                <li class="breadcrumb-item active">Thêm tài nguyên</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-concierge-bell"></i> Thêm Tài nguyên/Dịch vụ</h1>
            <a href="?act=admin_departure_detail&id=<?php echo $departure_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin dịch vụ</h5>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="?act=admin_departure_add_resource&departure_id=<?php echo $departure_id; ?>">
                    <input type="hidden" name="departure_id" value="<?php echo $departure_id; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="resource_type">Loại dịch vụ *</label>
                                <select class="form-control" id="resource_type" name="resource_type" required>
                                    <option value="">-- Chọn loại --</option>
                                    <option value="transport">🚌 Vận chuyển</option>
                                    <option value="accommodation">🏨 Lưu trú</option>
                                    <option value="meal">🍽️ Ăn uống</option>
                                    <option value="ticket">🎫 Vé tham quan</option>
                                    <option value="attraction">🏞️ Điểm tham quan</option>
                                    <option value="other">📋 Khác</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="service_name">Tên dịch vụ *</label>
                                <input type="text" class="form-control" id="service_name" name="service_name" 
                                       placeholder="VD: Xe 45 chỗ, Khách sạn 3 sao, Nhà hàng..." required>
                            </div>

                            <div class="form-group">
                                <label for="provider_name">Nhà cung cấp</label>
                                <input type="text" class="form-control" id="provider_name" name="provider_name" 
                                       placeholder="Tên công ty/cá nhân cung cấp">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity">Số lượng</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" 
                                               value="1" min="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unit">Đơn vị</label>
                                        <input type="text" class="form-control" id="unit" name="unit" 
                                               placeholder="VD: xe, phòng, suất...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="schedule_date">Ngày thực hiện *</label>
                                <input type="date" class="form-control" id="schedule_date" name="schedule_date" required>
                            </div>

                            <div class="form-group">
                                <label for="schedule_time">Giờ thực hiện</label>
                                <input type="time" class="form-control" id="schedule_time" name="schedule_time">
                            </div>

                            <div class="form-group">
                                <label for="location">Địa điểm</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       placeholder="Địa điểm cụ thể">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unit_price">Đơn giá (VNĐ)</label>
                                        <input type="number" class="form-control" id="unit_price" name="unit_price" 
                                               min="0" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Trạng thái *</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="pending" selected>Chờ xác nhận</option>
                                            <option value="confirmed">Đã xác nhận</option>
                                            <option value="cancelled">Đã hủy</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_person">Người liên hệ</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                       placeholder="Tên người liên hệ">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_info">Thông tin liên hệ</label>
                                <input type="text" class="form-control" id="contact_info" name="contact_info" 
                                       placeholder="SĐT, email...">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirmation_number">Mã xác nhận</label>
                                <input type="text" class="form-control" id="confirmation_number" name="confirmation_number" 
                                       placeholder="Mã booking, mã hợp đồng...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total_price">Tổng tiền (VNĐ)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="total_price_display" readonly>
                                    <input type="hidden" id="total_price" name="total_price">
                                    <div class="input-group-append">
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Tự động tính: Số lượng × Đơn giá</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="resource_notes">Ghi chú</label>
                        <textarea class="form-control" id="resource_notes" name="resource_notes" 
                                  rows="3" placeholder="Ghi chú về dịch vụ, điều khoản đặc biệt..."></textarea>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-custom btn-lg">
                            <i class="fas fa-save"></i> Lưu Dịch vụ
                        </button>
                        <a href="?act=admin_departure_detail&id=<?php echo $departure_id; ?>" 
                           class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Hủy bỏ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Format số tiền
            function formatMoney(number) {
                return new Intl.NumberFormat('vi-VN').format(number) + ' VNĐ';
            }

            // Tính tổng tiền tự động
            function calculateTotal() {
                var quantity = parseInt($('#quantity').val()) || 0;
                var unitPrice = parseInt($('#unit_price').val()) || 0;
                var total = quantity * unitPrice;
                
                $('#total_price').val(total);
                $('#total_price_display').val(formatMoney(total));
            }

            // Tính toán khi thay đổi số lượng hoặc đơn giá
            $('#quantity, #unit_price').on('input', calculateTotal);
            
            // Tính toán lần đầu
            calculateTotal();
            
            // Set min date cho schedule_date là hôm nay
            var today = new Date().toISOString().split('T')[0];
            $('#schedule_date').attr('min', today);
        });
    </script>
</body>
</html>