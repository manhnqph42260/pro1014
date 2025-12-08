<?php
// // có class chứa các function thực thi xử lý logic 
// class ProductController
// {
//     public $modelProduct;

//     public function __construct()
//     {
//         $this->modelProduct = new ProductModel();
//     }

//     public function Home()
//     {
//         $title = "Đây là trang chủ nhé hahaa";
//         $thoiTiet = "Hôm nay trời có vẻ là mưa";
//         require_once './views/trangchu.php';
//     }
// }


class ProductController {
    
    public function __construct() {
        // Bỏ dòng require_once hoặc khởi tạo Model
        // require_once './models/ProductModel.php';
        // $this->productModel = new ProductModel();
    }
    
    public function Home() {
        // Code hiển thị trang chủ
        echo "<h1>Trang chủ hệ thống quản lý Tour</h1>";
        echo "<p>Chào mừng đến với hệ thống!</p>";
        
        // Hiển thị link đến trang quản trị
        echo '<div style="margin-top: 20px;">';
        echo '<a href="?act=admin_login" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">';
        echo '🚀 Truy cập trang Quản trị';
        echo '</a>';
        echo '</div>';

    }

      
    // THÊM METHOD XỬ LÝ HDV
    public function admin_guides() {
        // Hiển thị trang quản lý HDV hoặc chuyển hướng đến trang login HDV
        header('Location: index.php?act=guide_login');
        exit();
    }
    
    // THÊM METHOD XỬ LÝ TRANG LOGIN HDV
    public function guide_login() {
        // Hiển thị trang đăng nhập HDV
        require_once './views/admin/guides/guide_login.php';
    }

}
?>