<?php
$page_title = "Dashboard";
require_once 'header.php';
?>

<!-- Main Content - Không có sidebar, full width -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">Hôm nay</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Tháng này</button>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Điều chỉnh layout cho đẹp hơn -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-mountains fs-1 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="card-title mb-0"><?php echo $tour_stats['total_tours']; ?></h3>
                            <p class="card-text text-muted mb-0">Tổng số Tour</p>
                            <small class="text-muted">Đã xuất bản: <?php echo $tour_stats['published_tours']; ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-check fs-1 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="card-title mb-0"><?php echo $departure_stats['total_departures']; ?></h3>
                            <p class="card-text text-muted mb-0">Lịch khởi hành</p>
                            <small class="text-muted">Đã xác nhận: <?php echo $departure_stats['confirmed']; ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-badge fs-1 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="card-title mb-0"><?php echo $guide_stats['total_guides']; ?></h3>
                            <p class="card-text text-muted mb-0">Hướng dẫn viên</p>
                            <small class="text-muted">Đang hoạt động</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions - Điều chỉnh cho responsive hơn -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="bi bi-lightning me-2"></i>Thao tác nhanh</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="?act=admin_tours_create" class="btn btn-primary px-4">
                            <i class="bi bi-plus-circle me-1"></i>Tạo Tour mới
                        </a>
                        <a href="?act=admin_tours" class="btn btn-outline-primary px-4">
                            <i class="bi bi-list-ul me-1"></i>Quản lý Tour
                        </a>
                        <a href="#" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-calendar-plus me-1"></i>Tạo lịch trình
                        </a>
                        <a href="#" class="btn btn-outline-success px-4">
                            <i class="bi bi-person-plus me-1"></i>Thêm HDV
                        </a>
                        <a href="#" class="btn btn-outline-info px-4">
                            <i class="bi bi-graph-up me-1"></i>Báo cáo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tours và Upcoming Departures - Điều chỉnh layout -->
    <div class="row">
        <!-- Recent Tours -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-clock-history me-2"></i>Tour gần đây</span>
                        <a href="?act=admin_tours" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                    </h5>
                    <div class="list-group list-group-flush">
                        <?php if (count($recent_tours) > 0): ?>
                            <?php foreach ($recent_tours as $tour): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($tour['tour_name']); ?></h6>
                                    <small class="text-muted"><?php echo $tour['tour_code']; ?></small>
                                </div>
                                <span class="badge bg-<?php echo $tour['status'] === 'published' ? 'success' : 'warning'; ?> px-3 py-2">
                                    <?php echo $tour['status'] === 'published' ? 'Đã xuất bản' : 'Bản nháp'; ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2">Chưa có tour nào</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Departures -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-calendar-event me-2"></i>Lịch khởi hành sắp tới</span>
                        <a href="#" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                    </h5>
                    <div class="list-group list-group-flush">
                        <?php if (count($upcoming_departures) > 0): ?>
                            <?php foreach ($upcoming_departures as $departure): ?>
                            <div class="list-group-item py-3">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($departure['tour_name']); ?></h6>
                                    <small class="text-muted fw-bold"><?php echo date('d/m/Y', strtotime($departure['departure_date'])); ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Khởi hành <?php echo date('d/m/Y', strtotime($departure['departure_date'])); ?></small>
                                    <span class="badge bg-<?php echo $departure['status'] === 'confirmed' ? 'success' : 'info'; ?> px-3 py-2">
                                        <?php echo $departure['status'] === 'confirmed' ? 'Đã xác nhận' : 'Đang chờ'; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-calendar-x fs-1"></i>
                                <p class="mt-2">Không có lịch khởi hành</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'footer.php'; ?>