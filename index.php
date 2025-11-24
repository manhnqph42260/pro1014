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
    './controllers/DepartureController.php'
];

foreach ($controllers as $controller) {
    if (file_exists($controller)) {
        require_once $controller;
    }
}

// Route
$act = $_GET['act'] ?? '/';

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
    'admin_bookings' => (new BookingController())->adminList(),
    'admin_bookings_create' => (new BookingController())->adminCreate(),
    'admin_bookings_view' => (new BookingController())->adminView(),
    'admin_bookings_confirm' => (new BookingController())->adminConfirm(),
    
    default => (new ProductController())->Home()
};
?>