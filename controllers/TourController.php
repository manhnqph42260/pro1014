<?php
require_once 'BaseController.php'; // THÊM DÒNG NÀY

class TourController extends BaseController { // THÊM "extends BaseController"
    
    public function adminList() {
        $this->checkAdminAuth(); // SỬ DỤNG METHOD TỪ BASECONTROLLER
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $conn = connectDB();
        
        // Xử lý search và filter
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $query = "SELECT * FROM tours WHERE 1=1";
        $params = [];
        
        if ($search) {
            $query .= " AND (tour_name LIKE :search OR tour_code LIKE :search)";
            $params['search'] = "%$search%";
        }
        
        if ($status) {
            $query .= " AND status = :status";
            $params['status'] = $status;
        }
        
        $query .= " ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $tours = $stmt->fetchAll();
        
        // SỬ DỤNG METHOD RENDERVIEW TỪ BASECONTROLLER
        $this->renderView('./views/admin/tours/list.php', [
            'tours' => $tours,
            'search' => $search,
            'status' => $status
        ]);
    }
    
    public function adminCreate() {
        $this->checkAdminAuth();
        
        if ($_POST) {
            // Xử lý tạo tour
            $this->handleCreateTour();
        }
        
        $this->renderView('./views/admin/tours/create.php');
    }
    
    public function adminEdit($id = null) {
        $this->checkAdminAuth();
        $tour_id = $id ?? $_GET['id'];
        
        if ($_POST) {
            $this->handleUpdateTour($tour_id);
        }
        
        // Lấy thông tin tour
        require_once './commons/env.php';
        require_once './commons/function.php';
        $conn = connectDB();
        
        $stmt = $conn->prepare("SELECT * FROM tours WHERE tour_id = :id");
        $stmt->execute(['id' => $tour_id]);
        $tour = $stmt->fetch();
        
        if (!$tour) {
            $this->setFlash('error', 'Tour không tồn tại');
            $this->redirect('?act=admin_tours');
        }
        
        $this->renderView('./views/admin/tours/edit.php', ['tour' => $tour]);
    }
    
    public function adminDelete() {
        $this->checkAdminAuth();
        $tour_id = $_GET['id'];
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $conn = connectDB();
        
        try {
            $stmt = $conn->prepare("DELETE FROM tours WHERE tour_id = :id");
            $stmt->execute(['id' => $tour_id]);
            
            $this->setFlash('success', 'Xóa tour thành công');
        } catch (Exception $e) {
            $this->setFlash('error', 'Lỗi khi xóa tour: ' . $e->getMessage());
        }
        
        $this->redirect('?act=admin_tours');
    }
    
    private function handleCreateTour() {
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $conn = connectDB();
        
        try {
            $conn->beginTransaction();
            
            // VALIDATION - SỬ DỤNG METHOD TỪ BASECONTROLLER
            $errors = $this->validateRequired(['tour_code', 'tour_name'], $_POST);
            if (!empty($errors)) {
                $this->setFlash('error', implode("<br>", $errors));
                return;
            }
            
            // INSERT TOUR
            $query = "INSERT INTO tours (tour_code, tour_name, description, destination, duration_days, price_adult, price_child, max_participants, created_by) 
                      VALUES (:code, :name, :desc, :destination, :duration, :price_adult, :price_child, :max_participants, :created_by)";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([
                'code' => $_POST['tour_code'],
                'name' => $_POST['tour_name'],
                'desc' => $_POST['description'] ?? '',
                'destination' => $_POST['destination'] ?? '',
                'duration' => $_POST['duration_days'] ?? 1,
                'price_adult' => $_POST['price_adult'] ?? 0,
                'price_child' => $_POST['price_child'] ?? 0,
                'max_participants' => $_POST['max_participants'] ?? 1,
                'created_by' => $_SESSION['admin_id']
            ]);
            
            $tour_id = $conn->lastInsertId();
            
            // UPLOAD ẢNH - SỬ DỤNG METHOD TỪ BASECONTROLLER
            $this->handleImageUpload($tour_id);
            
            $conn->commit();
            $this->setFlash('success', 'Tạo tour thành công!');
            $this->redirect('?act=admin_tours');
            
        } catch (PDOException $e) {
            $conn->rollBack();
            $this->setFlash('error', "Lỗi khi tạo tour: " . $e->getMessage());
        }
    }
    
    private function handleUpdateTour($tour_id) {
        // Code xử lý update tour
    }
    
    private function handleImageUpload($tour_id) {
        if (!empty($_FILES['images']['name'][0])) {
            require_once './commons/env.php';
            require_once './commons/function.php';
            
            $conn = connectDB();
            $upload_dir = './uploads/tours/';
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $is_primary = ($key === 0) ? 1 : 0;
                        
                        $image_query = "INSERT INTO tour_images (tour_id, image_url, is_primary) 
                                       VALUES (:tour_id, :image_url, :is_primary)";
                        $image_stmt = $conn->prepare($image_query);
                        $image_stmt->execute([
                            'tour_id' => $tour_id,
                            'image_url' => 'uploads/tours/' . $file_name,
                            'is_primary' => $is_primary
                        ]);
                    }
                }
            }
        }
    }
}
?>