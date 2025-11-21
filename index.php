<?php 
session_start();

// Require file Common
require_once './commons/env.php';
require_once './commons/function.php';

// Kiểm tra và require controllers với debug
$controllers = [
    './controllers/ProductController.php',
    './controllers/AdminController.php', 
    './controllers/TourController.php'
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
    
    // Tour Management
    'admin_tours' => (new TourController())->adminList(),
    'admin_tours_create' => (new TourController())->adminCreate(),
        // Tour Management - Admin
    'admin_tours' => (new TourController())->adminList(),
    'admin_tours_create' => (new TourController())->adminCreate(),
    'admin_tours_edit' => (new TourController())->adminEdit(),
    'admin_tours_delete' => (new TourController())->adminDelete(),
    'admin_tours_update' => (new TourController())->adminUpdate(),
    default => (new ProductController())->Home()
    
};

?>