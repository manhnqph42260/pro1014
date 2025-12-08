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
    // Thêm vào DepartureController.php

// View chi tiết departure với phân bổ
public function adminDetail() {
    $this->checkAdminAuth();
    
    $departure_id = $_GET['id'] ?? 0;
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    // Lấy thông tin departure
    $departure = $this->model->getDepartureById($departure_id);
    
    if (!$departure) {
        $_SESSION['error'] = "Lịch khởi hành không tồn tại!";
        header("Location: ?act=admin_departures");
        exit();
    }
    
    // Lấy thông tin tour
    $tour_stmt = $conn->prepare("SELECT * FROM tours WHERE tour_id = ?");
    $tour_stmt->execute([$departure['tour_id']]);
    $tour = $tour_stmt->fetch();
    
    // Load models
    require_once './models/DepartureAssignmentModel.php';
    require_once './models/DepartureResourceModel.php';
    require_once './models/GuideModel.php';
    
    $assignmentModel = new DepartureAssignmentModel($conn);
    $resourceModel = new DepartureResourceModel($conn);
    
    // Lấy danh sách phân bổ
    $assignments = $assignmentModel->getAssignmentsByDeparture($departure_id);
    $assignmentStats = $assignmentModel->getAssignmentStats($departure_id);
    
    // Lấy danh sách tài nguyên
    $resources = $resourceModel->getResourcesByDeparture($departure_id);
    $resourceStats = $resourceModel->getResourceStats($departure_id);
    
    // Lấy danh sách HDV có sẵn
    $guides = $conn->query("SELECT guide_id, guide_code, full_name FROM guides WHERE status = 'active'")->fetchAll();
    
    // Lấy danh sách booking
    $bookings = $conn->prepare("
        SELECT b.*, t.tour_name 
        FROM bookings b
        JOIN departure_schedules d ON b.departure_id = d.departure_id
        JOIN tours t ON d.tour_id = t.tour_id
        WHERE b.departure_id = ? AND b.status != 'cancelled'
        ORDER BY b.booked_at DESC
    ");
    $bookings->execute([$departure_id]);
    $bookingList = $bookings->fetchAll();
    
    // Tính số chỗ đã đặt
    $booked_slots = 0;
    foreach ($bookingList as $booking) {
        $booked_slots += $booking['total_guests'];
    }
    require_once './models/ChecklistModel.php';
$checklistModel = new ChecklistModel($conn);
$checklistItems = $checklistModel->getChecklistByDeparture($departure_id);
$checklistStats = $checklistModel->getChecklistStats($departure_id);
$upcomingDeadlines = $checklistModel->getUpcomingDeadlines($departure_id);
    
    $available_slots = $departure['expected_slots'] - $booked_slots;
    
    require_once './views/admin/departures/detail.php';
}

// Thêm phân bổ nhân sự
public function adminAddAssignment() {
    $this->checkAdminAuth();
    
    $departure_id = $_GET['departure_id'] ?? 0;
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    require_once './models/DepartureAssignmentModel.php';
    $assignmentModel = new DepartureAssignmentModel($conn);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $data = [
                'departure_id' => $departure_id,
                'assignment_type' => $_POST['assignment_type'],
                'person_id' => !empty($_POST['person_id']) ? $_POST['person_id'] : null,
                'person_name' => $_POST['person_name'],
                'role' => $_POST['role'],
                'contact_info' => $_POST['contact_info'] ?? '',
                'status' => $_POST['status'] ?? 'pending',
                'assignment_date' => $_POST['assignment_date'] ?? null,
                'assignment_notes' => $_POST['assignment_notes'] ?? ''
            ];
            
            if ($assignmentModel->createAssignment($data)) {
                $_SESSION['success'] = "Thêm phân bổ nhân sự thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi thêm phân bổ!";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        
        header("Location: ?act=admin_departure_detail&id=" . $departure_id);
        exit();
    }
    
    // Lấy danh sách HDV
    $guides = $conn->query("SELECT guide_id, guide_code, full_name, phone FROM guides WHERE status = 'active'")->fetchAll();
    
    require_once './views/admin/departures/add_assignment.php';
}

// Thêm tài nguyên/dịch vụ
public function adminAddResource() {
    $this->checkAdminAuth();
    
    $departure_id = $_GET['departure_id'] ?? 0;
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    require_once './models/DepartureResourceModel.php';
    $resourceModel = new DepartureResourceModel($conn);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $data = [
                'departure_id' => $departure_id,
                'resource_type' => $_POST['resource_type'],
                'service_name' => $_POST['service_name'],
                'provider_name' => $_POST['provider_name'] ?? '',
                'quantity' => $_POST['quantity'] ?? 1,
                'unit' => $_POST['unit'] ?? '',
                'unit_price' => $_POST['unit_price'] ?? 0,
                'schedule_date' => $_POST['schedule_date'],
                'schedule_time' => $_POST['schedule_time'] ?? null,
                'location' => $_POST['location'] ?? '',
                'contact_person' => $_POST['contact_person'] ?? '',
                'contact_info' => $_POST['contact_info'] ?? '',
                'status' => $_POST['status'] ?? 'pending',
                'confirmation_number' => $_POST['confirmation_number'] ?? '',
                'resource_notes' => $_POST['resource_notes'] ?? ''
            ];
            
            if ($resourceModel->createResource($data)) {
                $_SESSION['success'] = "Thêm tài nguyên/dịch vụ thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi thêm tài nguyên!";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        
        header("Location: ?act=admin_departure_detail&id=" . $departure_id);
        exit();
    }
    
    require_once './views/admin/departures/add_resource.php';
}

// Xóa phân bổ
public function adminDeleteAssignment() {
    $this->checkAdminAuth();
    
    $assignment_id = $_GET['assignment_id'] ?? 0;
    $departure_id = $_GET['departure_id'] ?? 0;
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    require_once './models/DepartureAssignmentModel.php';
    $assignmentModel = new DepartureAssignmentModel($conn);
    
    try {
        $assignmentModel->deleteAssignment($assignment_id);
        $_SESSION['success'] = "Xóa phân bổ thành công!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    
    header("Location: ?act=admin_departure_detail&id=" . $departure_id);
    exit();
}

// Xóa tài nguyên
public function adminDeleteResource() {
    $this->checkAdminAuth();
    
    $resource_id = $_GET['resource_id'] ?? 0;
    $departure_id = $_GET['departure_id'] ?? 0;
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    require_once './models/DepartureResourceModel.php';
    $resourceModel = new DepartureResourceModel($conn);
    
    try {
        $resourceModel->deleteResource($resource_id);
        $_SESSION['success'] = "Xóa tài nguyên thành công!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    
    header("Location: ?act=admin_departure_detail&id=" . $departure_id);
    exit();
}

// Cập nhật trạng thái phân bổ
public function adminUpdateAssignmentStatus() {
    $this->checkAdminAuth();
    
    $assignment_id = $_GET['assignment_id'] ?? 0;
    $departure_id = $_GET['departure_id'] ?? 0;
    $status = $_GET['status'] ?? 'confirmed';
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    $stmt = $conn->prepare("UPDATE departure_assignments SET status = ? WHERE assignment_id = ?");
    $stmt->execute([$status, $assignment_id]);
    
    $_SESSION['success'] = "Cập nhật trạng thái thành công!";
    header("Location: ?act=admin_departure_detail&id=" . $departure_id);
    exit();
}

// Cập nhật trạng thái tài nguyên
public function adminUpdateResourceStatus() {
    $this->checkAdminAuth();
    
    $resource_id = $_GET['resource_id'] ?? 0;
    $departure_id = $_GET['departure_id'] ?? 0;
    $status = $_GET['status'] ?? 'confirmed';
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    $stmt = $conn->prepare("UPDATE departure_resources SET status = ? WHERE resource_id = ?");
    $stmt->execute([$status, $resource_id]);
    
    $_SESSION['success'] = "Cập nhật trạng thái thành công!";
    header("Location: ?act=admin_departure_detail&id=" . $departure_id);
    exit();
}
public function adminAddChecklist() {
    $this->checkAdminAuth();
    
    $departure_id = $_GET['departure_id'] ?? 0;
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    require_once './models/ChecklistModel.php';
    $checklistModel = new ChecklistModel($conn);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $data = [
                'departure_id' => $departure_id,
                'category' => $_POST['category'],
                'item_name' => $_POST['item_name'],
                'assigned_to' => $_POST['assigned_to'] ?? '',
                'deadline' => !empty($_POST['deadline']) ? $_POST['deadline'] . ' ' . ($_POST['deadline_time'] ?? '23:59:59') : null,
                'status' => $_POST['status'] ?? 'pending',
                'completion_notes' => $_POST['completion_notes'] ?? ''
            ];
            
            if ($checklistModel->createChecklistItem($data)) {
                $_SESSION['success'] = "Thêm công việc checklist thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi thêm công việc!";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
        
        header("Location: ?act=admin_departure_detail&id=" . $departure_id . "&tab=checklist");
        exit();
    }
    
    // Lấy danh sách admin để assign
    $admins = $conn->query("SELECT admin_id, full_name, username FROM admins WHERE status = 'active'")->fetchAll();
    
    require_once './views/admin/departures/add_checklist.php';
}

// Cập nhật trạng thái checklist
public function adminUpdateChecklistStatus() {
    $this->checkAdminAuth();
    
    $item_id = $_GET['item_id'] ?? 0;
    $departure_id = $_GET['departure_id'] ?? 0;
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    require_once './models/ChecklistModel.php';
    $checklistModel = new ChecklistModel($conn);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $status = $_POST['status'];
            $notes = $_POST['completion_notes'] ?? '';
            
            if ($checklistModel->updateChecklistStatus($item_id, $status, $notes)) {
                $_SESSION['success'] = "Cập nhật trạng thái thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi cập nhật trạng thái!";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
        }
    } else {
        $status = $_GET['status'] ?? 'completed';
        $checklistModel->updateChecklistStatus($item_id, $status);
        $_SESSION['success'] = "Cập nhật trạng thái thành công!";
    }
    
    header("Location: ?act=admin_departure_detail&id=" . $departure_id . "&tab=checklist");
    exit();
}

// Xóa checklist item
public function adminDeleteChecklist() {
    $this->checkAdminAuth();
    
    $item_id = $_GET['item_id'] ?? 0;
    $departure_id = $_GET['departure_id'] ?? 0;
    
    require_once './commons/env.php';
    require_once './commons/function.php';
    $conn = connectDB();
    
    require_once './models/ChecklistModel.php';
    $checklistModel = new ChecklistModel($conn);
    
    try {
        $checklistModel->deleteChecklistItem($item_id);
        $_SESSION['success'] = "Xóa công việc thành công!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    
    header("Location: ?act=admin_departure_detail&id=" . $departure_id . "&tab=checklist");
    exit();
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