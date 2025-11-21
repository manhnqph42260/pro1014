<?php
class TourController {
    
    private $model;

    public function __construct()
    {
        require_once './commons/env.php';
        require_once './commons/function.php';
        require_once './models/TourModel.php';
        $db = connectDB();
        $this->model = new TourModel($db);
    }

    // ==================== ADMIN FUNCTIONS ====================

    public function adminList() {
        $this->checkAdminAuth();
        
        $tours = $this->model->getAllTours();
        $total_tours = count($tours);
        
        // Xử lý tìm kiếm và filter
        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status'] ?? '';

        if (!empty($search) || !empty($status_filter)) {
            $tours = $this->model->searchTours($search, $status_filter);
        }
        
        require_once './views/admin/tours/list.php';
    }

    public function adminCreate() {
        $this->checkAdminAuth();
        
        if ($_POST) {
    $image = null;
    if (!empty($_FILES["featured_image"]["name"])) {
        $image = time() . "_" . $_FILES["featured_image"]["name"];
        move_uploaded_file($_FILES["featured_image"]["tmp_name"], "uploads/" . $image);
    }
    // ... rest of the code
}
        if ($_POST) {
            $image = null;
            if (!empty($_FILES["image"]["name"])) {
                $image = time() . "_" . $_FILES["image"]["name"];
                move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $image);
            }
            
            $data = [
                'tour_code' => 'TOUR' . time(),
                'tour_name' => $_POST['tour_name'],
                'description' => $_POST['description'],
                'destination' => $_POST['destination'],
                'duration_days' => $_POST['duration_days'],
                'price_adult' => $_POST['price_adult'],
                'price_child' => $_POST['price_child'] ?? 0,
                'max_participants' => $_POST['max_participants'],
                'status' => $_POST['status'],
                'featured_image' => $image,
                'created_by' => $_SESSION['admin_id']
            ];
            
            if ($this->model->insertTourAdmin($data)) {
                $_SESSION['success'] = "Tạo tour thành công!";
                header("Location: ?act=admin_tours");
                exit();
            } else {
                $error = "Lỗi khi tạo tour!";
            }
        }
        
        require_once './views/admin/tours/create.php';
    }

    public function adminEdit() {
        $this->checkAdminAuth();
        
        
        $id = $_GET['id'] ?? $_POST['tour_id'] ?? 0;
        
        if ($_POST) {
    $oldTour = $this->model->getTourById($id);
    $image = $oldTour['featured_image'];

    if (!empty($_FILES['featured_image']['name'])) {
        if (!empty($oldTour['featured_image']) && file_exists("uploads/" . $oldTour['featured_image'])) {
            unlink("uploads/" . $oldTour['featured_image']);
        }
        $image = time() . "_" . $_FILES['featured_image']['name'];
        move_uploaded_file($_FILES['featured_image']['tmp_name'], "uploads/" . $image);
    }
    // ... rest of the code
}
        if ($_POST) {
            $oldTour = $this->model->getTourById($id);
            $image = $oldTour['featured_image'];

            if (!empty($_FILES['image']['name'])) {
                if (!empty($oldTour['featured_image']) && file_exists("uploads/" . $oldTour['featured_image'])) {
                    unlink("uploads/" . $oldTour['featured_image']);
                }
                $image = time() . "_" . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
            }

            $data = [
                'tour_name' => $_POST['tour_name'],
                'description' => $_POST['description'],
                'destination' => $_POST['destination'],
                'duration_days' => $_POST['duration_days'],
                'price_adult' => $_POST['price_adult'],
                'price_child' => $_POST['price_child'] ?? 0,
                'max_participants' => $_POST['max_participants'],
                'status' => $_POST['status'],
                'featured_image' => $image
            ];

            if ($this->model->updateTourAdmin($id, $data)) {
                $_SESSION['success'] = "Cập nhật tour thành công!";
                header("Location: ?act=admin_tours");
                exit();
            } else {
                $error = "Lỗi khi cập nhật tour!";
            }
        }

        $tour = $this->model->getTourById($id);
        if (!$tour) {
            $_SESSION['error'] = "Tour không tồn tại!";
            header("Location: ?act=admin_tours");
            exit();
        }
        
        require_once './views/admin/tours/edit.php';
    }

    public function adminDelete() {
        $this->checkAdminAuth();
        
        $id = $_GET['id'] ?? 0;
        if ($this->model->deleteTour($id)) {
            $_SESSION['success'] = "Xóa tour thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa tour!";
        }
        
        header("Location: ?act=admin_tours");
        exit();
    }

    // ==================== FRONTEND FUNCTIONS ====================

    public function index() {
        $tours = $this->model->getAllTours();
        include "./views/trangchu.php";
    }

    public function add() {
        include "./views/add.php";
    }

    public function store() {
        $image = null;
        if (!empty($_FILES["image"]["name"])) {
            $image = time() . "_" . $_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $image);
        }
        $data = [
            'tour_name' => $_POST['tour_name'],
            'price'     => $_POST['price'],
            'description' => $_POST['description'],
            'start_date' => $_POST['start_date'],
            'duration' => $_POST['duration'],
            'destination' => $_POST['destination'],
            'available_seats' => $_POST['available_seats'],
            'image' => $image,
            'status'=> $_POST['status']
        ];
        $this->model->insertTour($data);
        header("Location: index.php?act=home");
    }

    public function edit() {
        $id = $_GET['id'];
        $tour = $this->model->getTourById($id);
        include "./views/edit.php";
    }

    public function update() {
        $id = $_POST['tour_id'];
        $oldTour = $this->model->getTourById($id);
        $image = $oldTour['image'];

        if (!empty($_FILES['image']['name'])) {
            if (!empty($oldTour['image']) && file_exists("uploads/" . $oldTour['image'])) {
                unlink("uploads/" . $oldTour['image']);
            }
            $image = time() . "_" . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
        }

        $data = [
            'tour_name' => $_POST['tour_name'],
            'price'     => $_POST['price'],
            'description' => $_POST['description'],
            'start_date' => $_POST['start_date'],
            'duration' => $_POST['duration'],
            'destination' => $_POST['destination'],
            'available_seats' => $_POST['available_seats'],
            'image' => $image,
            'status' => $_POST['status']
        ];

        $this->model->updateTour($id, $data);
        header("Location: index.php?act=home");
    }

    public function delete() {
        $id = $_GET['id'];
        $this->model->deleteTour($id);
        header("Location: index.php?act=home");
    }

    // ==================== UTILITY FUNCTIONS ====================

    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=admin_login");
            exit();
        }
    }
    // Thêm vào class TourController

public function adminUpdate() {
    $this->checkAdminAuth();
    
    $id = $_POST['tour_id'] ?? 0;
    
    if ($_POST) {
        require_once './commons/env.php';
        require_once './commons/function.php';
        $db = connectDB();
        $this->model = new TourModel($db);
        
        $oldTour = $this->model->getTourById($id);
        $image = $oldTour['featured_image'];

        if (!empty($_FILES['featured_image']['name'])) {
            if (!empty($oldTour['featured_image']) && file_exists("uploads/" . $oldTour['featured_image'])) {
                unlink("uploads/" . $oldTour['featured_image']);
            }
            $image = time() . "_" . $_FILES['featured_image']['name'];
            move_uploaded_file($_FILES['featured_image']['tmp_name'], "uploads/" . $image);
        }

        $data = [
            'tour_name' => $_POST['tour_name'],
            'description' => $_POST['description'],
            'destination' => $_POST['destination'],
            'duration_days' => $_POST['duration_days'],
            'price_adult' => $_POST['price_adult'],
            'price_child' => $_POST['price_child'] ?? 0,
            'max_participants' => $_POST['max_participants'],
            'status' => $_POST['status'],
            'featured_image' => $image
        ];

        if ($this->model->updateTourAdmin($id, $data)) {
            $_SESSION['success'] = "Cập nhật tour thành công!";
            header("Location: ?act=admin_tours");
            exit();
        } else {
            $_SESSION['error'] = "Lỗi khi cập nhật tour!";
            header("Location: ?act=admin_tours_edit&id=" . $id);
            exit();
        }
    }
}
}

?>