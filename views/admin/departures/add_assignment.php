<?php
// Kiểm tra admin đã đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header("Location: ?act=admin_login");
    exit();
}

$departure_id = $_GET['departure_id'] ?? 0;
$page_title = "Thêm Phân bổ Nhân sự";
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25);
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
                <li class="breadcrumb-item active">Thêm phân bổ</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-user-plus"></i> Thêm Phân bổ Nhân sự</h1>
            <a href="?act=admin_departure_detail&id=<?php echo $departure_id; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin phân bổ</h5>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="?act=admin_departure_add_assignment&departure_id=<?php echo $departure_id; ?>">
                    <input type="hidden" name="departure_id" value="<?php echo $departure_id; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="assignment_type">Loại phân bổ *</label>
                                <select class="form-control" id="assignment_type" name="assignment_type" required>
                                    <option value="">-- Chọn loại --</option>
                                    <option value="guide">Hướng dẫn viên</option>
                                    <option value="driver">Tài xế</option>
                                    <option value="staff">Nhân viên hỗ trợ</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="role">Vai trò/Công việc *</label>
                                <input type="text" class="form-control" id="role" name="role" 
                                       placeholder="VD: HDV chính, Tài xế, Nhân viên hỗ trợ..." required>
                            </div>

                            <div class="form-group">
                                <label for="person_name">Tên người phụ trách *</label>
                                <input type="text" class="form-control" id="person_name" name="person_name" 
                                       placeholder="Nhập họ tên đầy đủ" required>
                            </div>

                            <div class="form-group">
                                <label for="person_id">Chọn HDV (nếu có)</label>
                                <select class="form-control" id="person_id" name="person_id">
                                    <option value="">-- Chọn HDV từ danh sách --</option>
                                    <?php if (!empty($guides)): ?>
                                        <?php foreach ($guides as $guide): ?>
                                            <option value="<?php echo $guide['guide_id']; ?>" 
                                                    data-phone="<?php echo htmlspecialchars($guide['phone'] ?? ''); ?>"
                                                    data-name="<?php echo htmlspecialchars($guide['full_name']); ?>">
                                                <?php echo htmlspecialchars($guide['guide_code']); ?> - <?php echo htmlspecialchars($guide['full_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">Chỉ áp dụng khi chọn loại là "Hướng dẫn viên"</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_info">Thông tin liên hệ</label>
                                <input type="text" class="form-control" id="contact_info" name="contact_info" 
                                       placeholder="Số điện thoại, email...">
                            </div>

                            <div class="form-group">
                                <label for="status">Trạng thái *</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="pending" selected>Chờ xác nhận</option>
                                    <option value="confirmed">Đã xác nhận</option>
                                    <option value="cancelled">Đã hủy</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="assignment_date">Ngày phân công</label>
                                <input type="date" class="form-control" id="assignment_date" name="assignment_date">
                            </div>

                            <div class="form-group">
                                <label for="assignment_notes">Ghi chú</label>
                                <textarea class="form-control" id="assignment_notes" name="assignment_notes" 
                                          rows="3" placeholder="Ghi chú về phân công này..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-custom btn-lg">
                            <i class="fas fa-save"></i> Lưu Phân bổ
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
            // Khi chọn loại phân bổ là guide, hiển thị select HDV
            $('#assignment_type').change(function() {
                if ($(this).val() === 'guide') {
                    $('#person_id').prop('required', false);
                    $('label[for="person_id"]').html('Chọn HDV');
                } else {
                    $('#person_id').prop('required', false);
                    $('label[for="person_id"]').html('Chọn HDV (nếu có)');
                    $('#person_id').val('');
                }
            });

            // Khi chọn HDV từ danh sách, tự động điền thông tin
            $('#person_id').change(function() {
                if ($(this).val()) {
                    var selectedOption = $(this).find(':selected');
                    $('#person_name').val(selectedOption.data('name'));
                    $('#contact_info').val(selectedOption.data('phone') || '');
                }
            });

            // Set min date cho assignment_date là hôm nay
            var today = new Date().toISOString().split('T')[0];
            $('#assignment_date').attr('min', today);
        });
    </script>
</body>
</html>