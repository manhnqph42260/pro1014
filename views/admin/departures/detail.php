<?php require_once './views/admin/header.php'; ?>

<!-- Thêm các thư viện cần thiết -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="container-fluid">
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>
    
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết Lịch Khởi Hành</h1>
        <div>
            <a href="?act=admin_departures" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <a href="?act=admin_departures_edit&id=<?= $departure['departure_id'] ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Sửa Lịch
            </a>
        </div>
    </div>
    
    <!-- Departure Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin Lịch Khởi Hành</h6>
            <span class="badge badge-<?= $departure['status'] == 'confirmed' ? 'success' : 
                                        ($departure['status'] == 'scheduled' ? 'warning' : 
                                        ($departure['status'] == 'completed' ? 'info' : 'secondary')) ?>">
                <?= $departure['status'] == 'scheduled' ? 'Đã lên lịch' : 
                   ($departure['status'] == 'confirmed' ? 'Đã xác nhận' : 
                   ($departure['status'] == 'completed' ? 'Đã hoàn thành' : 'Đã hủy')) ?>
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-primary"><?= htmlspecialchars($tour['tour_name']) ?></h5>
                    <p class="text-muted">Mã Tour: <?= htmlspecialchars($tour['tour_code']) ?></p>
                    
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Ngày khởi hành:</th>
                            <td><?= date('d/m/Y', strtotime($departure['departure_date'])) ?></td>
                        </tr>
                        <tr>
                            <th>Giờ khởi hành:</th>
                            <td><?= date('H:i', strtotime($departure['departure_time'])) ?></td>
                        </tr>
                        <tr>
                            <th>Điểm tập trung:</th>
                            <td><?= !empty($departure['meeting_point']) ? htmlspecialchars($departure['meeting_point']) : 'Chưa cập nhật' ?></td>
                        </tr>
                        <tr>
                            <th>Ngày kết thúc:</th>
                            <td><?= !empty($departure['end_date']) ? date('d/m/Y', strtotime($departure['end_date'])) : 'Chưa cập nhật' ?></td>
                        </tr>
                        <tr>
                            <th>Số chỗ:</th>
                            <td>
                                <span class="badge badge-info">Tổng: <?= $departure['expected_slots'] ?></span>
                                <span class="badge badge-success">Đã đặt: <?= $booked_slots ?></span>
                                <span class="badge badge-warning">Còn trống: <?= $available_slots ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-primary">Thông tin giá</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Giá người lớn:</th>
                            <td class="text-success font-weight-bold"><?= number_format($departure['price_adult'], 0, ',', '.') ?> VNĐ</td>
                        </tr>
                        <tr>
                            <th>Giá trẻ em:</th>
                            <td class="text-info"><?= number_format($departure['price_child'], 0, ',', '.') ?> VNĐ</td>
                        </tr>
                        <tr>
                            <th>Ghi chú vận hành:</th>
                            <td><?= !empty($departure['operational_notes']) ? nl2br(htmlspecialchars($departure['operational_notes'])) : 'Không có' ?></td>
                        </tr>
                    </table>
                    
                    <?php if (!empty($departure['notes'])): ?>
                    <h6 class="font-weight-bold text-primary mt-3">Ghi chú bổ sung</h6>
                    <div class="alert alert-info">
                        <?= nl2br(htmlspecialchars($departure['notes'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs Navigation với xử lý URL parameters -->
    <?php
    // Lấy tab active từ URL, mặc định là assignments
    $active_tab = $_GET['tab'] ?? 'assignments';
    ?>
    
    <ul class="nav nav-tabs mb-4" id="departureTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'assignments' ? 'active' : '' ?>" 
               href="?act=admin_departure_detail&id=<?= $departure['departure_id'] ?>&tab=assignments">
                <i class="fas fa-users"></i> Phân bổ Nhân sự
                <span class="badge badge-primary"><?= count($assignments) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'resources' ? 'active' : '' ?>" 
               href="?act=admin_departure_detail&id=<?= $departure['departure_id'] ?>&tab=resources">
                <i class="fas fa-concierge-bell"></i> Dịch vụ & Tài nguyên
                <span class="badge badge-success"><?= count($resources) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'bookings' ? 'active' : '' ?>" 
               href="?act=admin_departure_detail&id=<?= $departure['departure_id'] ?>&tab=bookings">
                <i class="fas fa-calendar-check"></i> Booking
                <span class="badge badge-info"><?= count($bookingList) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $active_tab == 'checklist' ? 'active' : '' ?>" 
               href="?act=admin_departure_detail&id=<?= $departure['departure_id'] ?>&tab=checklist">
                <i class="fas fa-tasks"></i> Checklist
            </a>
        </li>
    </ul>
    
    <!-- Tab Content hiển thị theo active_tab -->
    <div class="tab-content">
        
        <!-- Tab 1: Phân bổ Nhân sự -->
        <div class="tab-pane <?= $active_tab == 'assignments' ? 'show active' : '' ?>" id="assignments">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Phân bổ Nhân sự</h6>
                    <a href="?act=admin_departure_add_assignment&departure_id=<?= $departure['departure_id'] ?>" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Thêm Nhân sự
                    </a>
                </div>
                <div class="card-body">
                    
                    <!-- Assignment Stats -->
                    <?php if (!empty($assignmentStats)): ?>
                    <div class="row mb-4">
                        <?php foreach ($assignmentStats as $stat): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-<?= $stat['assignment_type'] == 'guide' ? 'primary' : 
                                                        ($stat['assignment_type'] == 'driver' ? 'warning' : 
                                                        ($stat['assignment_type'] == 'staff' ? 'success' : 'info')) ?> shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-<?= $stat['assignment_type'] == 'guide' ? 'primary' : 
                                                                                       ($stat['assignment_type'] == 'driver' ? 'warning' : 
                                                                                       ($stat['assignment_type'] == 'staff' ? 'success' : 'info')) ?> text-uppercase mb-1">
                                                <?= $stat['assignment_type'] == 'guide' ? 'Hướng dẫn viên' : 
                                                   ($stat['assignment_type'] == 'driver' ? 'Tài xế' : 
                                                   ($stat['assignment_type'] == 'staff' ? 'Nhân viên' : 'Khác')) ?>
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                        <?= $stat['total'] ?> người
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <?php if ($stat['total'] > 0): ?>
                                                        <div class="progress-bar bg-<?= $stat['assignment_type'] == 'guide' ? 'primary' : 
                                                                                     ($stat['assignment_type'] == 'driver' ? 'warning' : 
                                                                                     ($stat['assignment_type'] == 'staff' ? 'success' : 'info')) ?>" 
                                                             role="progressbar" 
                                                             style="width: <?= ($stat['confirmed'] / $stat['total']) * 100 ?>%">
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge badge-success"><?= $stat['confirmed'] ?> xác nhận</span>
                                                <span class="badge badge-warning"><?= $stat['pending'] ?> chờ</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Assignments Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Vai trò</th>
                                    <th>Người phụ trách</th>
                                    <th>Liên hệ</th>
                                    <th>Ngày phân công</th>
                                    <th>Trạng thái</th>
                                    <th>Ghi chú</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($assignments)): ?>
                                    <?php $i = 1; foreach ($assignments as $assign): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <span class="badge badge-<?= $assign['assignment_type'] == 'guide' ? 'primary' : 
                                                                      ($assign['assignment_type'] == 'driver' ? 'warning' : 
                                                                      ($assign['assignment_type'] == 'staff' ? 'success' : 'info')) ?>">
                                                <?= $assign['role'] ?>
                                            </span>
                                            <small class="d-block text-muted"><?= $assign['assignment_type'] ?></small>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($assign['person_name']) ?></strong>
                                            <?php if ($assign['assignment_type'] == 'guide' && !empty($assign['person_id'])): ?>
                                            <br>
                                            <small class="text-primary">
                                                <a href="?act=admin_guides_view&id=<?= $assign['person_id'] ?>">
                                                    <i class="fas fa-external-link-alt"></i> Xem HDV
                                                </a>
                                            </small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($assign['contact_info']) ? htmlspecialchars($assign['contact_info']) : 'N/A' ?></td>
                                        <td>
                                            <?= !empty($assign['assignment_date']) ? date('d/m/Y', strtotime($assign['assignment_date'])) : 'N/A' ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_badges = [
                                                'pending' => 'warning',
                                                'confirmed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge badge-<?= $status_badges[$assign['status']] ?? 'secondary' ?>">
                                                <?= $assign['status'] == 'pending' ? 'Chờ xác nhận' : 
                                                   ($assign['status'] == 'confirmed' ? 'Đã xác nhận' : 'Đã hủy') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= !empty($assign['assignment_notes']) ? 
                                                '<small>' . nl2br(htmlspecialchars(substr($assign['assignment_notes'], 0, 100))) . 
                                                (strlen($assign['assignment_notes']) > 100 ? '...' : '') . '</small>' : 
                                                '-' ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <?php if ($assign['status'] != 'confirmed'): ?>
                                                <a href="?act=admin_departure_update_assignment_status&assignment_id=<?= $assign['assignment_id'] ?>&departure_id=<?= $departure['departure_id'] ?>&status=confirmed" 
                                                   class="btn btn-success" title="Xác nhận">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($assign['status'] != 'cancelled'): ?>
                                                <a href="?act=admin_departure_update_assignment_status&assignment_id=<?= $assign['assignment_id'] ?>&departure_id=<?= $departure['departure_id'] ?>&status=cancelled" 
                                                   class="btn btn-warning" title="Hủy">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                                <?php endif; ?>
                                                
                                                <a href="?act=admin_departure_delete_assignment&assignment_id=<?= $assign['assignment_id'] ?>&departure_id=<?= $departure['departure_id'] ?>" 
                                                   class="btn btn-danger" title="Xóa"
                                                   onclick="return confirm('Xóa phân bổ này?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-2x mb-3"></i><br>
                                            Chưa có phân bổ nhân sự nào
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab 2: Dịch vụ & Tài nguyên -->
        <div class="tab-pane <?= $active_tab == 'resources' ? 'show active' : '' ?>" id="resources">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Dịch vụ & Tài nguyên</h6>
                    <a href="?act=admin_departure_add_resource&departure_id=<?= $departure['departure_id'] ?>" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Thêm Dịch vụ
                    </a>
                </div>
                <div class="card-body">
                    
                    <!-- Resource Stats -->
                    <?php if (!empty($resourceStats)): ?>
                    <div class="row mb-4">
                        <?php foreach ($resourceStats as $stat): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-<?= $stat['resource_type'] == 'transport' ? 'warning' : 
                                                        ($stat['resource_type'] == 'accommodation' ? 'primary' : 
                                                        ($stat['resource_type'] == 'meal' ? 'success' : 
                                                        ($stat['resource_type'] == 'ticket' ? 'info' : 'secondary'))) ?> shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-<?= $stat['resource_type'] == 'transport' ? 'warning' : 
                                                                                       ($stat['resource_type'] == 'accommodation' ? 'primary' : 
                                                                                       ($stat['resource_type'] == 'meal' ? 'success' : 
                                                                                       ($stat['resource_type'] == 'ticket' ? 'info' : 'secondary'))) ?> text-uppercase mb-1">
                                                <?= $stat['resource_type'] == 'transport' ? 'Vận chuyển' : 
                                                   ($stat['resource_type'] == 'accommodation' ? 'Lưu trú' : 
                                                   ($stat['resource_type'] == 'meal' ? 'Ăn uống' : 
                                                   ($stat['resource_type'] == 'ticket' ? 'Vé tham quan' : 'Khác'))) ?>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $stat['total'] ?> dịch vụ
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge badge-success"><?= $stat['confirmed'] ?> xác nhận</span>
                                                <br>
                                                <span class="text-xs">Tổng chi phí: <?= number_format($stat['total_cost'], 0, ',', '.') ?> VNĐ</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Resources Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Loại dịch vụ</th>
                                    <th>Tên dịch vụ</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Số lượng</th>
                                    <th>Ngày/giờ</th>
                                    <th>Địa điểm</th>
                                    <th>Trạng thái</th>
                                    <th>Chi phí</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($resources)): ?>
                                    <?php $i = 1; foreach ($resources as $resource): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <span class="badge badge-<?= $resource['resource_type'] == 'transport' ? 'warning' : 
                                                                      ($resource['resource_type'] == 'accommodation' ? 'primary' : 
                                                                      ($resource['resource_type'] == 'meal' ? 'success' : 
                                                                      ($resource['resource_type'] == 'ticket' ? 'info' : 'secondary'))) ?>">
                                                <?= $resource['resource_type'] == 'transport' ? '🚌 Vận chuyển' : 
                                                   ($resource['resource_type'] == 'accommodation' ? '🏨 Lưu trú' : 
                                                   ($resource['resource_type'] == 'meal' ? '🍽️ Ăn uống' : 
                                                   ($resource['resource_type'] == 'ticket' ? '🎫 Vé tham quan' : '📋 Khác'))) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($resource['service_name']) ?></strong>
                                            <?php if (!empty($resource['confirmation_number'])): ?>
                                            <br>
                                            <small class="text-muted">Mã xác nhận: <?= htmlspecialchars($resource['confirmation_number']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($resource['provider_name']) ? htmlspecialchars($resource['provider_name']) : 'N/A' ?></td>
                                        <td>
                                            <?= $resource['quantity'] ?> <?= $resource['unit'] ?>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($resource['schedule_date'])) ?>
                                            <?php if (!empty($resource['schedule_time'])): ?>
                                            <br>
                                            <small><?= date('H:i', strtotime($resource['schedule_time'])) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($resource['location']) ? htmlspecialchars($resource['location']) : 'N/A' ?></td>
                                        <td>
                                            <?php
                                            $status_badges = [
                                                'pending' => 'warning',
                                                'confirmed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge badge-<?= $status_badges[$resource['status']] ?? 'secondary' ?>">
                                                <?= $resource['status'] == 'pending' ? 'Chờ xác nhận' : 
                                                   ($resource['status'] == 'confirmed' ? 'Đã xác nhận' : 'Đã hủy') ?>
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <?php if ($resource['total_price'] > 0): ?>
                                            <span class="font-weight-bold text-success">
                                                <?= number_format($resource['total_price'], 0, ',', '.') ?> VNĐ
                                            </span>
                                            <?php if ($resource['unit_price'] > 0 && $resource['quantity'] > 1): ?>
                                            <br>
                                            <small class="text-muted">(<?= number_format($resource['unit_price'], 0, ',', '.') ?>/<?= $resource['unit'] ?>)</small>
                                            <?php endif; ?>
                                            <?php else: ?>
                                            <span class="text-muted">Miễn phí</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-info" title="Xem chi tiết"
                                                        onclick="showResourceDetail(<?= htmlspecialchars(json_encode($resource)) ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <?php if ($resource['status'] != 'confirmed'): ?>
                                                <a href="?act=admin_departure_update_resource_status&resource_id=<?= $resource['resource_id'] ?>&departure_id=<?= $departure['departure_id'] ?>&status=confirmed" 
                                                   class="btn btn-success" title="Xác nhận">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($resource['status'] != 'cancelled'): ?>
                                                <a href="?act=admin_departure_update_resource_status&resource_id=<?= $resource['resource_id'] ?>&departure_id=<?= $departure['departure_id'] ?>&status=cancelled" 
                                                   class="btn btn-warning" title="Hủy">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                                <?php endif; ?>
                                                
                                                <a href="?act=admin_departure_delete_resource&resource_id=<?= $resource['resource_id'] ?>&departure_id=<?= $departure['departure_id'] ?>" 
                                                   class="btn btn-danger" title="Xóa"
                                                   onclick="return confirm('Xóa dịch vụ này?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="fas fa-concierge-bell fa-2x mb-3"></i><br>
                                            Chưa có dịch vụ nào được đặt
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab 3: Booking -->
        <div class="tab-pane <?= $active_tab == 'bookings' ? 'show active' : '' ?>" id="bookings">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách Booking</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Mã Booking</th>
                                    <th>Khách hàng</th>
                                    <th>SĐT</th>
                                    <th>Loại</th>
                                    <th>Số khách</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bookingList)): ?>
                                    <?php foreach ($bookingList as $booking): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($booking['booking_code']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($booking['customer_phone']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $booking['booking_type'] == 'individual' ? 'info' : 'primary' ?>">
                                                <?= $booking['booking_type'] == 'individual' ? 'Cá nhân' : 'Đoàn' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success"><?= $booking['total_guests'] ?> khách</span>
                                            <small class="d-block">
                                                Người lớn: <?= $booking['adult_count'] ?>,
                                                Trẻ em: <?= $booking['child_count'] ?>
                                            </small>
                                        </td>
                                        <td class="text-right font-weight-bold text-success">
                                            <?= number_format($booking['total_amount'], 0, ',', '.') ?> VNĐ
                                        </td>
                                        <td>
                                            <?php
                                            $booking_status = [
                                                'pending' => ['class' => 'warning', 'text' => 'Chờ xác nhận'],
                                                'confirmed' => ['class' => 'success', 'text' => 'Đã xác nhận'],
                                                'cancelled' => ['class' => 'danger', 'text' => 'Đã hủy'],
                                                'completed' => ['class' => 'info', 'text' => 'Hoàn tất']
                                            ];
                                            $status = $booking['status'];
                                            ?>
                                            <span class="badge badge-<?= $booking_status[$status]['class'] ?? 'secondary' ?>">
                                                <?= $booking_status[$status]['text'] ?? $status ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($booking['booked_at'])) ?></td>
                                        <td>
                                            <a href="?act=admin_bookings_view&id=<?= $booking['booking_id'] ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-times fa-2x mb-3"></i><br>
                                            Chưa có booking nào
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab 4: Checklist -->
<div class="tab-pane <?= $active_tab == 'checklist' ? 'show active' : '' ?>" id="checklist">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Checklist Chuẩn bị</h6>
            <a href="?act=admin_add_checklist&departure_id=<?= $departure['departure_id'] ?>" 
               class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Thêm công việc
            </a>
        </div>
        <div class="card-body">
            
            <!-- Checklist Stats -->
            <?php if (!empty($checklistStats)): ?>
            <div class="row mb-4">
                <?php 
                $category_names = [
                    'preparation' => ['name' => 'Chuẩn bị', 'color' => 'primary', 'icon' => '📋'],
                    'document' => ['name' => 'Tài liệu', 'color' => 'info', 'icon' => '📄'],
                    'equipment' => ['name' => 'Thiết bị', 'color' => 'warning', 'icon' => '🎒'],
                    'communication' => ['name' => 'Liên lạc', 'color' => 'success', 'icon' => '📱'],
                    'transport' => ['name' => 'Vận chuyển', 'color' => 'danger', 'icon' => '🚌'],
                    'accommodation' => ['name' => 'Lưu trú', 'color' => 'secondary', 'icon' => '🏨'],
                    'meal' => ['name' => 'Ăn uống', 'color' => 'dark', 'icon' => '🍽️'],
                    'other' => ['name' => 'Khác', 'color' => 'light', 'icon' => '📝']
                ];
                ?>
                <?php foreach ($checklistStats as $stat): ?>
                <?php 
                $category = $category_names[$stat['category']] ?? ['name' => $stat['category'], 'color' => 'secondary', 'icon' => '📋'];
                $total = $stat['total'];
                $completed = $stat['completed'];
                $progress = $total > 0 ? ($completed / $total) * 100 : 0;
                ?>
                <div class="col-md-3 mb-3">
                    <div class="card border-left-<?= $category['color'] ?> shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-<?= $category['color'] ?> text-uppercase mb-1">
                                        <?= $category['icon'] ?> <?= $category['name'] ?>
                                    </div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                <?= $completed ?>/<?= $total ?>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-<?= $category['color'] ?>" 
                                                     role="progressbar" 
                                                     style="width: <?= $progress ?>%"
                                                     aria-valuenow="<?= $progress ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <?= number_format($progress, 0) ?>% hoàn thành
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Upcoming Deadlines -->
            <?php if (!empty($upcomingDeadlines)): ?>
            <div class="alert alert-warning mb-4">
                <h6><i class="fas fa-exclamation-triangle"></i> Công việc sắp đến hạn (3 ngày tới):</h6>
                <ul class="mb-0">
                    <?php foreach ($upcomingDeadlines as $item): ?>
                    <li>
                        <strong><?= htmlspecialchars($item['item_name']) ?></strong> 
                        - Hạn: <?= date('d/m/Y H:i', strtotime($item['deadline'])) ?>
                        <?php if ($item['assigned_to']): ?>
                        <span class="badge badge-info"><?= htmlspecialchars($item['assigned_to']) ?></span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Checklist Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="40">STT</th>
                            <th>Danh mục</th>
                            <th>Công việc</th>
                            <th>Người PT</th>
                            <th>Hạn chót</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($checklistItems)): ?>
                            <?php $i = 1; foreach ($checklistItems as $item): ?>
                            <?php 
                            $status_info = [
                                'pending' => ['class' => 'warning', 'icon' => '⏳', 'text' => 'Chưa bắt đầu'],
                                'in_progress' => ['class' => 'info', 'icon' => '🚀', 'text' => 'Đang thực hiện'],
                                'completed' => ['class' => 'success', 'icon' => '✅', 'text' => 'Hoàn thành'],
                                'cancelled' => ['class' => 'danger', 'icon' => '❌', 'text' => 'Đã hủy']
                            ];
                            $status = $item['status'];
                            $status_class = $status_info[$status]['class'] ?? 'secondary';
                            $status_icon = $status_info[$status]['icon'] ?? '❓';
                            $status_text = $status_info[$status]['text'] ?? $status;
                            
                            $category_info = $category_names[$item['category']] ?? ['name' => $item['category'], 'color' => 'secondary', 'icon' => '📋'];
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td>
                                    <span class="badge badge-<?= $category_info['color'] ?>">
                                        <?= $category_info['icon'] ?> <?= $category_info['name'] ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($item['item_name']) ?></strong>
                                </td>
                                <td>
                                    <?php if ($item['assigned_to']): ?>
                                    <span class="badge badge-light"><?= htmlspecialchars($item['assigned_to']) ?></span>
                                    <?php else: ?>
                                    <span class="text-muted">Chưa assign</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['deadline']): ?>
                                        <?= date('d/m/Y', strtotime($item['deadline'])) ?>
                                        <br>
                                        <small><?= date('H:i', strtotime($item['deadline'])) ?></small>
                                        <?php 
                                        // Kiểm tra deadline có sắp đến hay quá hạn không
                                        $deadline = strtotime($item['deadline']);
                                        $now = time();
                                        $diff = $deadline - $now;
                                        
                                        if ($diff < 0 && $item['status'] != 'completed' && $item['status'] != 'cancelled') {
                                            echo '<br><span class="badge badge-danger">QUÁ HẠN</span>';
                                        } elseif ($diff < 86400 && $item['status'] != 'completed' && $item['status'] != 'cancelled') {
                                            echo '<br><span class="badge badge-warning">SẮP HẾT HẠN</span>';
                                        }
                                        ?>
                                    <?php else: ?>
                                        <span class="text-muted">Không có</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $status_class ?>">
                                        <?= $status_icon ?> <?= $status_text ?>
                                    </span>
                                    <?php if ($item['completed_at']): ?>
                                    <br>
                                    <small>Hoàn thành: <?= date('d/m/Y H:i', strtotime($item['completed_at'])) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['completion_notes']): ?>
                                    <small><?= nl2br(htmlspecialchars(substr($item['completion_notes'], 0, 100))) ?>
                                    <?php if (strlen($item['completion_notes']) > 100): ?>...<?php endif; ?>
                                    </small>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Nút chuyển trạng thái -->
                                        <?php if ($item['status'] != 'completed'): ?>
                                        <a href="?act=admin_update_checklist_status&item_id=<?= $item['item_id'] ?>&departure_id=<?= $departure['departure_id'] ?>&status=completed" 
                                           class="btn btn-success" title="Đánh dấu hoàn thành">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($item['status'] != 'in_progress' && $item['status'] != 'completed'): ?>
                                        <a href="?act=admin_update_checklist_status&item_id=<?= $item['item_id'] ?>&departure_id=<?= $departure['departure_id'] ?>&status=in_progress" 
                                           class="btn btn-info" title="Bắt đầu thực hiện">
                                            <i class="fas fa-play"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($item['status'] != 'cancelled'): ?>
                                        <a href="?act=admin_update_checklist_status&item_id=<?= $item['item_id'] ?>&departure_id=<?= $departure['departure_id'] ?>&status=cancelled" 
                                           class="btn btn-warning" title="Hủy công việc">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <a href="?act=admin_delete_checklist&item_id=<?= $item['item_id'] ?>&departure_id=<?= $departure['departure_id'] ?>" 
                                           class="btn btn-danger" title="Xóa"
                                           onclick="return confirm('Xóa công việc này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-tasks fa-2x mb-3"></i><br>
                                    Chưa có công việc checklist nào
                                    <br>
                                    <a href="?act=admin_add_checklist&departure_id=<?= $departure['departure_id'] ?>" 
                                       class="btn btn-primary mt-2">
                                        <i class="fas fa-plus"></i> Thêm công việc đầu tiên
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Quick Status Update Form -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bolt"></i> Cập nhật nhanh trạng thái</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($checklistItems)): ?>
                    <form method="POST" action="?act=admin_update_checklist_status" class="form-inline">
                        <input type="hidden" name="departure_id" value="<?= $departure['departure_id'] ?>">
                        
                        <div class="form-group mr-3">
                            <label for="quick_item_id" class="mr-2">Công việc:</label>
                            <select class="form-control" id="quick_item_id" name="item_id" required>
                                <option value="">-- Chọn công việc --</option>
                                <?php foreach ($checklistItems as $item): ?>
                                    <?php if ($item['status'] != 'completed' && $item['status'] != 'cancelled'): ?>
                                    <option value="<?= $item['item_id'] ?>">
                                        <?= htmlspecialchars($item['item_name']) ?> 
                                        (<?= $status_info[$item['status']]['text'] ?? $item['status'] ?>)
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group mr-3">
                            <label for="quick_status" class="mr-2">Trạng thái mới:</label>
                            <select class="form-control" id="quick_status" name="status" required>
                                <option value="in_progress">🚀 Đang thực hiện</option>
                                <option value="completed">✅ Hoàn thành</option>
                                <option value="cancelled">❌ Hủy</option>
                            </select>
                        </div>
                        
                        <div class="form-group mr-3">
                            <label for="quick_notes" class="mr-2">Ghi chú:</label>
                            <input type="text" class="form-control" id="quick_notes" name="completion_notes" 
                                   placeholder="Ghi chú ngắn...">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Cập nhật
                        </button>
                    </form>
                    <?php else: ?>
                    <p class="text-muted mb-0">Không có công việc nào để cập nhật nhanh.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<!-- Modal xem chi tiết resource -->
<div class="modal fade" id="resourceDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết Dịch vụ</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="resourceDetailContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
function showResourceDetail(resource) {
    let html = `
        <h6>${resource.service_name}</h6>
        <hr>
        
        <div class="row">
            <div class="col-md-6">
                <p><strong>Loại dịch vụ:</strong><br>
                <span class="badge badge-${resource.resource_type === 'transport' ? 'warning' : 
                                          resource.resource_type === 'accommodation' ? 'primary' : 
                                          resource.resource_type === 'meal' ? 'success' : 
                                          resource.resource_type === 'ticket' ? 'info' : 'secondary'}">
                    ${resource.resource_type === 'transport' ? 'Vận chuyển' : 
                     resource.resource_type === 'accommodation' ? 'Lưu trú' : 
                     resource.resource_type === 'meal' ? 'Ăn uống' : 
                     resource.resource_type === 'ticket' ? 'Vé tham quan' : 'Khác'}
                </span></p>
                
                <p><strong>Nhà cung cấp:</strong><br>
                ${resource.provider_name || 'N/A'}</p>
                
                <p><strong>Số lượng:</strong><br>
                ${resource.quantity} ${resource.unit || ''}</p>
                
                <p><strong>Chi phí:</strong><br>
                ${resource.total_price > 0 ? 
                    '<span class="text-success font-weight-bold">' + new Intl.NumberFormat('vi-VN').format(resource.total_price) + ' VNĐ</span>' : 
                    'Miễn phí'}
                </p>
            </div>
            
            <div class="col-md-6">
                <p><strong>Ngày/giờ:</strong><br>
                ${new Date(resource.schedule_date).toLocaleDateString('vi-VN')}
                ${resource.schedule_time ? '<br>' + resource.schedule_time.substring(0,5) : ''}</p>
                
                <p><strong>Địa điểm:</strong><br>
                ${resource.location || 'N/A'}</p>
                
                <p><strong>Người liên hệ:</strong><br>
                ${resource.contact_person || 'N/A'}<br>
                ${resource.contact_info ? '<small>' + resource.contact_info + '</small>' : ''}</p>
                
                <p><strong>Trạng thái:</strong><br>
                <span class="badge badge-${resource.status === 'pending' ? 'warning' : 
                                          resource.status === 'confirmed' ? 'success' : 'danger'}">
                    ${resource.status === 'pending' ? 'Chờ xác nhận' : 
                     resource.status === 'confirmed' ? 'Đã xác nhận' : 'Đã hủy'}
                </span></p>
            </div>
        </div>
        
        <hr>
        
        <p><strong>Mã xác nhận:</strong><br>
        ${resource.confirmation_number || 'Chưa có'}</p>
        
        <p><strong>Ghi chú:</strong><br>
        ${resource.resource_notes ? resource.resource_notes.replace(/\n/g, '<br>') : 'Không có ghi chú'}</p>
    `;
    
    document.getElementById('resourceDetailContent').innerHTML = html;
    $('#resourceDetailModal').modal('show');
}

function addChecklistItem() {
    alert('Tính năng đang được phát triển!');
}
</script>

<style>
.border-left-primary { border-left: .25rem solid #4e73df!important; }
.border-left-success { border-left: .25rem solid #1cc88a!important; }
.border-left-info { border-left: .25rem solid #36b9cc!important; }
.border-left-warning { border-left: .25rem solid #f6c23e!important; }
.border-left-danger { border-left: .25rem solid #e74a3b!important; }
.border-left-secondary { border-left: .25rem solid #858796!important; }
</style>

<?php require_once './views/admin/footer.php'; ?>