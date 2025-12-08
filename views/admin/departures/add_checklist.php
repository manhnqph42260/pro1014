<?php
// Kiểm tra admin đã đăng nhập
if (!isset($_SESSION['admin_id'])) {
    header("Location: ?act=admin_login");
    exit();
}

$departure_id = $_GET['departure_id'] ?? 0;
$page_title = "Thêm Công việc Checklist";
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
            background: linear-gradient(135deg, #42e695 0%, #3bb2b8 100%);
            color: white;
        }
        .btn-custom {
            background: linear-gradient(135deg, #42e695 0%, #3bb2b8 100%);
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, #3bb2b8 0%, #42e695 100%);
            color: white;
        }
        .form-control:focus {
            border-color: #3bb2b8;
            box-shadow: 0 0 0 0.2rem rgba(59, 178, 184, 0.25);
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
                <li class="breadcrumb-item active">Thêm checklist</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><i class="fas fa-tasks"></i> Thêm Công việc Checklist</h1>
            <a href="?act=admin_departure_detail&id=<?php echo $departure_id; ?>&tab=checklist" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <!-- Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin công việc</h5>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="?act=admin_add_checklist&departure_id=<?php echo $departure_id; ?>">
                    <input type="hidden" name="departure_id" value="<?php echo $departure_id; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Danh mục *</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <option value="preparation">📋 Chuẩn bị</option>
                                    <option value="document">📄 Tài liệu</option>
                                    <option value="equipment">🎒 Thiết bị</option>
                                    <option value="communication">📱 Liên lạc</option>
                                    <option value="transport">🚌 Vận chuyển</option>
                                    <option value="accommodation">🏨 Lưu trú</option>
                                    <option value="meal">🍽️ Ăn uống</option>
                                    <option value="other">📝 Khác</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="item_name">Tên công việc *</label>
                                <input type="text" class="form-control" id="item_name" name="item_name" 
                                       placeholder="VD: In vé tham quan, chuẩn bị loa tour, kiểm tra xe..." required>
                            </div>

                            <div class="form-group">
                                <label for="assigned_to">Người phụ trách</label>
                                <select class="form-control" id="assigned_to" name="assigned_to">
                                    <option value="">-- Chọn người phụ trách --</option>
                                    <?php if (!empty($admins)): ?>
                                        <?php foreach ($admins as $admin): ?>
                                            <option value="<?php echo htmlspecialchars($admin['full_name'] ?: $admin['username']); ?>">
                                                <?php echo htmlspecialchars($admin['full_name'] ?: $admin['username']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="HDV">Hướng dẫn viên</option>
                                    <option value="Tài xế">Tài xế</option>
                                    <option value="Đội vận hành">Đội vận hành</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="deadline">Hạn chót (ngày)</label>
                                        <input type="date" class="form-control" id="deadline" name="deadline">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="deadline_time">Giờ</label>
                                        <input type="time" class="form-control" id="deadline_time" name="deadline_time" value="17:00">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status">Trạng thái *</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="pending" selected>⏳ Chưa bắt đầu</option>
                                    <option value="in_progress">🚀 Đang thực hiện</option>
                                    <option value="completed">✅ Hoàn thành</option>
                                    <option value="cancelled">❌ Đã hủy</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="completion_notes">Ghi chú hoàn thành</label>
                                <textarea class="form-control" id="completion_notes" name="completion_notes" 
                                          rows="3" placeholder="Ghi chú về tiến độ, kết quả..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-custom btn-lg">
                            <i class="fas fa-save"></i> Lưu Công việc
                        </button>
                        <a href="?act=admin_departure_detail&id=<?php echo $departure_id; ?>&tab=checklist" 
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
            // Set min date cho deadline là hôm nay
            var today = new Date().toISOString().split('T')[0];
            $('#deadline').attr('min', today);
            
            // Auto fill deadline nếu không có giá trị
            if (!$('#deadline').val()) {
                $('#deadline').val(today);
            }
        });
    </script>
</body>
</html>