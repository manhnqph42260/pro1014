<?php
// index.php – PHIÊN BẢN HOÀN CHỈNH, CHẠY NGON NHẤT
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

foreach ($controllers as $file) {
    if (file_exists($file)) require_once $file;
}

// ==================== ROUTES ====================
$act = $_GET['act'] ?? '/';

match ($act) {
    // Trang chủ & Login CHUNG
    '/', 'home' => require_once './views/login.php', // Login page chung
    'login' => require_once './views/login.php', // Login page chung
      'process_login' => require_once './views/login.php',
    'process_login' => (new AdminController())->processLogin(), // Xử lý login chung
    
    
    // Logout CHUNG
    'logout' => (new AdminController())->logout(),
    'logout', 'admin_logout', 'guide_logout' => (new AdminController())->logout(),
    // ==================== ADMIN ROUTES ====================
    'admin_dashboard' => (new AdminController())->dashboard(),
    'admin_profile' => (new AdminController())->profile(),
    'admin_change_password' => (new AdminController())->changePassword(),
    
    // ==================== GUIDE ROUTES ====================
    'guide_dashboard' => (new AdminController())->guideDashboard(),
    'guide_profile' => (new AdminController())->guideProfile(),
    'guide_change_password' => (new AdminController())->guideChangePassword(),
    
    
    // ==================== TOUR MANAGEMENT ====================
    'admin_tours' => (new TourController())->adminList(),
    'admin_tours_create' => (new TourController())->adminCreate(),
    'admin_tours_edit' => (new TourController())->adminEdit(),
    'admin_tours_update' => (new TourController())->adminUpdate(),
    'admin_tours_delete' => (new TourController())->adminDelete(),
    'admin_tours_itinerary' => (new TourController())->adminItinerary(),
    'admin_tours_itinerary_add' => (new TourController())->adminAddItinerary(),
    'admin_tours_itinerary_edit' => (new TourController())->adminEditItinerary(),
    'admin_tours_itinerary_delete' => (new TourController())->adminDeleteItinerary(),
    
    // ==================== DEPARTURE MANAGEMENT ====================
    'admin_departures' => (new DepartureController())->adminList(),
    'admin_departures_create' => (new DepartureController())->adminCreate(),
    'admin_departures_edit' => (new DepartureController())->adminEdit(),
    'admin_departures_delete' => (new DepartureController())->adminDelete(),
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
    
    // ==================== BOOKING MANAGEMENT ====================
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
    
    // ==================== GUIDE MANAGEMENT ====================
    'admin_guides' => (new GuideController())->adminList(),
    'admin_guides_create' => (new GuideController())->adminCreate(),
    'admin_guides_edit' => (new GuideController())->adminEdit(),
    'admin_guides_delete' => (new GuideController())->adminDelete(),
    'admin_guides_view' => (new GuideController())->adminView(),
    'admin_guide_categories' => (new GuideController())->adminCategories(),
    'admin_guide_category_create' => (new GuideController())->adminCategoryCreate(),
    'admin_guide_category_edit' => (new GuideController())->adminCategoryEdit(),
    'admin_guide_category_delete' => (new GuideController())->adminCategoryDelete(),
    
    // ==================== GUEST MANAGEMENT ====================
    'admin_guest_management' => (new GuestController())->adminGuestManagement(),
    'admin_guest_detail' => (new GuestController())->adminGuestDetail(),
    'edit_special_notes' => (new GuestController())->editSpecialNotes(),
    'ajax_get_departures' => (new GuestController())->ajaxGetDepartures(),
    'ajax_get_guest_info' => (new GuestController())->ajaxGetGuestInfo(),
    'updateGuestInfo' => (new GuestController())->updateGuestInfo(),
    'updateCheckStatus' => (new GuestController())->updateCheckStatus(),
    'assignRoom' => (new GuestController())->assignRoom(),
    'returnRoom' => (new GuestController())->returnRoom(),
    'showGuestList' => (new GuestController())->showGuestList(),
    'showRoomList' => (new GuestController())->showRoomList(),
    'edit_room' => (new GuestController())->editRoom(),
    'delete_room' => (new GuestController())->deleteRoom(),
    'print_special_notes' => (new GuestController())->printSpecialNotes(),
    
    // ==================== GUIDE DASHBOARD & FEATURES ====================
    'guide_dashboard' => (new AdminController())->guideDashboard(),
    'guide_my_tours' => (new AdminController())->guideMyTours(),
    'guide_tour_detail' => (new AdminController())->guideTourDetail(),
    'guide_journal' => (new AdminController())->guideJournal(),
    'guide_attendance' => (new AdminController())->guideAttendance(),
    'guide_participants' => (new AdminController())->guideTourParticipants(),
    'guide_special_requests' => (new AdminController())->guideSpecialRequests(),
    'guide_incident_reports' => (new AdminController())->guideIncidentReports(),
    'guide_offline_mode' => (new AdminController())->guideOfflineMode(),
    'guide_profile' => (new AdminController())->guideProfile(),
    'guide_change_password' => (new AdminController())->guideChangePassword(),
    'guide_create_incident_report' => (new AdminController())->guideCreateIncidentReport(),
    
    default => (new ProductController())->Home()
};
?>