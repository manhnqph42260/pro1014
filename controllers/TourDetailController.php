<?php
class TourDetailController {

    private $model;

    public function __construct($db){
        $this->model = new TourDetailModel($db);
    }

    // Danh sách lịch trình của 1 tour
    public function index(){
        $tour_id = $_GET['tour_id'];
        $details = $this->model->getDetailsByTour($tour_id);
        include "./views/detail/list.php";
    }

    // Form thêm
    public function add(){
        $tour_id = $_GET['tour_id'];
        include "./views/detail/add.php";
    }

    // Xử lý thêm
    public function store(){
        $image = null;
        if(!empty($_FILES['image']['name'])){
            $image = time()."_".$_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);
        }

        $data = [
            'tour_id' => $_POST['tour_id'],
            'day_number' => $_POST['day_number'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'image' => $image,
            'policy' => $_POST['policy'],
            'tags' => $_POST['tags']
        ];

        $this->model->insertDetail($data);
        header("Location: index.php?act=detail&tour_id=".$_POST['tour_id']);
    }

    // Form sửa
    public function edit(){
        $detail = $this->model->getDetail($_GET['id']);
        include "./views/detail/edit.php";
    }

    // Xử lý update
    public function update(){
        $id = $_POST['detail_id'];

        $image = $_POST['old_image'];
        if(!empty($_FILES['image']['name'])){
            $image = time()."_".$_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);
        }

        $data = [
            'day_number' => $_POST['day_number'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'image' => $image,
            'policy' => $_POST['policy'],
            'tags' => $_POST['tags']
        ];

        $this->model->updateDetail($id, $data);
        header("Location: index.php?act=detail&tour_id=".$_POST['tour_id']);
    }

    // Xóa
    public function delete(){
        $this->model->deleteDetail($_GET['id']);
        header("Location: index.php?act=detail&tour_id=".$_GET['tour_id']);
    }
}
