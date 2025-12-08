<?php
require_once './models/DepartureModel.php';

class DepartureController {
    
    private $model;

    public function __construct() {
        require_once './commons/env.php';
        require_once './commons/function.php';
        $db = connectDB();
        $this->model = new DepartureModel($db);
    }
    
    public function adminList() {
        $this->checkAdminAuth();
        
        $departures = $this->model->getAllDepartures();
        
        require_once './views/admin/departures/list.php';
    }
    
    public function adminCreate() {
        $this->checkAdminAuth();
        
        $conn = connectDB();
        $tours = $conn->query("SELECT tour_id, tour_code, tour_name FROM tours WHERE status = 'published'")->fetchAll();
        
        if ($_POST) {
            try {
                $data = [   
                    'tour_id' => $_POST['tour_id'],
                    'departure_date' => $_POST['departure_date'],
                    'departure_time' => $_POST['departure_time'],
                    'meeting_point' => $_POST['meeting_point'],
                    'expected_slots' => $_POST['expected_slots'],
                    'price_adult' => $_POST['price_adult'],
                    'price_child' => $_POST['price_child'],
                    'operational_notes' => $_POST['operational_notes'],
                    'created_by' => $_SESSION['admin_id']
                ];
                
                if ($this->model->createDeparture($data)) {
                    $_SESSION['success'] = "Tạo lịch khởi hành thành công!";
                    header("Location: ?act=admin_departures");
                    exit();
                } else {
                    $error = "Lỗi khi tạo lịch khởi hành!";
                }
                
            } catch (PDOException $e) {
                $error = "Lỗi khi tạo lịch khởi hành: " . $e->getMessage();
            }
        }
        
        require_once './views/admin/departures/create.php';
    }
    
    public function adminEdit() {
        $this->checkAdminAuth();
        
        $departure_id = $_GET['id'] ?? 0;
        
        if (!$departure_id) {
            $_SESSION['error'] = "ID lịch khởi hành không hợp lệ!";
            header("Location: ?act=admin_departures");
            exit();
        }
        
        // Lấy thông tin lịch khởi hành hiện tại
        $departure = $this->model->getDepartureById($departure_id);
        
        if (!$departure) {
            $_SESSION['error'] = "Không tìm thấy lịch khởi hành!";
            header("Location: ?act=admin_departures");
            exit();
        }
        
        $conn = connectDB();
        // Lấy danh sách tour để chọn
        $tours = $conn->query("SELECT tour_id, tour_code, tour_name FROM tours WHERE status = 'published'")->fetchAll();
        
        if ($_POST) {
            try {
                $data = [
                    'tour_id' => $_POST['tour_id'],
                    'departure_date' => $_POST['departure_date'],
                    'departure_time' => $_POST['departure_time'],
                    'meeting_point' => $_POST['meeting_point'],
                    'expected_slots' => $_POST['expected_slots'],
                    'price_adult' => $_POST['price_adult'],
                    'price_child' => $_POST['price_child'],
                    'operational_notes' => $_POST['operational_notes']
                ];
                
                if ($this->model->updateDeparture($departure_id, $data)) {
                    $_SESSION['success'] = "Cập nhật lịch khởi hành thành công!";
                    header("Location: ?act=admin_departures");
                    exit();
                } else {
                    $error = "Lỗi khi cập nhật lịch khởi hành!";
                }
                
            } catch (PDOException $e) {
                $error = "Lỗi khi cập nhật lịch khởi hành: " . $e->getMessage();
            }
        }
        
        require_once './views/admin/departures/edit.php';
    }
    
    public function adminDelete() {
        $this->checkAdminAuth();
        
        $departure_id = $_GET['id'] ?? 0;
        
        if (!$departure_id) {
            $_SESSION['error'] = "ID lịch khởi hành không hợp lệ!";
            header("Location: ?act=admin_departures");
            exit();
        }
        
        try {
            if ($this->model->deleteDeparture($departure_id)) {
                $_SESSION['success'] = "Xóa lịch khởi hành thành công!";
            } else {
                $_SESSION['error'] = "Không tìm thấy lịch khởi hành để xóa!";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header("Location: ?act=admin_departures");
        exit();
    }
    
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=admin_login");
            exit();
        }
    }
}
?>