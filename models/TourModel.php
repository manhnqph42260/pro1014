<?php
class TourModel {

    private $conn;
    private $table = "tours";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả tour
    public function getAllTours()
    {
        $sql = "SELECT * FROM $this->table ORDER BY tour_id DESC";
        return $this->conn->query($sql)->fetchAll();
    }

    // Lấy 1 tour theo ID
    public function getTourById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE tour_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Thêm mới tour
    public function insertTour($data)
    {
        $sql = "INSERT INTO $this->table (tour_name, price, description, start_date, duration, destination, available_seats, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$data['tour_name'], 
        $data['price'], 
        $data['description'], 
        $data['start_date'],
        $data['duration'],
        $data['destination'],
        $data['available_seats'],
        $data['image'],
        $data['status']]);
    }

    // Cập nhật tour
    public function updateTour($id, $data)
    {
        $sql = "UPDATE $this->table SET tour_name=?, price=?, description=?, start_date=?, duration=?, destination=?, available_seats=?, image=?, status=? WHERE tour_id=?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$data['tour_name'], 
        $data['price'], 
        $data['description'],
        $data['start_date'],
        $data['duration'],
        $data['destination'],
        $data['available_seats'],
        $data['image'],
        $data['status'],
        $id]);
    }

    // Xóa tour
    public function deleteTour($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE tour_id=?");
        return $stmt->execute([$id]);
    }
}
