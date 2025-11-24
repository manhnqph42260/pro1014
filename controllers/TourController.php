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

    public function adminDetail() {
        $this->checkAdminAuth();
        
        $id = $_GET['id'] ?? 0;
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $db = connectDB();
        $this->model = new TourModel($db);
        
        $tour = $this->model->getTourById($id);
        if (!$tour) {
            $_SESSION['error'] = "Tour không tồn tại!";
            header("Location: ?act=admin_tours");
            exit();
        }
        
        // Lấy dữ liệu chi tiết
        $itineraries = $this->model->getTourItineraries($id);
        $images = $this->model->getTourImages($id);
        $policies = $this->model->getTourPolicies($id);
        $tags = $this->model->getTourTags($id);
        $all_tags = $this->model->getAllTags();
        
        require_once './views/admin/tours/detail.php';
    }

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

    // ==================== ITINERARY MANAGEMENT ====================

    public function adminAddItinerary() {
        $this->checkAdminAuth();
        
        if ($_POST) {
            require_once './commons/env.php';
            require_once './commons/function.php';
            $db = connectDB();
            $this->model = new TourModel($db);
            
            $data = [
                'tour_id' => $_POST['tour_id'],
                'day_number' => $_POST['day_number'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'activities' => $_POST['activities'],
                'accommodation' => $_POST['accommodation'],
                'meals' => $_POST['meals'],
                'guide_notes' => $_POST['guide_notes']
            ];
            
            if ($this->model->addItinerary($data)) {
                $_SESSION['success'] = "Thêm lịch trình thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi thêm lịch trình!";
            }
            
            header("Location: ?act=admin_tour_detail&id=" . $_POST['tour_id']);
            exit();
        }
    }

    public function adminDeleteItinerary() {
        $this->checkAdminAuth();
        
        $id = $_GET['id'] ?? 0;
        $tour_id = $_GET['tour_id'] ?? 0;
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $db = connectDB();
        $this->model = new TourModel($db);
        
        if ($this->model->deleteItinerary($id)) {
            $_SESSION['success'] = "Xóa lịch trình thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa lịch trình!";
        }
        
        header("Location: ?act=admin_tour_detail&id=" . $tour_id);
        exit();
    }

    // ==================== IMAGE MANAGEMENT ====================

    public function adminAddImage() {
        $this->checkAdminAuth();
        
        if ($_POST) {
            require_once './commons/env.php';
            require_once './commons/function.php';
            $db = connectDB();
            $this->model = new TourModel($db);
            
            $image_url = null;
            if (!empty($_FILES["image"]["name"])) {
                $image_url = time() . "_" . $_FILES["image"]["name"];
                move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $image_url);
            }
            
            $data = [
                'tour_id' => $_POST['tour_id'],
                'image_url' => $image_url,
                'caption' => $_POST['caption'],
                'is_primary' => $_POST['is_primary'] ?? 0,
                'display_order' => $_POST['display_order'] ?? 0
            ];
            
            if ($this->model->addImage($data)) {
                $_SESSION['success'] = "Thêm ảnh thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi thêm ảnh!";
            }
            
            header("Location: ?act=admin_tour_detail&id=" . $_POST['tour_id']);
            exit();
        }
    }

    public function adminDeleteImage() {
        $this->checkAdminAuth();
        
        $id = $_GET['id'] ?? 0;
        $tour_id = $_GET['tour_id'] ?? 0;
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $db = connectDB();
        $this->model = new TourModel($db);
        
        // Lấy thông tin ảnh trước khi xóa
        $image = $this->model->getImageById($id);
        if ($image && file_exists("uploads/" . $image['image_url'])) {
            unlink("uploads/" . $image['image_url']);
        }
        
        if ($this->model->deleteImage($id)) {
            $_SESSION['success'] = "Xóa ảnh thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa ảnh!";
        }
        
        header("Location: ?act=admin_tour_detail&id=" . $tour_id);
        exit();
    }

    public function adminSetPrimaryImage() {
        $this->checkAdminAuth();
        
        $id = $_GET['id'] ?? 0;
        $tour_id = $_GET['tour_id'] ?? 0;
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $db = connectDB();
        $this->model = new TourModel($db);
        
        if ($this->model->setPrimaryImage($tour_id, $id)) {
            $_SESSION['success'] = "Đặt ảnh chính thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi đặt ảnh chính!";
        }
        
        header("Location: ?act=admin_tour_detail&id=" . $tour_id);
        exit();
    }

    // ==================== POLICY MANAGEMENT ====================

    public function adminAddPolicy() {
        $this->checkAdminAuth();
        
        if ($_POST) {
            require_once './commons/env.php';
            require_once './commons/function.php';
            $db = connectDB();
            $this->model = new TourModel($db);
            
            $data = [
                'tour_id' => $_POST['tour_id'],
                'policy_type' => $_POST['policy_type'],
                'title' => $_POST['title'],
                'content' => $_POST['content']
            ];
            
            if ($this->model->addPolicy($data)) {
                $_SESSION['success'] = "Thêm chính sách thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi thêm chính sách!";
            }
            
            header("Location: ?act=admin_tour_detail&id=" . $_POST['tour_id']);
            exit();
        }
    }

    public function adminDeletePolicy() {
        $this->checkAdminAuth();
        
        $id = $_GET['id'] ?? 0;
        $tour_id = $_GET['tour_id'] ?? 0;
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $db = connectDB();
        $this->model = new TourModel($db);
        
        if ($this->model->deletePolicy($id)) {
            $_SESSION['success'] = "Xóa chính sách thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa chính sách!";
        }
        
        header("Location: ?act=admin_tour_detail&id=" . $tour_id);
        exit();
    }

    // ==================== TAG MANAGEMENT ====================

    public function adminAddTag() {
        $this->checkAdminAuth();
        
        if ($_POST) {
            require_once './commons/env.php';
            require_once './commons/function.php';
            $db = connectDB();
            $this->model = new TourModel($db);
            
            $data = [
                'tag_name' => $_POST['tag_name'],
                'tag_type' => $_POST['tag_type'],
                'description' => $_POST['description']
            ];
            
            if ($this->model->addTag($data)) {
                $_SESSION['success'] = "Thêm tag thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi thêm tag!";
            }
            
            header("Location: ?act=admin_tour_detail&id=" . $_POST['tour_id']);
            exit();
        }
    }

    public function adminAssignTag() {
        $this->checkAdminAuth();
        
        if ($_POST) {
            require_once './commons/env.php';
            require_once './commons/function.php';
            $db = connectDB();
            $this->model = new TourModel($db);
            
            $tour_id = $_POST['tour_id'];
            $tag_id = $_POST['tag_id'];
            
            if ($this->model->assignTagToTour($tour_id, $tag_id)) {
                $_SESSION['success'] = "Gán tag thành công!";
            } else {
                $_SESSION['error'] = "Lỗi khi gán tag!";
            }
            
            header("Location: ?act=admin_tour_detail&id=" . $tour_id);
            exit();
        }
    }

    public function adminRemoveTag() {
        $this->checkAdminAuth();
        
        $tour_id = $_GET['tour_id'] ?? 0;
        $tag_id = $_GET['tag_id'] ?? 0;
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $db = connectDB();
        $this->model = new TourModel($db);
        
        if ($this->model->removeTagFromTour($tour_id, $tag_id)) {
            $_SESSION['success'] = "Gỡ tag thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi gỡ tag!";
        }
        
        header("Location: ?act=admin_tour_detail&id=" . $tour_id);
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
    public function adminView() {
    $this->checkAdminAuth();
    require_once './commons/env.php';
    require_once './commons/function.php';
    
    $conn = connectDB();
    $tour_id = $_GET['id'] ?? 0;
    
    // Lấy thông tin chi tiết tour
    $query = "SELECT * FROM tours WHERE tour_id = :tour_id";
    $stmt = $conn->prepare($query);
    $stmt->execute(['tour_id' => $tour_id]);
    $tour = $stmt->fetch();
    
    if (!$tour) {
        $_SESSION['error'] = "Tour không tồn tại!";
        header("Location: ?act=admin_tours");
        exit();
    }
    
    // Lấy lịch trình
    $itineraries = $conn->query("
        SELECT * FROM tour_itineraries 
        WHERE tour_id = $tour_id 
        ORDER BY day_number
    ")->fetchAll();
    
    // Lấy hình ảnh
    $images = $conn->query("
        SELECT * FROM tour_images 
        WHERE tour_id = $tour_id 
        ORDER BY is_primary DESC, display_order
    ")->fetchAll();
    
    // Lấy chính sách
    $policies = $conn->query("
        SELECT * FROM tour_policies 
        WHERE tour_id = $tour_id 
        ORDER BY policy_type
    ")->fetchAll();
    
    // Lấy lịch khởi hành
    $departures = $conn->query("
        SELECT * FROM departure_schedules 
        WHERE tour_id = $tour_id 
        ORDER BY departure_date DESC
    ")->fetchAll();
    
    require_once './views/admin/tours/view.php';
}

public function adminItinerary() {
    $this->checkAdminAuth();
    require_once './commons/env.php';
    require_once './commons/function.php';
    
    $tour_id = $_GET['tour_id'] ?? 0;
    
    if (!$tour_id) {
        $_SESSION['error'] = "Tour ID không hợp lệ!";
        header("Location: ?act=admin_tours");
        exit();
    }
    
    $conn = connectDB();
    
    // Lấy thông tin tour - THÊM KIỂM TRA KỸ
    $tour_query = "SELECT * FROM tours WHERE tour_id = :tour_id";
    $tour_stmt = $conn->prepare($tour_query);
    $tour_stmt->execute(['tour_id' => $tour_id]);
    $tour = $tour_stmt->fetch();
    
    if (!$tour) {
        $_SESSION['error'] = "Tour không tồn tại!";
        header("Location: ?act=admin_tours");
        exit();
    }
    
    // Đảm bảo tour có đầy đủ thông tin cần thiết
    $tour = array_merge([
        'tour_name' => 'Tour không xác định',
        'tour_code' => 'N/A',
        'duration_days' => 0,
        'destination' => 'Chưa cập nhật'
    ], $tour);
    
    // Lấy lịch trình tour
    $itinerary_query = "SELECT * FROM tour_itineraries WHERE tour_id = :tour_id ORDER BY day_number ASC";
    $itinerary_stmt = $conn->prepare($itinerary_query);
    $itinerary_stmt->execute(['tour_id' => $tour_id]);
    $itineraries = $itinerary_stmt->fetchAll();
    
    require_once './views/admin/tours/itinerary.php';
}

public function adminImages() {
    $this->checkAdminAuth();
    require_once './commons/env.php';
    require_once './commons/function.php';
    
    $conn = connectDB();
    $tour_id = $_GET['id'] ?? 0;
    
    if ($_POST) {
        // Xử lý upload ảnh
        if (isset($_FILES['images'])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = uniqid() . '_' . $_FILES['images']['name'][$key];
                    $upload_path = './uploads/tours/' . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $query = "INSERT INTO tour_images (tour_id, image_url, caption, display_order) 
                                  VALUES (:tour_id, :image_url, :caption, :display_order)";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([
                            'tour_id' => $tour_id,
                            'image_url' => 'uploads/tours/' . $file_name,
                            'caption' => $_POST['captions'][$key] ?? '',
                            'display_order' => $key
                        ]);
                    }
                }
            }
            $_SESSION['success'] = "Upload ảnh thành công!";
            header("Location: ?act=admin_tours_images&id=" . $tour_id);
            exit();
        }
    }
    
    // Xóa ảnh
    if (isset($_GET['delete_image'])) {
        $image_id = $_GET['delete_image'];
        $stmt = $conn->prepare("DELETE FROM tour_images WHERE image_id = :image_id");
        $stmt->execute(['image_id' => $image_id]);
        
        $_SESSION['success'] = "Xóa ảnh thành công!";
        header("Location: ?act=admin_tours_images&id=" . $tour_id);
        exit();
    }
    
    $tour = $conn->query("SELECT * FROM tours WHERE tour_id = $tour_id")->fetch();
    $images = $conn->query("SELECT * FROM tour_images WHERE tour_id = $tour_id ORDER BY is_primary DESC, display_order")->fetchAll();
    
    require_once './views/admin/tours/images.php';
}

public function adminPolicies() {
    $this->checkAdminAuth();
    require_once './commons/env.php';
    require_once './commons/function.php';
    
    $conn = connectDB();
    $tour_id = $_GET['id'] ?? 0;
    
    if ($_POST) {
        try {
            // Xóa chính sách cũ
            $delete_stmt = $conn->prepare("DELETE FROM tour_policies WHERE tour_id = :tour_id");
            $delete_stmt->execute(['tour_id' => $tour_id]);
            
            // Thêm chính sách mới
            foreach ($_POST['policies'] as $policy_data) {
                if (!empty($policy_data['title']) && !empty($policy_data['content'])) {
                    $query = "INSERT INTO tour_policies (tour_id, policy_type, title, content) 
                              VALUES (:tour_id, :policy_type, :title, :content)";
                    
                    $stmt = $conn->prepare($query);
                    $stmt->execute([
                        'tour_id' => $tour_id,
                        'policy_type' => $policy_data['policy_type'],
                        'title' => $policy_data['title'],
                        'content' => $policy_data['content']
                    ]);
                }
            }
            
            $_SESSION['success'] = "Cập nhật chính sách thành công!";
            header("Location: ?act=admin_tours_view&id=" . $tour_id);
            exit();
            
        } catch (PDOException $e) {
            $error = "Lỗi khi cập nhật chính sách: " . $e->getMessage();
        }
    }
    
    $tour = $conn->query("SELECT * FROM tours WHERE tour_id = $tour_id")->fetch();
    $policies = $conn->query("SELECT * FROM tour_policies WHERE tour_id = $tour_id ORDER BY policy_type")->fetchAll();
    
    require_once './views/admin/tours/policies.php';
}

}
?>