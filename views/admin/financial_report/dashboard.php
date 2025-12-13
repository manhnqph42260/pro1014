<?php
$page_title = "Dashboard Tài chính";
require_once './views/admin/header.php';
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard Tài chính
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="?act=admin_financial_report" class="btn btn-outline-primary me-2">
                <i class="bi bi-graph-up me-1"></i>Báo cáo chi tiết
            </a>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-calendar-range me-2"></i>Chọn kỳ báo cáo</h5>
            <form method="GET" class="row g-3">
                <input type="hidden" name="act" value="financial_dashboard">
                
                <div class="col-md-3">
                    <select class="form-select" name="period" onchange="this.form.submit()">
                        <option value="this_month" <?php echo ($_GET['period'] ?? 'this_month') === 'this_month' ? 'selected' : ''; ?>>Tháng này</option>
                        <option value="last_month" <?php echo ($_GET['period'] ?? '') === 'last_month' ? 'selected' : ''; ?>>Tháng trước</option>
                        <option value="this_quarter" <?php echo ($_GET['period'] ?? '') === 'this_quarter' ? 'selected' : ''; ?>>Quý này</option>
                        <option value="this_year" <?php echo ($_GET['period'] ?? '') === 'this_year' ? 'selected' : ''; ?>>Năm nay</option>
                        <option value="custom">Tùy chọn...</option>
                    </select>
                </div>
                
                <div class="col-md-9">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary active">Hôm nay</button>
                        <button type="button" class="btn btn-outline-secondary">Tuần này</button>
                        <button type="button" class="btn btn-outline-secondary">Tháng này</button>
                        <button type="button" class="btn btn-outline-secondary">Quý này</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Doanh thu tháng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($current_month['summary']['total_revenue']) 
                                    ? number_format($current_month['summary']['total_revenue'], 0, ',', '.') . ' ₫' 
                                    : '0 ₫'; ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <?php if (isset($last_month['summary']['total_revenue']) && $last_month['summary']['total_revenue'] > 0): ?>
                                <span class="text-<?php echo ($current_month['summary']['total_revenue'] ?? 0) >= ($last_month['summary']['total_revenue'] ?? 0) ? 'success' : 'danger'; ?> mr-2">
                                    <i class="fas fa-arrow-<?php echo ($current_month['summary']['total_revenue'] ?? 0) >= ($last_month['summary']['total_revenue'] ?? 0) ? 'up' : 'down'; ?>"></i>
                                    <?php 
                                    $growth = 0;
                                    if ($last_month['summary']['total_revenue'] > 0) {
                                        $growth = (($current_month['summary']['total_revenue'] - $last_month['summary']['total_revenue']) / $last_month['summary']['total_revenue']) * 100;
                                    }
                                    echo abs(round($growth, 1)) . '%';
                                    ?>
                                </span>
                                <span>So với tháng trước</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-stack fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chi phí tháng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($current_month['summary']['total_cost']) 
                                    ? number_format($current_month['summary']['total_cost'], 0, ',', '.') . ' ₫' 
                                    : '0 ₫'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Lợi nhuận tháng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($current_month['summary']['total_profit']) 
                                    ? number_format($current_month['summary']['total_profit'], 0, ',', '.') . ' ₫' 
                                    : '0 ₫'; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up-arrow fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit Margin Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Biên lợi nhuận</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo isset($current_month['summary']['avg_profit_margin']) 
                                    ? $current_month['summary']['avg_profit_margin'] . '%' 
                                    : '0%'; ?>
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

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh thu 6 tháng gần nhất</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Tours -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 tour lợi nhuận cao</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tour</th>
                                    <th class="text-end">Lợi nhuận</th>
                                    <th class="text-center">Biên</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($top_tours)): ?>
                                    <?php foreach ($top_tours as $tour): ?>
                                    <tr>
                                        <td>
                                            <small class="text-muted"><?php echo htmlspecialchars($tour['tour_code']); ?></small><br>
                                            <span class="fw-bold"><?php echo htmlspecialchars(substr($tour['tour_name'], 0, 20)); ?>...</span>
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
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            <i class="bi bi-inbox fs-1"></i>
                                            <p class="mt-2">Chưa có dữ liệu</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <!-- Monthly Comparison -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">So sánh tháng</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Chỉ tiêu</th>
                                    <th class="text-end">Tháng này</th>
                                    <th class="text-end">Tháng trước</th>
                                    <th class="text-end">Thay đổi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Doanh thu</td>
                                    <td class="text-end"><?php echo isset($current_month['summary']['total_revenue']) ? number_format($current_month['summary']['total_revenue'], 0, ',', '.') . ' ₫' : '0 ₫'; ?></td>
                                    <td class="text-end"><?php echo isset($last_month['summary']['total_revenue']) ? number_format($last_month['summary']['total_revenue'], 0, ',', '.') . ' ₫' : '0 ₫'; ?></td>
                                    <td class="text-end">
                                        <?php if (isset($current_month['summary']['total_revenue']) && isset($last_month['summary']['total_revenue']) && $last_month['summary']['total_revenue'] > 0): ?>
                                            <?php 
                                            $change = (($current_month['summary']['total_revenue'] - $last_month['summary']['total_revenue']) / $last_month['summary']['total_revenue']) * 100;
                                            $color = $change >= 0 ? 'text-success' : 'text-danger';
                                            $icon = $change >= 0 ? 'arrow-up' : 'arrow-down';
                                            ?>
                                            <span class="<?php echo $color; ?>">
                                                <i class="bi bi-<?php echo $icon; ?>"></i>
                                                <?php echo abs(round($change, 1)); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lợi nhuận</td>
                                    <td class="text-end"><?php echo isset($current_month['summary']['total_profit']) ? number_format($current_month['summary']['total_profit'], 0, ',', '.') . ' ₫' : '0 ₫'; ?></td>
                                    <td class="text-end"><?php echo isset($last_month['summary']['total_profit']) ? number_format($last_month['summary']['total_profit'], 0, ',', '.') . ' ₫' : '0 ₫'; ?></td>
                                    <td class="text-end">
                                        <?php if (isset($current_month['summary']['total_profit']) && isset($last_month['summary']['total_profit']) && $last_month['summary']['total_profit'] != 0): ?>
                                            <?php 
                                            $change = (($current_month['summary']['total_profit'] - $last_month['summary']['total_profit']) / abs($last_month['summary']['total_profit'])) * 100;
                                            $color = $change >= 0 ? 'text-success' : 'text-danger';
                                            $icon = $change >= 0 ? 'arrow-up' : 'arrow-down';
                                            ?>
                                            <span class="<?php echo $color; ?>">
                                                <i class="bi bi-<?php echo $icon; ?>"></i>
                                                <?php echo abs(round($change, 1)); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Giao dịch gần đây</h6>
                    <a href="?act=admin_bookings" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php
                        // Lấy danh sách booking gần đây
                        require_once './commons/env.php';
                        require_once './commons/function.php';
                        $conn = connectDB();
                        $recent_bookings = $conn->query("
                            SELECT b.booking_code, b.customer_name, b.total_amount, b.status, b.booked_at,
                                   t.tour_name
                            FROM bookings b
                            JOIN departure_schedules ds ON b.departure_id = ds.departure_id
                            JOIN tours t ON ds.tour_id = t.tour_id
                            ORDER BY b.booked_at DESC
                            LIMIT 5
                        ")->fetchAll();
                        ?>
                        
                        <?php if (!empty($recent_bookings)): ?>
                            <?php foreach ($recent_bookings as $booking): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($booking['customer_name']); ?></h6>
                                    <small><?php echo date('d/m', strtotime($booking['booked_at'])); ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($booking['tour_name']); ?></p>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><?php echo $booking['booking_code']; ?></span>
                                    <span class="fw-bold"><?php echo number_format($booking['total_amount'], 0, ',', '.'); ?> ₫</span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-receipt fs-1"></i>
                                <p class="mt-2">Chưa có giao dịch nào</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Dữ liệu mẫu - trong thực tế sẽ lấy từ PHP
    const chartData = {
        labels: <?php echo json_encode(array_column($chart_data, 'month')); ?>,
        datasets: [{
            label: 'Doanh thu',
            data: <?php echo json_encode(array_column($chart_data, 'revenue')); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            tension: 0.1
        }, {
            label: 'Chi phí',
            data: <?php echo json_encode(array_column($chart_data, 'cost')); ?>,
            backgroundColor: 'rgba(255, 206, 86, 0.2)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 2,
            tension: 0.1
        }, {
            label: 'Lợi nhuận',
            data: <?php echo json_encode(array_column($chart_data, 'profit')); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            tension: 0.1
        }]
    };
    
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('vi-VN', { 
                                    style: 'currency', 
                                    currency: 'VND' 
                                }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', { 
                                style: 'currency', 
                                currency: 'VND',
                                minimumFractionDigits: 0 
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once './views/admin/footer.php'; ?>