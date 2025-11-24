<?php
class DepartureController {
    
    public function adminList() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $conn = connectDB();
        
        // Lấy danh sách lịch khởi hành với thông tin tour
        $query = "
            SELECT 
                d.*,
                t.tour_name,
                t.tour_code,
                t.duration_days
            FROM departure_schedules d
            JOIN tours t ON d.tour_id = t.tour_id
            ORDER BY d.departure_date DESC
        ";
        
        $departures = $conn->query($query)->fetchAll();
        
        require_once './views/admin/departures/list.php';
    }
    
    public function adminCreate() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $conn = connectDB();
        
        // Lấy danh sách tour để chọn
        $tours = $conn->query("SELECT tour_id, tour_code, tour_name FROM tours WHERE status = 'published'")->fetchAll();
        
        if ($_POST) {
            try {
                $query = "INSERT INTO departure_schedules (tour_id, departure_date, departure_time, meeting_point, expected_slots, price_adult, price_child, operational_notes, created_by) 
                          VALUES (:tour_id, :departure_date, :departure_time, :meeting_point, :expected_slots, :price_adult, :price_child, :operational_notes, :created_by)";
                
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    'tour_id' => $_POST['tour_id'],
                    'departure_date' => $_POST['departure_date'],
                    'departure_time' => $_POST['departure_time'],
                    'meeting_point' => $_POST['meeting_point'],
                    'expected_slots' => $_POST['expected_slots'],
                    'price_adult' => $_POST['price_adult'],
                    'price_child' => $_POST['price_child'],
                    'operational_notes' => $_POST['operational_notes'],
                    'created_by' => $_SESSION['admin_id']
                ]);
                
                $_SESSION['success'] = "Tạo lịch khởi hành thành công!";
                header("Location: ?act=admin_departures");
                exit();
                
            } catch (PDOException $e) {
                $error = "Lỗi khi tạo lịch khởi hành: " . $e->getMessage();
            }
        }
        
        require_once './views/admin/departures/create.php';
    }
    
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=admin_login");
            exit();
        }
    }
}
?>