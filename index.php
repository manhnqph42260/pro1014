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
    'ProductController.php',
    'AdminController.php',
    'TourController.php',
    'DepartureController.php',
    'BookingController.php',
    // 'GuideController.php' ← ĐÃ XÓA DÒNG NÀY
];

foreach ($controllers as $file) {
    $path = "./controllers/$file";
    if (file_exists($path)) require_once $path;
}

// ==================== ROUTES ====================
$routes = [
    // Trang khách
    '/'     => fn() => require './views/login.php',   // Trang chủ = login chung
    'home'  => fn() => require './views/login.php',   // gõ /home cũng ra login
    'login'             => fn() => require './views/login.php',

    // ==================== ADMIN ====================
    'admin_login'       => ['AdminController', 'login'],
    'admin_dashboard'   => ['AdminController', 'dashboard'],
    'admin_logout'      => ['AdminController', 'logout'],

    // Quản lý tour, lịch khởi hành, booking...
    'admin_tours'                   => ['TourController', 'adminList'],
    'admin_tours_create'            => ['TourController', 'adminCreate'],
    'admin_tours_edit'              => ['TourController', 'adminEdit'],
    'admin_tours_update'            => ['TourController', 'adminUpdate'],
    'admin_tours_delete'            => ['TourController', 'adminDelete'],
    'admin_tours_itinerary'         => ['TourController', 'adminItinerary'],
    'admin_tours_itinerary_add'     => ['TourController', 'adminAddItinerary'],
    'admin_tours_itinerary_edit'    => ['TourController', 'adminEditItinerary'],
    'admin_tours_itinerary_delete'  => ['TourController', 'adminDeleteItinerary'],
    'admin_departures'              => ['DepartureController', 'adminList'],
    'admin_departures_create'       => ['DepartureController', 'adminCreate'],
    'admin_departures_edit'          => ['DepartureController', 'adminEdit'],
    'admin_departures_delete'       => ['DepartureController', 'adminDelete'],
    'admin_bookings'                 => ['BookingController', 'adminList'],
    'admin_bookings_view'            => ['BookingController', 'adminView'],
    'admin_bookings_confirm'         => ['BookingController', 'adminConfirm'],
    'admin_bookings_cancel'          => ['BookingController', 'adminCancel'],
    'admin_bookings_update_status'   => ['BookingController', 'adminUpdateStatus'],
    // HƯỚNG DẪN VIÊN (dùng chung login + dashboard riêng)
    'guide_login'       => fn() => require './views/login.php',
    'guide_dashboard'   => ['AdminController', 'guideDashboard'],
    'guide_logout'      => ['AdminController', 'guideLogout'],
    'guide_my_tours'    => ['AdminController', 'guideMyTours'],
    'guide_tour_detail' => ['AdminController', 'guideTourDetail'],
    'guide_journal'     => ['AdminController', 'guideJournal'],
    'guide_attendance'  => fn() => require './views/admin/guides/attendance.php',
    'guide_participants' => fn() => require './views/admin/guides/tour_participants.php',
    'guide_special_requests' => fn() => require './views/admin/guides/special_requests.php',

    // Nếu admin bấm vào "Quản lý HDV" → chuyển về login HDV
    'admin_guides' => fn() => header('Location: index.php?act=guide_login') && exit,
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
