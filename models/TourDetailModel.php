<?php
class TourDetailModel {

    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    // Lấy tất cả lịch trình theo tour
    public function getDetailsByTour($tour_id){
        $stmt = $this->conn->prepare("SELECT * FROM tour_details WHERE tour_id = ?");
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    // Lấy 1 lịch trình
    public function getDetail($id){
        $stmt = $this->conn->prepare("SELECT * FROM tour_details WHERE detail_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Thêm lịch trình
    public function insertDetail($data){
        $stmt = $this->conn->prepare("
            INSERT INTO tour_details (tour_id, day_number, title, description, image, policy, tags)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['tour_id'],
            $data['day_number'],
            $data['title'],
            $data['description'],
            $data['image'],
            $data['policy'],
            $data['tags']
        ]);
    }

    // Cập nhật
    public function updateDetail($id, $data){
        $stmt = $this->conn->prepare("
            UPDATE tour_details SET
                day_number=?, title=?, description=?, image=?, policy=?, tags=?
            WHERE detail_id=?
        ");
        return $stmt->execute([
            $data['day_number'],
            $data['title'],
            $data['description'],
            $data['image'],
            $data['policy'],
            $data['tags'],
            $id
        ]);
    }

    // Xóa
    public function deleteDetail($id){
        $stmt = $this->conn->prepare("DELETE FROM tour_details WHERE detail_id = ?");
        return $stmt->execute([$id]);
    }
}
