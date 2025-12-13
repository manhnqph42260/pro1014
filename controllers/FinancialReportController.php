<?php
require_once './controllers/BaseController.php';

class FinancialReportController extends BaseController {
    
    /**
     * Hiển thị trang báo cáo tài chính
     */
    public function adminFinancialReport() {
        $this->checkAdminAuth();
        
        require_once './models/FinancialReportModel.php';
        $financialModel = new FinancialReportModel();
        
        // Xử lý filter
        $period_start = $_GET['start_date'] ?? date('Y-m-01');
        $period_end = $_GET['end_date'] ?? date('Y-m-d');
        $tour_id = $_GET['tour_id'] ?? null;
        
        // Lấy dữ liệu báo cáo
        $report = $financialModel->getTourFinancialReport($period_start, $period_end, $tour_id);
        
        // Lấy danh sách tour cho filter
        $conn = connectDB();
        $tours_query = $conn->query("SELECT tour_id, tour_code, tour_name FROM tours WHERE status = 'published' ORDER BY tour_name");
        $tours = $tours_query->fetchAll();
        
        // Lấy top tours lợi nhuận cao nhất
        $top_tours = $financialModel->getTopProfitableTours(5, $period_start, $period_end);
        
        // Lấy báo cáo theo tháng
        $monthly_report = $financialModel->getMonthlyFinancialReport(date('Y'));
        
        $data = [
            'page_title' => 'Báo cáo Tài chính',
            'report' => $report,
            'tours' => $tours,
            'top_tours' => $top_tours,
            'monthly_report' => $monthly_report,
            'filters' => [
                'start_date' => $period_start,
                'end_date' => $period_end,
                'tour_id' => $tour_id
            ],
            'breadcrumb' => [
                ['title' => 'Dashboard', 'link' => '?act=admin_dashboard'],
                ['title' => 'Báo cáo Tài chính', 'active' => true]
            ]
        ];
        
        $this->renderView('./views/admin/financial_report/index.php', $data);
    }
    
    /**
     * Xuất báo cáo ra Excel
     */
    public function exportFinancialReport() {
        $this->checkAdminAuth();
        
        require_once './models/FinancialReportModel.php';
        $financialModel = new FinancialReportModel();
        
        $period_start = $_GET['start_date'] ?? date('Y-m-01');
        $period_end = $_GET['end_date'] ?? date('Y-m-d');
        $format = $_GET['format'] ?? 'excel';
        
        $export_data = $financialModel->exportFinancialReport($period_start, $period_end, $format);
        
        if ($export_data['success']) {
            // Trong thực tế, bạn sẽ tạo file Excel hoặc PDF ở đây
            // Tạm thời trả về JSON
            $this->jsonSuccess($export_data, 'Xuất báo cáo thành công');
        } else {
            $this->jsonError('Xuất báo cáo thất bại: ' . ($export_data['error'] ?? ''));
        }
    }
    
    /**
     * API lấy dữ liệu báo cáo (cho chart)
     */
    public function apiFinancialData() {
        $this->checkAdminAuth();
        
        require_once './models/FinancialReportModel.php';
        $financialModel = new FinancialReportModel();
        
        $period_start = $_GET['start_date'] ?? date('Y-m-01');
        $period_end = $_GET['end_date'] ?? date('Y-m-d');
        $tour_id = $_GET['tour_id'] ?? null;
        
        $report = $financialModel->getTourFinancialReport($period_start, $period_end, $tour_id);
        
        if ($report['success']) {
            $this->jsonSuccess($report);
        } else {
            $this->jsonError($report['error'] ?? 'Lỗi lấy dữ liệu');
        }
    }
    
    /**
     * Hiển thị dashboard thống kê nhanh
     */
    public function financialDashboard() {
        $this->checkAdminAuth();
        
        require_once './models/FinancialReportModel.php';
        $financialModel = new FinancialReportModel();
        
        // Tháng này
        $current_month_start = date('Y-m-01');
        $current_month_end = date('Y-m-d');
        $current_month_report = $financialModel->getTourFinancialReport($current_month_start, $current_month_end);
        
        // Tháng trước
        $last_month_start = date('Y-m-01', strtotime('-1 month'));
        $last_month_end = date('Y-m-t', strtotime('-1 month'));
        $last_month_report = $financialModel->getTourFinancialReport($last_month_start, $last_month_end);
        
        // Top tours tháng này
        $top_tours = $financialModel->getTopProfitableTours(5, $current_month_start, $current_month_end);
        
        // Dữ liệu biểu đồ 6 tháng gần nhất
        $chart_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $month_start = date('Y-m-01', strtotime($month));
            $month_end = date('Y-m-t', strtotime($month));
            
            $month_report = $financialModel->getTourFinancialReport($month_start, $month_end);
            
            if ($month_report['success']) {
                $chart_data[] = [
                    'month' => date('m/Y', strtotime($month)),
                    'revenue' => $month_report['summary']['total_revenue'],
                    'cost' => $month_report['summary']['total_cost'],
                    'profit' => $month_report['summary']['total_profit']
                ];
            }
        }
        
        $data = [
            'page_title' => 'Dashboard Tài chính',
            'current_month' => $current_month_report,
            'last_month' => $last_month_report,
            'top_tours' => $top_tours,
            'chart_data' => $chart_data,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'link' => '?act=admin_dashboard'],
                ['title' => 'Dashboard Tài chính', 'active' => true]
            ]
        ];
        
        $this->renderView('./views/admin/financial_report/dashboard.php', $data);
    }
}
?>