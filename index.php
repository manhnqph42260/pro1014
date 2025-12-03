<?php 
session_start();

// Require file Common
require_once './commons/env.php';
require_once './commons/function.php';

// Auto-load controllers
$controllers = [
    './controllers/ProductController.php',
    './controllers/AdminController.php', 
    './controllers/TourController.php',
    './controllers/DepartureController.php',
     './controllers/BookingController.php',
      './controllers/GuideController.php'
];

foreach ($controllers as $controller) {
    if (file_exists($controller)) {
        require_once $controller;
        echo "✅ Loaded: " . $controller . "<br>";
    } else {
        echo "❌ Not found: " . $controller . "<br>";
    }
}

// Route
$act = $_GET['act'] ?? '/';

echo "Current action: " . $act . "<br>";

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
    
    default => (new ProductController())->Home()
};
?>