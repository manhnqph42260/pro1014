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