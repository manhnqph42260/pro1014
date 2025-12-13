<?php
/* =========================================================================
   FILE KHỞI ĐỘNG (ROUTER) - index.php
   ========================================================================= */

session_start(); // Khởi tạo session ngay đầu file

// Cấu hình hiển thị lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Nạp tài nguyên
require_once './commons/env.php';
require_once './commons/function.php';

// Nạp Controllers
require_once './controllers/BaseController.php'; 
require_once './controllers/ProductController.php';
require_once './controllers/AdminController.php';
require_once './controllers/AuthController.php';
require_once './controllers/TourController.php';
require_once './controllers/DepartureController.php';
require_once './controllers/BookingController.php';
require_once './controllers/GuestController.php';
require_once './controllers/GuideController.php';
// Chỉ load các controller cần thiết
$controllers = [
    './controllers/ProductController.php',
    './controllers/AdminController.php', 
    './controllers/TourController.php',
    './controllers/DepartureController.php',
    './controllers/BookingController.php',
    './controllers/GuideController.php',
    './controllers/GuestController.php',
    './controllers/FinancialReportController.php'
];

foreach ($controllers as $file) {
    if (file_exists($file)) require_once $file;
}

$act = $_GET['act'] ?? '/';

match ($act) {
    // ==========================================================
    // 1. XỬ LÝ LOGIN CHUNG (CỔNG ĐĂNG NHẬP CHÍNH)
    // ==========================================================
    'login'       => (new AuthController())->login(),      // Form đăng nhập chung
    'check_login' => (new AuthController())->checkLogin(), // Xử lý logic phân quyền (Admin/Guide)
    'logout'      => (new AuthController())->logout(),     // Đăng xuất chung

    // ==========================================================
    // 2. KHÁCH HÀNG (CLIENT)
    // ==========================================================
    '/'     => (new ProductController())->Home(),

    // ==========================================================
    // 3. ADMIN PORTAL (QUẢN TRỊ VIÊN)
    // ==========================================================
    'admin_dashboard'       => (new AdminController())->dashboard(),
    'admin_profile'         => (new AdminController())->profile(),
    'admin_change_password' => (new AdminController())->changePassword(),

    // Quản lý Tour
    'admin_tours'              => (new TourController())->adminList(),
    'admin_tours_create'       => (new TourController())->adminCreate(),
    'admin_tours_edit'         => (new TourController())->adminEdit(),
    'admin_tours_delete'       => (new TourController())->adminDelete(),
    'admin_tours_update'       => (new TourController())->adminUpdate(),

    // Lịch trình & Khởi hành
    'admin_tours_itinerary'        => (new TourController())->adminItinerary(),
    'admin_tours_itinerary_add'    => (new TourController())->adminAddItinerary(),
    'admin_tours_itinerary_edit'   => (new TourController())->adminEditItinerary(),
    'admin_tours_itinerary_delete' => (new TourController())->adminDeleteItinerary(),
    'admin_departures'             => (new DepartureController())->adminList(),
    'admin_departures_create'      => (new DepartureController())->adminCreate(),
    'admin_departures_edit'        => (new DepartureController())->adminEdit(),
    'admin_departures_delete'      => (new DepartureController())->adminDelete(),
    
    // Chi tiết điều hành (Phần logic phức tạp)
    'admin_departure_detail'                 => (new DepartureController())->adminDetail(),
    'admin_departure_add_assignment'         => (new DepartureController())->adminAddAssignment(),
    'admin_departure_add_resource'           => (new DepartureController())->adminAddResource(),
    'admin_departure_delete_assignment'      => (new DepartureController())->adminDeleteAssignment(),
    'admin_departure_delete_resource'        => (new DepartureController())->adminDeleteResource(),
    'admin_departure_update_assignment_status' => (new DepartureController())->adminUpdateAssignmentStatus(),
    'admin_departure_update_resource_status'   => (new DepartureController())->adminUpdateResourceStatus(),
    'admin_add_checklist'                      => (new DepartureController())->adminAddChecklist(),
    'admin_update_checklist_status'            => (new DepartureController())->adminUpdateChecklistStatus(),
    'admin_delete_checklist'                   => (new DepartureController())->adminDeleteChecklist(),

    // Booking & Khách hàng
    'admin_bookings'                => (new BookingController())->adminList(),
    'admin_bookings_view'           => (new BookingController())->adminView(),
    'admin_bookings_confirm'        => (new BookingController())->adminConfirm(),
    'admin_bookings_cancel'         => (new BookingController())->adminCancel(),
    'admin_bookings_update_status'  => (new BookingController())->adminUpdateStatus(),
    'api_booking_status'            => (new BookingController())->apiGetStatusInfo(),
    'admin_bookings_status_history' => (new BookingController())->adminStatusHistory(),
    
    'admin_guest_management' => (new GuestController())->adminGuestManagement(),
    'admin_guest_detail'     => (new GuestController())->adminGuestDetail(),
    'edit_special_notes'     => (new GuestController())->editSpecialNotes(),
    'ajax_get_departures'    => (new GuestController())->ajaxGetDepartures(),
    'ajax_get_guest_info'    => (new GuestController())->ajaxGetGuestInfo(),
    'updateGuestInfo'        => (new GuestController())->updateGuestInfo(),
    'updateCheckStatus'      => (new GuestController())->updateCheckStatus(),
    'assignRoom'             => (new GuestController())->assignRoom(),
    'returnRoom'             => (new GuestController())->returnRoom(),
    'showGuestList'          => (new GuestController())->showGuestList(),
    'showRoomList'           => (new GuestController())->showRoomList(),
    'edit_room'              => (new GuestController())->editRoom(),
    'delete_room'            => (new GuestController())->deleteRoom(),

    // Quản lý Danh sách HDV (Admin CRUD)
    'admin_guides'                => (new GuideController())->adminList(),
    'admin_guides_create'         => (new GuideController())->adminCreate(),
    'admin_guides_edit'           => (new GuideController())->adminEdit(),
    'admin_guides_delete'         => (new GuideController())->adminDelete(),
    'admin_guides_view'           => (new GuideController())->adminView(),
    'admin_guide_categories'      => (new GuideController())->adminCategories(),
    'admin_guide_category_create' => (new GuideController())->adminCategoryCreate(),
    'admin_guide_category_edit'   => (new GuideController())->adminCategoryEdit(),
    'admin_guide_category_delete' => (new GuideController())->adminCategoryDelete(),


    // ==========================================================
    // 4. HƯỚNG DẪN VIÊN (GUIDE PORTAL)
    // ==========================================================
    // Lưu ý: Đã xóa guide_login vì dùng chung login ở trên
    
    // ==================== QUẢN LÝ KHÁCH HÀNG ====================
    // Guest Management - QUAN TRỌNG: Đây là action cần sửa
    'admin_guest_management' => (new GuestController())->adminGuestManagement(),
    'admin_guest_detail' => (new GuestController())->adminGuestDetail(),
    'edit_special_notes' => (new GuestController())->editSpecialNotes(),
    
    // AJAX functions for Guest Management
    'ajax_get_departures' => (new GuestController())->ajaxGetDepartures(),
    'ajax_get_guest_info' => (new GuestController())->ajaxGetGuestInfo(),
    'updateGuestInfo' => (new GuestController())->updateGuestInfo(),
    'updateCheckStatus' => (new GuestController())->updateCheckStatus(),
    'assignRoom' => (new GuestController())->assignRoom(),
    'returnRoom' => (new GuestController())->returnRoom(),
    
    // Room Management
    'showGuestList' => (new GuestController())->showGuestList(),
    'showRoomList' => (new GuestController())->showRoomList(),
    'edit_room' => (new GuestController())->editRoom(),
    'delete_room' => (new GuestController())->deleteRoom(),
    // Financial Report Routes
'admin_financial_report' => (new FinancialReportController())->adminFinancialReport(),
'financial_dashboard' => (new FinancialReportController())->financialDashboard(),
'export_financial_report' => (new FinancialReportController())->exportFinancialReport(),
'api_financial_data' => (new FinancialReportController())->apiFinancialData(),
    
    // Điểm danh (Tích hợp từ file thứ 2 của bạn)
    'guide_attendance'       => (new GuideController())->attendanceList(), 
    'guide_attendance_check' => (new GuideController())->attendanceCheck(),
    'guide_attendance_save'  => (new GuideController())->attendanceSave(), 
    
    'guide_journal'          => (new GuideController())->journal(),
    'guide_participants'     => (new GuideController())->tourParticipants(),
    'guide_incident_report'  => (new GuideController())->createIncident(), // Thêm từ file thứ 2

    // ==========================================================
    // 5. DEFAULT
    // ==========================================================
    default => (new ProductController())->Home()
};