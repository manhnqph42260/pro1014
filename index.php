<?php
/* =========================================================================
   FILE KHỞI ĐỘNG (ROUTER) - index.php
   ========================================================================= */

// 1. Khởi tạo Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cấu hình hiển thị lỗi (Nên tắt khi deploy thực tế)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 3. Nạp các file tài nguyên chung
require_once './commons/env.php';
require_once './commons/function.php';

// Chỉ load các controller cần thiết
$controllers = [
    './controllers/ProductController.php',
    './controllers/AdminController.php', 
    './controllers/TourController.php',
    './controllers/DepartureController.php',
    './controllers/BookingController.php',
    './controllers/GuideController.php',
    './controllers/GuestController.php'
];

// Danh sách các Controller cần dùng
require_once './controllers/ProductController.php';
require_once './controllers/AdminController.php';
require_once './controllers/TourController.php';
require_once './controllers/DepartureController.php';
require_once './controllers/BookingController.php';
require_once './controllers/GuestController.php';
require_once './controllers/GuideController.php'; // <--- QUAN TRỌNG: File xử lý HDV


/* =========================================================================
   DANH SÁCH ĐƯỜNG DẪN (ROUTES)
   Cấu trúc: 'tên_act' => ['Tên_Controller', 'Tên_Hàm']
   ========================================================================= */

// Lấy action từ URL
$act = $_GET['act'] ?? '/';

// Xử lý routing bằng match statement
match ($act) {
    // Trang chủ
    '/' => (new ProductController())->Home(),
    
    // Admin Routes
    'admin_login' => (new AdminController())->login(),
    'admin_dashboard' => (new AdminController())->dashboard(),
    'admin_logout' => (new AdminController())->logout(),
    'admin_profile' => (new AdminController())->profile(),
    'admin_change_password' => (new AdminController())->changePassword(),
    
    // Tour Management - Admin
    'admin_tours' => (new TourController())->adminList(),
    'admin_tours_create' => (new TourController())->adminCreate(),
    'admin_tours_edit' => (new TourController())->adminEdit(),
    'admin_tours_delete' => (new TourController())->adminDelete(),
    'admin_tours_update' => (new TourController())->adminUpdate(),
    
    // Tour Itinerary Management
    'admin_tours_itinerary' => (new TourController())->adminItinerary(),
    'admin_tours_itinerary_add' => (new TourController())->adminAddItinerary(),
    'admin_tours_itinerary_edit' => (new TourController())->adminEditItinerary(),
    'admin_tours_itinerary_delete' => (new TourController())->adminDeleteItinerary(),
    
    // Departure Management
    'admin_departures' => (new DepartureController())->adminList(),
    'admin_departures_create' => (new DepartureController())->adminCreate(),
    'admin_departures_delete' => (new DepartureController())->adminDelete(),
    'admin_departures_edit' => (new DepartureController())->adminEdit(),
    
    // Departure Detail & Assignment
    'admin_departure_detail' => (new DepartureController())->adminDetail(),
    'admin_departure_add_assignment' => (new DepartureController())->adminAddAssignment(),
    'admin_departure_add_resource' => (new DepartureController())->adminAddResource(),
    'admin_departure_delete_assignment' => (new DepartureController())->adminDeleteAssignment(),
    'admin_departure_delete_resource' => (new DepartureController())->adminDeleteResource(),
    'admin_departure_update_assignment_status' => (new DepartureController())->adminUpdateAssignmentStatus(),
    'admin_departure_update_resource_status' => (new DepartureController())->adminUpdateResourceStatus(),
    'admin_add_checklist' => (new DepartureController())->adminAddChecklist(),
    'admin_update_checklist_status' => (new DepartureController())->adminUpdateChecklistStatus(),
    'admin_delete_checklist' => (new DepartureController())->adminDeleteChecklist(),
    
    // Booking Management - Admin
    'admin_bookings' => (new BookingController())->adminList(),
    'admin_bookings_create' => (new BookingController())->adminCreate(),
    'admin_bookings_view' => (new BookingController())->adminView(),
    'admin_bookings_edit' => (new BookingController())->adminEdit(),
    'admin_bookings_confirm' => (new BookingController())->adminConfirm(),
    'admin_bookings_cancel' => (new BookingController())->adminCancel(),
    'admin_bookings_add_payment' => (new BookingController())->adminAddPayment(),
    'admin_bookings_delete_payment' => (new BookingController())->adminDeletePayment(),
    'admin_bookings_update_status' => (new BookingController())->adminUpdateStatus(),
    'api_booking_status' => (new BookingController())->apiGetStatusInfo(),
    'admin_bookings_status_history' => (new BookingController())->adminStatusHistory(),

    
    // ==================== HƯỚNG DẪN VIÊN ====================
    // Guide Management - QUAN TRỌNG: Đây là action cần sửa
    'admin_guides' => (new GuideController())->adminList(),
    'admin_guides_create' => (new GuideController())->adminCreate(),
    'admin_guides_edit' => (new GuideController())->adminEdit(),
    'admin_guides_delete' => (new GuideController())->adminDelete(),
    'admin_guides_view' => (new GuideController())->adminView(),
    
    // Guide Category Management
    'admin_guide_categories' => (new GuideController())->adminCategories(),
    'admin_guide_category_create' => (new GuideController())->adminCategoryCreate(),
    'admin_guide_category_edit' => (new GuideController())->adminCategoryEdit(),
    'admin_guide_category_delete' => (new GuideController())->adminCategoryDelete(),
    
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
    
    // ==================== HƯỚNG DẪN VIÊN ĐĂNG NHẬP ====================
    'guide_login' => (new AdminController())->guideLogin(),
    'guide_dashboard' => (new AdminController())->guideDashboard(),
    'guide_logout' => (new AdminController())->guideLogout(),
    'guide_my_tours' => (new AdminController())->guideMyTours(),
    'guide_tour_detail' => (new AdminController())->guideTourDetail(),
    'guide_journal' => (new AdminController())->guideJournal(),
    'guide_attendance' => (new AdminController())->guideAttendance(),
    'guide_participants' => (new AdminController())->guideTourParticipants(),
    'guide_special_requests' => (new AdminController())->guideSpecialRequests(),
    
    default => (new ProductController())->Home()
};
?>
