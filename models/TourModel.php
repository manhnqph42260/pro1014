<?php
class TourModel {

    private $conn;
    private $table = "tours";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ==================== ADMIN METHODS ====================

    // Lấy tất cả tour cho admin
    public function getAllTours()
    {
        $sql = "SELECT * FROM $this->table ORDER BY created_at DESC";
        return $this->conn->query($sql)->fetchAll();
    }

    // Tìm kiếm tour cho admin
    public function searchTours($search = '', $status = '')
    {
        $sql = "SELECT * FROM $this->table WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (tour_name LIKE ? OR tour_code LIKE ? OR destination LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Thêm tour mới cho admin
    public function insertTourAdmin($data)
    {
        $sql = "INSERT INTO $this->table (tour_code, tour_name, description, destination, duration_days, price_adult, price_child, max_participants, status, featured_image, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_code'],
            $data['tour_name'],
            $data['description'],
            $data['destination'],
            $data['duration_days'],
            $data['price_adult'],
            $data['price_child'],
            $data['max_participants'],
            $data['status'],
            $data['featured_image'],
            $data['created_by']
        ]);
    }

    // Cập nhật tour cho admin
    public function updateTourAdmin($id, $data)
    {
        $sql = "UPDATE $this->table SET 
                tour_name = ?, description = ?, destination = ?, duration_days = ?, 
                price_adult = ?, price_child = ?, max_participants = ?, status = ?, featured_image = ?
                WHERE tour_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_name'],
            $data['description'],
            $data['destination'],
            $data['duration_days'],
            $data['price_adult'],
            $data['price_child'],
            $data['max_participants'],
            $data['status'],
            $data['featured_image'],
            $id
        ]);
    }

    // ==================== FRONTEND METHODS ====================

    // Lấy 1 tour theo ID
    public function getTourById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE tour_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Thêm mới tour (frontend)
    public function insertTour($data)
    {
        $sql = "INSERT INTO $this->table (tour_name, price, description, start_date, duration, destination, available_seats, image, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_name'], 
            $data['price'], 
            $data['description'], 
            $data['start_date'],
            $data['duration'],
            $data['destination'],
            $data['available_seats'],
            $data['image'],
            $data['status']
        ]);
    }

    // Cập nhật tour (frontend)
    public function updateTour($id, $data)
    {
        $sql = "UPDATE $this->table SET 
                tour_name=?, price=?, description=?, start_date=?, duration=?, destination=?, available_seats=?, image=?, status=? 
                WHERE tour_id=?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_name'], 
            $data['price'], 
            $data['description'],
            $data['start_date'],
            $data['duration'],
            $data['destination'],
            $data['available_seats'],
            $data['image'],
            $data['status'],
            $id
        ]);
    }

    // Xóa tour
    public function deleteTour($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE tour_id=?");
        return $stmt->execute([$id]);
    }

    // ==================== UTILITY METHODS ====================

    // Đếm tổng số tour
    public function countTours()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM $this->table");
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Lấy tour theo trạng thái
    public function getToursByStatus($status)
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE status = ? ORDER BY created_at DESC");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
}
?>