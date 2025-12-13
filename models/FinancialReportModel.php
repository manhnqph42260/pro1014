<?php
/**
 * FinancialReportModel - Model xử lý báo cáo tài chính
 */
class FinancialReportModel {
    private $conn;

    public function __construct() {
        require_once './commons/env.php';
        require_once './commons/function.php';
        $this->conn = connectDB();
    }

    /**
     * Lấy báo cáo doanh thu, chi phí, lợi nhuận theo tour
     * @param string $period_start Ngày bắt đầu (Y-m-d)
     * @param string $period_end Ngày kết thúc (Y-m-d)
     * @param int|null $tour_id ID tour cụ thể (null để lấy tất cả)
     * @return array
     */
    public function getTourFinancialReport($period_start = null, $period_end = null, $tour_id = null) {
        try {
            // Điều kiện thời gian
            $date_condition = "";
            $params = [];
            
            if ($period_start && $period_end) {
                 $date_condition = "AND b.booked_at BETWEEN :start_date AND :end_date";
                $params[':start_date'] = $period_start . " 00:00:00";
                $params[':end_date'] = $period_end . " 23:59:59";
            }
            
            // Điều kiện tour
            $tour_condition = "";
            if ($tour_id) {
                $tour_condition = "AND t.tour_id = :tour_id";
                $params[':tour_id'] = $tour_id;
            }
            
            // ==================== DOANH THU ====================
            // Doanh thu từ bookings đã xác nhận
             $cost_query = "
                SELECT 
                    t.tour_id,
                    COALESCE(SUM(dr.total_price), 0) as total_cost
                FROM tours t
                LEFT JOIN departure_schedules ds ON t.tour_id = ds.tour_id
                LEFT JOIN departure_resources dr ON ds.departure_id = dr.departure_id
                WHERE (dr.status IS NULL OR dr.status = 'confirmed')
                    $date_condition
                    $tour_condition
                GROUP BY t.tour_id
            ";
            
            $stmt = $this->conn->prepare($cost_query);
            $stmt->execute($params);
            $revenue_data = $stmt->fetchAll();
            
            // ==================== CHI PHÍ ====================
            // Chi phí từ departure_resources
             $cost_query = "
                SELECT 
                    t.tour_id,
                    COALESCE(SUM(dr.total_price), 0) as total_cost
                FROM tours t
                LEFT JOIN departure_schedules ds ON t.tour_id = ds.tour_id
                LEFT JOIN departure_resources dr ON ds.departure_id = dr.departure_id
                WHERE (dr.status IS NULL OR dr.status = 'confirmed')
                    $date_condition
                    $tour_condition
                GROUP BY t.tour_id
            ";
            
            $stmt = $this->conn->prepare($cost_query);
            $stmt->execute($params);
            $cost_data = $stmt->fetchAll();
            
            // Chuyển cost_data thành mảng dễ truy cập
            $cost_map = [];
            foreach ($cost_data as $cost) {
                $cost_map[$cost['tour_id']] = $cost['total_cost'];
            }
            
            // ==================== TÍNH LỢI NHUẬN ====================
            $report_data = [];
            $total_revenue = 0;
            $total_cost = 0;
            $total_profit = 0;
            
            foreach ($revenue_data as $item) {
                $tour_cost = $cost_map[$item['tour_id']] ?? 0;
                $profit = $item['total_revenue'] - $tour_cost;
                $profit_margin = $item['total_revenue'] > 0 ? ($profit / $item['total_revenue'] * 100) : 0;
                
                $report_data[] = [
                    'tour_id' => $item['tour_id'],
                    'tour_code' => $item['tour_code'],
                    'tour_name' => $item['tour_name'],
                    'total_bookings' => (int)$item['total_bookings'],
                    'total_revenue' => (float)$item['total_revenue'],
                    'total_deposit' => (float)$item['total_deposit'],
                    'remaining_balance' => (float)$item['remaining_balance'],
                    'total_cost' => (float)$tour_cost,
                    'profit' => (float)$profit,
                    'profit_margin' => round($profit_margin, 2),
                    'profit_status' => $profit >= 0 ? 'profit' : 'loss'
                ];
                
                $total_revenue += $item['total_revenue'];
                $total_cost += $tour_cost;
                $total_profit += $profit;
            }
            
            // Tổng hợp
            $summary = [
                'total_tours' => count($report_data),
                'total_bookings' => array_sum(array_column($report_data, 'total_bookings')),
                'total_revenue' => $total_revenue,
                'total_cost' => $total_cost,
                'total_profit' => $total_profit,
                'avg_profit_margin' => $total_revenue > 0 ? round(($total_profit / $total_revenue) * 100, 2) : 0
            ];
            
            return [
                'success' => true,
                'data' => $report_data,
                'summary' => $summary,
                'period' => [
                    'start' => $period_start,
                    'end' => $period_end
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Lấy báo cáo chi tiết theo tháng
     */
    public function getMonthlyFinancialReport($year = null, $month = null) {
        try {
            $year = $year ?: date('Y');
            
            $query = "
                SELECT 
                    DATE_FORMAT(b.booked_at, '%Y-%m') as month_year,
                    COUNT(DISTINCT t.tour_id) as total_tours,
                    COUNT(DISTINCT b.booking_id) as total_bookings,
                    SUM(b.total_amount) as total_revenue,
                    COALESCE(SUM(dr.total_price), 0) as total_cost,
                    SUM(b.total_amount) - COALESCE(SUM(dr.total_price), 0) as profit
                FROM bookings b
                LEFT JOIN departure_schedules ds ON b.departure_id = ds.departure_id
                LEFT JOIN tours t ON ds.tour_id = t.tour_id
                LEFT JOIN departure_resources dr ON ds.departure_id = dr.departure_id AND dr.status = 'confirmed'
                WHERE b.status IN ('confirmed', 'completed')
                    AND YEAR(b.booked_at) = :year
                    " . ($month ? "AND MONTH(b.booked_at) = :month" : "") . "
                GROUP BY DATE_FORMAT(b.booked_at, '%Y-%m')
                ORDER BY month_year DESC
            ";
            
            $stmt = $this->conn->prepare($query);
            $params = [':year' => $year];
            if ($month) {
                $params[':month'] = $month;
            }
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Lấy top tours theo lợi nhuận
     */
    public function getTopProfitableTours($limit = 5, $period_start = null, $period_end = null) {
        try {
            $date_condition = "";
            $params = [':limit' => $limit];
            
            if ($period_start && $period_end) {
                $date_condition = "AND b.booked_at BETWEEN :start_date AND :end_date";
                $params[':start_date'] = $period_start . " 00:00:00";
                $params[':end_date'] = $period_end . " 23:59:59";
            }
            
            $query = "
                SELECT 
                    t.tour_id,
                    t.tour_code,
                    t.tour_name,
                    COUNT(DISTINCT b.booking_id) as bookings_count,
                    SUM(b.total_amount) as revenue,
                    COALESCE(SUM(dr.total_price), 0) as cost,
                    SUM(b.total_amount) - COALESCE(SUM(dr.total_price), 0) as profit,
                    ROUND((SUM(b.total_amount) - COALESCE(SUM(dr.total_price), 0)) / SUM(b.total_amount) * 100, 2) as profit_margin
                FROM tours t
                LEFT JOIN departure_schedules ds ON t.tour_id = ds.tour_id
                LEFT JOIN bookings b ON ds.departure_id = b.departure_id
                LEFT JOIN departure_resources dr ON ds.departure_id = dr.departure_id AND dr.status = 'confirmed'
                WHERE b.status IN ('confirmed', 'completed')
                    $date_condition
                GROUP BY t.tour_id, t.tour_code, t.tour_name
                HAVING profit IS NOT NULL
                ORDER BY profit DESC
                LIMIT :limit
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Xuất báo cáo ra Excel
     */
    public function exportFinancialReport($period_start, $period_end, $format = 'excel') {
        $report = $this->getTourFinancialReport($period_start, $period_end);
        
        if (!$report['success']) {
            return $report;
        }
        
        // Tạo dữ liệu Excel (giả lập - trong thực tế dùng PHPExcel hoặc PhpSpreadsheet)
        $export_data = [
            'period' => $period_start . ' đến ' . $period_end,
            'generated_at' => date('Y-m-d H:i:s'),
            'data' => $report['data'],
            'summary' => $report['summary']
        ];
        
        return [
            'success' => true,
            'format' => $format,
            'data' => $export_data,
            'filename' => 'bao_cao_tai_chinh_' . date('Ymd_His') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf')
        ];
    }
}
?>