<?php
require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../models/GuideModel.php';
require_once __DIR__ . '/../../models/GuideCategoryModel.php';

class GuideController extends BaseController
{

    public $guideModel;
    public $categoryModel;

    public function __construct()
    {
        // 1. Khởi tạo Model cho phần Portal của HDV
        // Đảm bảo bạn đã có file models/GuideModel.php như hướng dẫn trước
        $this->guideModel = new GuideModel();

        // 2. Khởi tạo Model cho phần Admin quản lý danh mục (Code cũ của bạn)
        // Cần connect DB thủ công vì model cũ của bạn yêu cầu truyền $conn vào constructor
        require_once './commons/env.php';
        require_once './commons/function.php';
        $conn = connectDB();
        $this->categoryModel = new GuideCategoryModel($conn);
    }

    /* =========================================================================
       PHẦN 1: DÀNH CHO ADMIN (QUẢN LÝ DANH MỤC HDV) - GIỮ NGUYÊN CODE CŨ
       ========================================================================= */

    // // Hiển thị danh sách categories
    // public function adminCategories()
    // {
    //     $this->checkAdminAuth();

    //     $categories = $this->categoryModel->getCategoryStats();
    //     $categoryTypes = $this->categoryModel->getCategoryTypes();

    //     $this->renderView('./views/admin/guides/categories.php', [
    //         'categories' => $categories,
    //         'categoryTypes' => $categoryTypes
    //     ]);
    // }

    // // Tạo category mới
    // public function adminCategoryCreate()
    // {
    //     $this->checkAdminAuth();

    //     $categoryTypes = $this->categoryModel->getCategoryTypes();

    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         try {
    //             $data = [
    //                 'category_name' => $_POST['category_name'],
    //                 'category_type' => $_POST['category_type'],
    //                 'description' => $_POST['description']
    //             ];

    //             if ($this->categoryModel->createCategory($data)) {
    //                 $this->setFlash('success', 'Thêm danh mục thành công');
    //                 $this->redirect('?act=admin_guide_categories');
    //             }
    //         } catch (Exception $e) {
    //             $this->setFlash('error', $e->getMessage());
    //         }
    //     }

    //     // Render view create (Nếu bạn có view riêng, hoặc dùng modal thì bỏ qua)
    //     // Ở đây giả sử bạn dùng chung view categories hoặc modal nên redirect về
    //     $this->redirect('?act=admin_guide_categories');
    // }

    // // Sửa category
    // public function adminCategoryEdit()
    // {
    //     $this->checkAdminAuth();
    //     // ... Logic edit của bạn ...
    // }

    // // Xóa category
    // public function adminCategoryDelete()
    // {
    //     $this->checkAdminAuth();
    //     $id = $_GET['id'] ?? 0;
    //     if ($this->categoryModel->deleteCategory($id)) {
    //         $this->setFlash('success', 'Xóa danh mục thành công');
    //     } else {
    //         $this->setFlash('error', 'Lỗi khi xóa danh mục');
    //     }
    //     $this->redirect('?act=admin_guide_categories');
    // }


    /* =========================================================================
       PHẦN 2: DÀNH CHO HƯỚNG DẪN VIÊN (HDV PORTAL) - CODE MỚI THÊM VÀO
       ========================================================================= */

    /**
     * TRANG CHỦ HDV (Dashboard)
     * URL: index.php?act=guide-dashboard
     */

    public function login()
    {
        // Nếu đã đăng nhập rồi thì đá về dashboard luôn
        if (isset($_SESSION['guide_id'])) {
            $this->redirect('?act=guide-dashboard');
        }
        require_once './views/admin/guides/guide_login.php';
    }

    /**
     * 2. XỬ LÝ ĐĂNG NHẬP (POST)
     */
    public function loginCheck()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Gọi Model kiểm tra
            $user = $this->guideModel->checkLogin($username, $password);

            if ($user) {
                // Đăng nhập thành công -> Lưu session
                $_SESSION['guide_id'] = $user['guide_id'];
                $_SESSION['user_guide'] = $user; // Lưu full info để dùng ở header
                $_SESSION['guide_name'] = $user['full_name'];
                $_SESSION['role'] = 'guide'; // Đánh dấu quyền

                $this->setFlash('success', 'Chào mừng trở lại, ' . $user['full_name']);
                $this->redirect('?act=guide-dashboard');
            } else {
                // Đăng nhập thất bại
                $this->setFlash('error', 'Sai tên đăng nhập hoặc mật khẩu!');
                $this->redirect('?act=guide-login');
            }
        }
    }

    /**
     * 3. ĐĂNG XUẤT
     */
    public function logout()
    {
        unset($_SESSION['guide_id']);
        unset($_SESSION['user_guide']);
        unset($_SESSION['guide_name']);

        session_destroy(); // Hủy toàn bộ session cho chắc
        header('Location: ?act=guide-login');
        exit();
    }
  // --- 1. DASHBOARD & LỊCH TRÌNH ---
// Hàm tiện ích: Lấy ID Guide (Hardcode = 1 để test như bạn muốn)
    private function getGuideId() {
        // $this->checkGuideAuth(); // Tạm tắt để test
        return 1; 
    }

    // 1. DASHBOARD
public function dashboard() {
    // 1. Kiểm tra đăng nhập
    if (!isset($_SESSION['guide_id'])) {
        header('Location: ?act=login');
        exit();
    }
    
    // 2. Set biến bắt buộc (dashboard đang cần)
    $page_title = "Bảng điều khiển HDV";
    
    // 3. Đường dẫn đến các file
    $basePath = __DIR__ . '/../../views/admin/guides/';
    
    // 4. Load header (nếu có)
    $headerFile = $basePath . 'header.php';
    if (file_exists($headerFile)) {
        require_once $headerFile;
    }
    
    // 5. Load dashboard chính
    $dashboardFile = $basePath . 'dashboard.php';
    if (file_exists($dashboardFile)) {
        require_once $dashboardFile;
    } else {
        echo "<h1>Dashboard not found!</h1>";
        echo "<p>Expected: " . htmlspecialchars($dashboardFile) . "</p>";
    }
    
    // 6. Load footer (nếu có)
    $footerFile = $basePath . 'footer.php';
    if (file_exists($footerFile)) {
        require_once $footerFile;
    }
    
    exit();
}
    // // 2. LỊCH TRÌNH TOUR (Trang riêng)
    // public function scheduleList() {
    //     $guide_id = $this->getGuideId();
    //     $myTours = $this->guideModel->getAssignedTours($guide_id);

    //     // Render view riêng cho lịch trình
    //     $this->renderView('./views/admin/guides/schedule_list.php', [
    //         'myTours' => $myTours,
    //         'page_title' => 'Lịch Trình Chi Tiết'
    //     ]);
    // }


    // // 3. DANH SÁCH KHÁCH HÀNG (Trang riêng)
    // public function guestList() {
    //     $guide_id = $this->getGuideId();
        
    //     // Nếu có ID tour trên URL thì hiện danh sách khách của tour đó
    //     // Nếu không thì hiện danh sách các tour để chọn
    //     $departure_id = $_GET['id'] ?? 0;

    //     if ($departure_id) {
    //         $passengers = $this->guideModel->getPassengersByDeparture($departure_id);
    //         $tourInfo = $this->guideModel->getDepartureDetail($departure_id);
            
    //         $this->renderView('./views/admin/guides/guest_list.php', [
    //             'passengers' => $passengers,
    //             'tourInfo' => $tourInfo,
    //             'page_title' => 'Danh sách khách hàng: ' . $tourInfo['tour_code']
    //         ]);
    //     } else {
    //         // Chưa chọn tour -> Hiện danh sách tour để chọn
    //         $myTours = $this->guideModel->getAssignedTours($guide_id);
    //         $this->renderView('./views/admin/guides/select_tour_for_guest.php', [
    //             'myTours' => $myTours,
    //             'target_act' => 'guide-guest-list', // Bấm vào sẽ sang trang khách
    //             'page_title' => 'Chọn Tour xem danh sách khách'
    //         ]);
    //     }
    // }

    // /**
    //  * DANH MỤC ĐIỂM DANH (Danh sách các tour cần điểm danh)
    //  * URL: index.php?act=guide-attendance-list
    //  */
// Thêm các hàm này vào GuideController

public function myTours() {
    if (!isset($_SESSION['guide_id'])) {
        header('Location: ?act=login');
        exit();
    }
    
    $page_title = "Tour của tôi";
    $viewFile = __DIR__ . '/../../views/admin/guides/my_tours.php';
    
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        // Fallback: hiển thị trang tạm thời
        $this->showTempPage($page_title, "Trang 'Tour của tôi' đang được phát triển.");
    }
    exit();
}
public function tourDetail() {
    if (!isset($_SESSION['guide_id'])) {
        header('Location: ?act=login');
        exit();
    }
    
    $page_title = "Chi Tiết Tour";
    $viewFile = __DIR__ . '/../../views/admin/guides/tour_detail.php';
    
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        $this->showTempPage($page_title, "Trang 'Chi Tiết Tour' đang được phát triển.");
    }
    exit();
}
public function scheduleList() {
    if (!isset($_SESSION['guide_id'])) {
        header('Location: ?act=login');
        exit();
    }
    
    $page_title = "Lịch Trình Tour";
    $viewFile = __DIR__ . '/../../views/admin/guides/schedule_list.php';
    
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        $this->showTempPage($page_title, "Trang 'Lịch Trình Tour' đang được phát triển.");
    }
    exit();
}

public function attendanceList() {
    if (!isset($_SESSION['guide_id'])) {
        header('Location: ?act=login');
        exit();
    }
    
    $page_title = "Điểm danh";
    $viewFile = __DIR__ . '/../../views/admin/guides/attendance.php';
    
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        $this->showTempPage($page_title, "Trang 'Điểm danh' đang được phát triển.");
    }
    exit();
}

// Thêm hàm hiển thị trang tạm thời
private function showTempPage($title, $message) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?php echo $title; ?> - Tour Management</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-success">
            <div class="container">
                <span class="navbar-brand">🎯 HỆ THỐNG HDV</span>
                <span class="text-white">Xin chào: <?php echo $_SESSION['full_name'] ?? 'HDV'; ?></span>
            </div>
        </nav>
        
        <div class="container mt-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4><?php echo $title; ?></h4>
                </div>
                <div class="card-body text-center py-5">
                    <h1 class="text-warning">🚧</h1>
                    <h3>Đang phát triển</h3>
                    <p class="lead"><?php echo $message; ?></p>
                    <p>Chức năng này sẽ sớm có mặt.</p>
                    
                    <div class="mt-4">
                        <a href="?act=guide_dashboard" class="btn btn-primary">Quay lại Dashboard</a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="?act=admin_dashboard" class="btn btn-secondary">Admin Dashboard</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}


public function guestList() {
    if (!isset($_SESSION['guide_id'])) {
        header('Location: ?act=login');
        exit();
    }
    
    $page_title = "Danh sách khách";
    require_once __DIR__ . '/../../views/admin/guides/guest_list.php';
    exit();
}

public function journalList() {
    if (!isset($_SESSION['guide_id'])) {
        header('Location: ?act=login');
        exit();
    }
    
    $page_title = "Nhật ký tour";
    require_once __DIR__ . '/../../views/admin/guides/journal.php';
    exit();
}

public function specialRequests() {
    if (!isset($_SESSION['guide_id'])) {
        header('Location: ?act=login');
        exit();
    }
    
    $page_title = "Yêu cầu đặc biệt";
    require_once __DIR__ . '/../../views/admin/guides/special_requests.php';
    exit();
}

public function incidentList() {
    if (!isset($_SESSION['guide_id'])) {
        header('Location: ?act=login');
        exit();
    }
    
    $page_title = "Báo cáo sự cố";
    require_once __DIR__ . '/../../views/admin/guides/incident_create.php';
    exit();
}
}   



