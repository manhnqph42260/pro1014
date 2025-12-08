<?php
// index.php – PHIÊN BẢN HOÀN CHỈNH, CHẠY NGON NHẤT
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './commons/env.php';
require_once './commons/function.php';

// Chỉ load các controller cần thiết (bỏ GuideController)
$controllers = [
    './controllers/ProductController.php',
    './controllers/AdminController.php', 
    './controllers/TourController.php',
    './controllers/DepartureController.php',
    './controllers/BookingController.php',
    './controllers/GuideController.php'
    './controllers/GuideController.php',
    './controllers/GuestController.php'
];

foreach ($controllers as $file) {
    if (file_exists($file)) require_once $file;
}

// ==================== ROUTES ====================
$routes = [
    '/'                 => fn() => require './views/login.php',
    'home'              => fn() => require './views/login.php',
    'login'             => fn() => require './views/login.php',

    // ==================== ADMIN ====================
    'admin_login'       => ['AdminController', 'login'],
    'admin_dashboard'   => ['AdminController', 'dashboard'],
    'admin_logout'      => ['AdminController', 'logout'],

    'admin_tours'                   => ['TourController', 'adminList'],
    'admin_tours_create'            => ['TourController', 'adminCreate'],
    'admin_tours_edit'              => ['TourController', 'adminEdit'],
    'admin_tours_update'            => ['TourController', 'adminUpdate'],
    'admin_tours_delete'            => ['TourController', 'adminDelete'],
    'admin_tours_itinerary'         => ['TourController', 'adminItinerary'],
    'admin_tours_itinerary_add'     => ['TourController', 'adminAddItinerary'],
    'admin_tours_itinerary_edit'    => ['TourController', 'adminEditItinerary'],
    'admin_tours_itinerary_delete'  => ['TourController', 'adminDeleteItinerary'],

    'admin_departures'         => ['DepartureController', 'adminList'],
    'admin_departures_create'  => ['DepartureController', 'adminCreate'],
    'admin_departures_edit'    => ['DepartureController', 'adminEdit'],
    'admin_departures_delete'  => ['DepartureController', 'adminDelete'],

    'admin_bookings'                => ['BookingController', 'adminList'],
    'admin_bookings_view'           => ['BookingController', 'adminView'],
    'admin_bookings_confirm'        => ['BookingController', 'adminConfirm'],
    'admin_bookings_cancel'         => ['BookingController', 'adminCancel'],
    'admin_bookings_update_status'  => ['BookingController', 'adminUpdateStatus'],

    // ==================== HƯỚNG DẪN VIÊN ====================
    'guide_login'            => fn() => require './views/login.php',
    'guide_dashboard'        => ['AdminController', 'guideDashboard'],
    'guide_logout'           => ['AdminController', 'guideLogout'],
    'guide_my_tours'         => ['AdminController', 'guideMyTours'],
    'guide_tour_detail'      => ['AdminController', 'guideTourDetail'],
    'guide_journal'          => ['AdminController', 'guideJournal'],
    'guide_attendance'       => ['AdminController', 'guideAttendance'],
    'guide_participants'     => ['AdminController', 'guideParticipants'],
    'guide_special_requests' => ['AdminController', 'guideSpecialRequests'],

    // Departure Detail & Assignment
    'admin_departure_detail' => ['DepartureController', 'adminDetail'],
    'admin_departure_add_assignment' => ['DepartureController', 'adminAddAssignment'],
    'admin_departure_add_resource' => ['DepartureController', 'adminAddResource'],
    'admin_departure_delete_assignment' => ['DepartureController', 'adminDeleteAssignment'],
    'admin_departure_delete_resource' => ['DepartureController', 'adminDeleteResource'],
    'admin_departure_update_assignment_status' => ['DepartureController', 'adminUpdateAssignmentStatus'],
    'admin_departure_update_resource_status' => ['DepartureController', 'adminUpdateResourceStatus'],
    'admin_add_checklist' => ['DepartureController', 'adminAddChecklist'],
    'admin_update_checklist_status' => ['DepartureController', 'adminUpdateChecklistStatus'],
    'admin_delete_checklist' => ['DepartureController', 'adminDeleteChecklist'],

    // Guide Management Routes
    'admin_guides' => ['GuideController', 'adminList'],
    'admin_guides_create' => ['GuideController', 'adminCreate'],
    'admin_guides_edit' => ['GuideController', 'adminEdit'],
    'admin_guides_delete' => ['GuideController', 'adminDelete'],
    'admin_guides_view' => ['GuideController', 'adminView'],

    // Guide Category Management
    'admin_guide_categories' => ['GuideController', 'adminCategories'],
    'admin_guide_category_create' => ['GuideController', 'adminCategoryCreate'],
    'admin_guide_category_edit' => ['GuideController', 'adminCategoryEdit'],
    'admin_guide_category_delete' => ['GuideController', 'adminCategoryDelete'],
];

// Lấy act
$act = $_GET['act'] ?? '/';

if (array_key_exists($act, $routes)) {
    $route = $routes[$act];

    if (is_callable($route)) {
        $route();
    } else {
        [$controller, $method] = $route;
        $obj = new $controller();

        if ($act === 'guide_tour_detail') {
            $obj->$method($_GET['id'] ?? 0);
        } else {
            $obj->$method();
        }
    }
} else {
    require './views/login.php';
}
?>
match ($act) {
    // Trang chủ
    '/' => (new ProductController())->Home(),
    
    // Admin Routes
    'admin_login' => (new AdminController())->login(),
    'admin_dashboard' => (new AdminController())->dashboard(),
    'admin_logout' => (new AdminController())->logout(),
    
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
    // Guide Management Routes
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
// Guest Management Routes
'admin_guest_management' => (new GuestController())->adminGuestManagement(),
'ajax_get_departures' => (new GuestController())->ajaxGetDepartures(),
'admin_guest_detail' => (new GuestController())->adminGuestDetail(),
'ajax_get_guest_info' => (new GuestController())->ajaxGetGuestInfo(),
'updateGuestInfo' => (new GuestController())->updateGuestInfo(),
'updateCheckStatus' => (new GuestController())->updateCheckStatus(),
'assignRoom' => (new GuestController())->assignRoom(),
'showGuestList' => (new GuestController())->showGuestList(),
'showRoomList' => (new GuestController())->showRoomList(),
'returnRoom' => (new GuestController())->returnRoom(),

    
    // Room Management Routes
    'delete_room' => (new GuestController())->deleteRoom(),
    'edit_room' => (new GuestController())->editRoom(),
    'print_special_notes' => (new GuestController())->printSpecialNotes(),
    default => (new ProductController())->Home()
};
?>
