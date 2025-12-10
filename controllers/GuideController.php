<?php
require_once 'BaseController.php';
require_once './models/GuideModel.php';
require_once './models/GuideCategoryModel.php';

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

    // Hiển thị danh sách categories
    public function adminCategories()
    {
        $this->checkAdminAuth();

        $categories = $this->categoryModel->getCategoryStats();
        $categoryTypes = $this->categoryModel->getCategoryTypes();

        $this->renderView('./views/admin/guides/categories.php', [
            'categories' => $categories,
            'categoryTypes' => $categoryTypes
        ]);
    }

    // Tạo category mới
    public function adminCategoryCreate()
    {
        $this->checkAdminAuth();

        $categoryTypes = $this->categoryModel->getCategoryTypes();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'category_name' => $_POST['category_name'],
                    'category_type' => $_POST['category_type'],
                    'description' => $_POST['description']
                ];

                if ($this->categoryModel->createCategory($data)) {
                    $this->setFlash('success', 'Thêm danh mục thành công');
                    $this->redirect('?act=admin_guide_categories');
                }
            } catch (Exception $e) {
                $this->setFlash('error', $e->getMessage());
            }
        }

        // Render view create (Nếu bạn có view riêng, hoặc dùng modal thì bỏ qua)
        // Ở đây giả sử bạn dùng chung view categories hoặc modal nên redirect về
        $this->redirect('?act=admin_guide_categories');
    }

    // Sửa category
    public function adminCategoryEdit()
    {
        $this->checkAdminAuth();
        // ... Logic edit của bạn ...
    }

    // Xóa category
    public function adminCategoryDelete()
    {
        $this->checkAdminAuth();
        $id = $_GET['id'] ?? 0;
        if ($this->categoryModel->deleteCategory($id)) {
            $this->setFlash('success', 'Xóa danh mục thành công');
        } else {
            $this->setFlash('error', 'Lỗi khi xóa danh mục');
        }
        $this->redirect('?act=admin_guide_categories');
    }


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
        $guide_id = $this->getGuideId();
        $myTours = $this->guideModel->getAssignedTours($guide_id);
        
        $this->renderView('./views/admin/guides/dashboard.php', [
            'myTours' => $myTours,
            'page_title' => 'Dashboard Tổng Quan'
        ]);
    }

    // 2. LỊCH TRÌNH TOUR (Trang riêng)
    public function scheduleList() {
        $guide_id = $this->getGuideId();
        $myTours = $this->guideModel->getAssignedTours($guide_id);

        // Render view riêng cho lịch trình
        $this->renderView('./views/admin/guides/schedule_list.php', [
            'myTours' => $myTours,
            'page_title' => 'Lịch Trình Chi Tiết'
        ]);
    }


    // 3. DANH SÁCH KHÁCH HÀNG (Trang riêng)
    public function guestList() {
        $guide_id = $this->getGuideId();
        
        // Nếu có ID tour trên URL thì hiện danh sách khách của tour đó
        // Nếu không thì hiện danh sách các tour để chọn
        $departure_id = $_GET['id'] ?? 0;

        if ($departure_id) {
            $passengers = $this->guideModel->getPassengersByDeparture($departure_id);
            $tourInfo = $this->guideModel->getDepartureDetail($departure_id);
            
            $this->renderView('./views/admin/guides/guest_list.php', [
                'passengers' => $passengers,
                'tourInfo' => $tourInfo,
                'page_title' => 'Danh sách khách hàng: ' . $tourInfo['tour_code']
            ]);
        } else {
            // Chưa chọn tour -> Hiện danh sách tour để chọn
            $myTours = $this->guideModel->getAssignedTours($guide_id);
            $this->renderView('./views/admin/guides/select_tour_for_guest.php', [
                'myTours' => $myTours,
                'target_act' => 'guide-guest-list', // Bấm vào sẽ sang trang khách
                'page_title' => 'Chọn Tour xem danh sách khách'
            ]);
        }
    }

    /**
     * DANH MỤC ĐIỂM DANH (Danh sách các tour cần điểm danh)
     * URL: index.php?act=guide-attendance-list
     */
    // --- 2. DANH SÁCH CHỌN TOUR ĐỂ ĐIỂM DANH ---
   // 4. ĐIỂM DANH (Logic cũ nhưng link chuẩn)
    public function attendanceList() {
        $guide_id = $this->getGuideId();
        $myTours = $this->guideModel->getAssignedTours($guide_id);

        $this->renderView('./views/admin/guides/select_tour_for_attendance.php', [
            'myTours' => $myTours,
            'target_act' => 'guide-attendance-check',
            'page_title' => 'Chọn Tour Để Điểm Danh'
        ]);
    }

    public function attendanceCheck() {
        $departure_id = $_GET['id'] ?? 0;
        if (!$departure_id) { $this->redirect('?act=guide-attendance-list'); }

        $tourInfo = $this->guideModel->getDepartureDetail($departure_id);
        $passengers = $this->guideModel->getPassengersByDeparture($departure_id);

        $this->renderView('./views/admin/guides/attendance_check.php', [
            'passengers' => $passengers,
            'tourInfo' => $tourInfo,
            'page_title' => 'Điểm danh: ' . $tourInfo['tour_code']
        ]);
    }

    public function attendanceSave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Logic lưu DB ở đây
            echo "<script>alert('Lưu điểm danh thành công!'); window.location.href='?act=guide-attendance-list';</script>";
        }
    }
    // 5. NHẬT KÝ TOUR
    public function journalList() {
        $guide_id = $this->getGuideId();
        // Lấy danh sách tour để viết nhật ký
        $myTours = $this->guideModel->getAssignedTours($guide_id);

        $this->renderView('./views/admin/guides/journal_list.php', [
            'myTours' => $myTours,
            'page_title' => 'Nhật Ký Tour'
        ]);
    }

    // 6. YÊU CẦU ĐẶC BIỆT
    public function specialRequests() {
        $guide_id = $this->getGuideId();
        // Cần viết thêm hàm trong Model để lấy request, tạm thời lấy tour
        $myTours = $this->guideModel->getAssignedTours($guide_id);
        
        // Giả lập dữ liệu request (Sau này lấy từ DB bảng bookings cột special_request)
        $requests = []; 
        
        $this->renderView('./views/admin/guides/special_requests.php', [
            'myTours' => $myTours,
            'requests' => $requests,
            'page_title' => 'Yêu Cầu Đặc Biệt'
        ]);
    }
    /**
     * BÁO CÁO SỰ CỐ (Tạo báo cáo)
     * URL: index.php?act=guide-incident-report
     */
// --- 5. BÁO CÁO SỰ CỐ ---
    public function createIncident() {
        $guide_id = 1; // Hardcode
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             echo "<script>alert('Báo cáo đã gửi!'); window.location.href='?act=guide-dashboard';</script>";
        }

        $myTours = $this->guideModel->getAssignedTours($guide_id);
        $selected_departure_id = $_GET['departure_id'] ?? '';

        $this->renderView('./views/admin/guides/incident_create.php', [
            'myTours' => $myTours,
            'selected_departure_id' => $selected_departure_id,
            'page_title' => 'Báo Cáo Sự Cố'
        ]);
    }
}

    // public function attendanceSave()
    // {
    //     $this->checkGuideAuth();

    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $departure_id = $_POST['departure_id'];
    //         $attendanceData = $_POST['attendance'] ?? []; // Mảng [guest_id => status]
    //         $notesData = $_POST['notes'] ?? [];           // Mảng [guest_id => note]

    //         // Xử lý lưu vào Database
    //         // Vì DB hiện tại chưa có bảng 'attendance_logs', tôi sẽ giả lập lưu thành công
    //         // Sau này bạn tạo bảng attendance_logs thì gọi Model ở đây:
    //         // $this->guideModel->saveAttendance($departure_id, $attendanceData, $notesData);

    //         // Tạm thời thông báo thành công
    //         $count = count($attendanceData);
    //         $this->setFlash('success', "Đã lưu điểm danh cho $count khách hàng!");

    //         // Quay lại trang danh sách
    //         $this->redirect('?act=guide-attendance-list');
    //     } else {
    //         $this->redirect('?act=guide-dashboard');
    //     }
    // }

