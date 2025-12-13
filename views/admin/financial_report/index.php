<?php
$page_title = "Báo cáo Tài chính";
require_once './views/admin/header.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-graph-up me-2"></i>Báo cáo Tài chính
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="?act=financial_dashboard" class="btn btn-outline-info me-2">
                <i class="bi bi-speedometer2 me-1"></i>Dashboard
            </a>
            <button class="btn btn-outline-success" onclick="exportReport()">
                <i class="bi bi-file-earmark-excel me-1"></i>Xuất Excel
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-filter me-2"></i>Lọc báo cáo</h5>
            <form method="GET" class="row g-3">
                <input type="hidden" name="act" value="admin_financial_report">
                
                <div class="col-md-3">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" name="start_date" 
                           value="<?php echo $filters['start_date']; ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" name="end_date" 
                           value="<?php echo $filters['end_date']; ?>">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Tour</label>
                    <select class="form-select" name="tour_id">
                        <option value="">Tất cả tour</option>
                        <?php foreach ($tours as $tour): ?>
                        <option value="<?php echo $tour['tour_id']; ?>" 
                            <?php echo ($filters['tour_id'] == $tour['tour_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tour['tour_code'] . ' - ' . $tour['tour_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <?php if ($report['success']): ?>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Doanh thu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($report['summary']['total_revenue'], 0, ',', '.'); ?> ₫
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chi phí</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($report['summary']['total_cost'], 0, ',', '.'); ?> ₫
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Lợi nhuận</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo number_format($report['summary']['total_profit'], 0, ',', '.'); ?> ₫
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up-arrow fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Biên lợi nhuận</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $report['summary']['avg_profit_margin']; ?>%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Report -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-table me-2"></i>Chi tiết theo tour
                <span class="badge bg-primary ms-2"><?php echo $report['summary']['total_tours']; ?> tour</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Tour</th>
                            <th class="text-center">Booking</th>
                            <th class="text-end">Doanh thu</th>
                            <th class="text-end">Chi phí</th>
                            <th class="text-end">Lợi nhuận</th>
                            <th class="text-center">Biên lợi nhuận</th>
                            <th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['data'] as $item): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($item['tour_code']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($item['tour_name']); ?></small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info"><?php echo $item['total_bookings']; ?></span>
                            </td>
                            <td class="text-end">
                                <?php echo number_format($item['total_revenue'], 0, ',', '.'); ?> ₫
                            </td>
                            <td class="text-end">
                                <?php echo number_format($item['total_cost'], 0, ',', '.'); ?> ₫
                            </td>
                            <td class="text-end">
                                <span class="fw-bold <?php echo $item['profit_status'] === 'profit' ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo number_format($item['profit'], 0, ',', '.'); ?> ₫
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?php echo $item['profit_margin'] >= 20 ? 'success' : ($item['profit_margin'] >= 10 ? 'warning' : 'danger'); ?>">
                                    <?php echo $item['profit_margin']; ?>%
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($item['profit'] > 0): ?>
                                <span class="badge bg-success">Có lãi</span>
                                <?php elseif ($item['profit'] < 0): ?>
                                <span class="badge bg-danger">Lỗ</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Hòa vốn</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <th>Tổng cộng</th>
                            <th class="text-center"><?php echo $report['summary']['total_bookings']; ?></th>
                            <th class="text-end"><?php echo number_format($report['summary']['total_revenue'], 0, ',', '.'); ?> ₫</th>
                            <th class="text-end"><?php echo number_format($report['summary']['total_cost'], 0, ',', '.'); ?> ₫</th>
                            <th class="text-end"><?php echo number_format($report['summary']['total_profit'], 0, ',', '.'); ?> ₫</th>
                            <th class="text-center"><?php echo $report['summary']['avg_profit_margin']; ?>%</th>
                            <th class="text-center">
                                <?php if ($report['summary']['total_profit'] > 0): ?>
                                <span class="badge bg-success">Có lãi</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Lỗ</span>
                                <?php endif; ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts and Additional Info -->
    <div class="row">
        <!-- Top Tours -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-trophy me-2"></i>Top 5 tour lợi nhuận cao nhất
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tour</th>
                                    <th class="text-end">Lợi nhuận</th>
                                    <th class="text-center">Biên lợi nhuận</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_tours as $tour): ?>
                                <tr>
                                    <td>
                                        <small><?php echo htmlspecialchars($tour['tour_code']); ?></small><br>
                                        <span class="fw-bold"><?php echo htmlspecialchars($tour['tour_name']); ?></span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-success">
                                            <?php echo number_format($tour['profit'], 0, ',', '.'); ?> ₫
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success"><?php echo $tour['profit_margin']; ?>%</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Report -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-calendar-month me-2"></i>Báo cáo theo tháng (<?php echo date('Y'); ?>)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tháng</th>
                                    <th class="text-end">Doanh thu</th>
                                    <th class="text-end">Lợi nhuận</th>
                                    <th class="text-center">Biên lợi nhuận</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthly_report as $month): ?>
                                <tr>
                                    <td><?php echo date('m/Y', strtotime($month['month_year'] . '-01')); ?></td>
                                    <td class="text-end"><?php echo number_format($month['total_revenue'], 0, ',', '.'); ?> ₫</td>
                                    <td class="text-end">
                                        <span class="fw-bold <?php echo $month['profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo number_format($month['profit'], 0, ',', '.'); ?> ₫
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($month['total_revenue'] > 0): ?>
                                        <span class="badge bg-<?php echo ($month['profit']/$month['total_revenue']*100) >= 20 ? 'success' : 'warning'; ?>">
                                            <?php echo round(($month['profit']/$month['total_revenue']*100), 2); ?>%
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">0%</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?php echo $report['error'] ?? 'Không thể lấy dữ liệu báo cáo'; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function exportReport() {
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    
    window.open(`?act=export_financial_report&start_date=${startDate}&end_date=${endDate}&format=excel`, '_blank');
}

// Chart.js integration
document.addEventListener('DOMContentLoaded', function() {
    // Bạn có thể thêm biểu đồ ở đây nếu cần
    <?php if ($report['success']): ?>
    console.log('Report data loaded successfully');
    <?php endif; ?>
});
</script>

<?php require_once './views/admin/footer.php'; ?>