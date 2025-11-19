<?php
class TourController {
    
    public function adminList() {
    $this->checkAdminAuth();
    require_once './commons/env.php';
    require_once './commons/function.php';
    
    $conn = connectDB();
    
    // Đếm tổng số tour
    $total_tours = $conn->query("SELECT COUNT(*) as total FROM tours")->fetch()['total'];
    
    // Xử lý tìm kiếm và filter
    $search = $_GET['search'] ?? '';
    $status_filter = $_GET['status'] ?? '';
    
    $query = "SELECT * FROM tours WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $query .= " AND (tour_name LIKE :search OR tour_code LIKE :search OR destination LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if (!empty($status_filter)) {
        $query .= " AND status = :status";
        $params['status'] = $status_filter;
    }
    
    $query .= " ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $tours = $stmt->fetchAll();
    
    require_once './views/admin/tours/list.php';
}
    
    public function adminCreate() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        if ($_POST) {
            $conn = connectDB();
            
            try {
                $query = "INSERT INTO tours (tour_code, tour_name, description, destination, duration_days, price_adult, price_child, max_participants, created_by) 
                          VALUES (:code, :name, :desc, :destination, :duration, :price_adult, :price_child, :max_participants, :created_by)";
                
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    'code' => $_POST['tour_code'],
                    'name' => $_POST['tour_name'],
                    'desc' => $_POST['description'],
                    'destination' => $_POST['destination'],
                    'duration' => $_POST['duration_days'],
                    'price_adult' => $_POST['price_adult'],
                    'price_child' => $_POST['price_child'],
                    'max_participants' => $_POST['max_participants'],
                    'created_by' => $_SESSION['admin_id']
                ]);
                
                $_SESSION['success'] = "Tạo tour thành công!";
                header("Location: ?act=admin_tours");
                exit();
                
            } catch (PDOException $e) {
                $error = "Lỗi khi tạo tour: " . $e->getMessage();
            }
        }
        
        require_once './views/admin/tours/create.php';
    }
    
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=admin_login");
            exit();
        }
    }
}
?>