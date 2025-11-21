<?php
// Load config
require_once './commons/env.php';
require_once './commons/function.php';

// Load Model & Controller
require_once './models/TourModel.php';
require_once './controllers/TourController.php';

// Kết nối DB
$db = connectDB();

// Lấy tham số act
$act = $_GET['act'] ?? 'home';

// Khởi tạo controller
$controller = new TourController($db);

// Router
match ($act) {
    'home'      => $controller->index(),
    'add'       => $controller->add(),
    'store'     => $controller->store(),
    'edit'      => $controller->edit(),
    'update'    => $controller->update(),
    'delete'    => $controller->delete(),

    default     => $controller->index(),
};
