<?php
class TourController {

    private $model;

    public function __construct($db)
    {
        $this->model = new TourModel($db);
    }

    // Trang danh sách
    public function index()
    {
        $tours = $this->model->getAllTours();
        include "./views/trangchu.php";
    }

    // Form thêm
    public function add()
    {
        include "./views/add.php";
    }

    // Xử lý thêm
    public function store()
    {
        $image = null;
        if (!empty($_FILES["image"]["name"])) {
            $image = time() . "_" . $_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $image);
        }
        $data = [
            'tour_name' => $_POST['tour_name'],
            'price'     => $_POST['price'],
            'description' => $_POST['description'],
            'start_date' => $_POST['start_date'],
            'duration' => $_POST['duration'],
            'destination' => $_POST['destination'],
            'available_seats' =>$_POST['available_seats'],
            'image' => $image,
            'status'=> $_POST['status']
        ];
        $this->model->insertTour($data);
        header("Location: index.php?act=home");
    }

    // Form sửa
    public function edit()
    {
        $id = $_GET['id'];
        $tour = $this->model->getTourById($id);
        include "./views/edit.php";
    }

    // Xử lý update
   public function update()
{
    $id = $_POST['tour_id'];

    // Lấy dữ liệu tour cũ
    $oldTour = $this->model->getTourById($id);

    // Xử lý ảnh
    $image = $oldTour['image']; // mặc định giữ ảnh cũ

    if (!empty($_FILES['image']['name'])) {

        // Xóa ảnh cũ nếu tồn tại
        if (!empty($oldTour['image']) && file_exists("uploads/" . $oldTour['image'])) {
            unlink("uploads/" . $oldTour['image']);
        }

        // Upload ảnh mới
        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

    // Chuẩn bị dữ liệu update
    $data = [
        'tour_name' => $_POST['tour_name'],
        'price'     => $_POST['price'],
        'description' => $_POST['description'],
        'start_date' => $_POST['start_date'],
        'duration' => $_POST['duration'],
        'destination' => $_POST['destination'],
        'available_seats' => $_POST['available_seats'],
        'image' => $image,
        'status' => $_POST['status']
    ];

    $this->model->updateTour($id, $data);
    header("Location: index.php?act=home");
}
    // Xóa tour
    public function delete()
    {
        $id = $_GET['id'];
        $this->model->deleteTour($id);
        header("Location: index.php?act=home");
    }
}
