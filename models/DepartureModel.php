<?php
class DepartureModel {
    
    private $conn;
    private $table = "departure_schedules";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả lịch khởi hành
    public function getAllDepartures() {
        $sql = "
            SELECT 
                d.*,
                t.tour_name,
                t.tour_code,
                t.duration_days
            FROM {$this->table} d
            JOIN tours t ON d.tour_id = t.tour_id
            ORDER BY d.departure_date DESC
        ";
        return $this->conn->query($sql)->fetchAll();
    }

    // Lấy lịch khởi hành theo ID
    public function getDepartureById($id) {
        $sql = "
            SELECT 
                d.*,
                t.tour_name,
                t.tour_code
            FROM {$this->table} d
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE d.departure_id = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Tạo lịch khởi hành mới
    public function createDeparture($data) {
        $sql = "
            INSERT INTO {$this->table} 
            (tour_id, departure_date, departure_time, meeting_point, expected_slots, price_adult, price_child, operational_notes, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_id'],
            $data['departure_date'],
            $data['departure_time'],
            $data['meeting_point'],
            $data['expected_slots'],
            $data['price_adult'],
            $data['price_child'],
            $data['operational_notes'],
            $data['created_by']
        ]);
    }

    // Xóa lịch khởi hành
    public function deleteDeparture($id) {
        // Kiểm tra xem có booking nào không
        $check_sql = "SELECT COUNT(*) as booking_count FROM bookings WHERE departure_id = ?";
        $check_stmt = $this->conn->prepare($check_sql);
        $check_stmt->execute([$id]);
        $result = $check_stmt->fetch();

        if ($result['booking_count'] > 0) {
            throw new Exception("Không thể xóa lịch khởi hành đã có booking!");
        }

        // Xóa lịch khởi hành
        $delete_sql = "DELETE FROM {$this->table} WHERE departure_id = ?";
        $delete_stmt = $this->conn->prepare($delete_sql);
        return $delete_stmt->execute([$id]);
    }

    // Cập nhật lịch khởi hành
    public function updateDeparture($id, $data) {
        $sql = "
            UPDATE {$this->table} SET 
            tour_id = ?, departure_date = ?, departure_time = ?, meeting_point = ?, 
            expected_slots = ?, price_adult = ?, price_child = ?, operational_notes = ?
            WHERE departure_id = ?
        ";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_id'],
            $data['departure_date'],
            $data['departure_time'],
            $data['meeting_point'],
            $data['expected_slots'],
            $data['price_adult'],
            $data['price_child'],
            $data['operational_notes'],
            $id
        ]);
    }

    // Kiểm tra số chỗ còn lại
    public function getAvailableSlots($departure_id) {
        $sql = "
            SELECT 
                d.expected_slots,
                COALESCE(SUM(b.total_guests), 0) as booked_slots,
                (d.expected_slots - COALESCE(SUM(b.total_guests), 0)) as available_slots
            FROM {$this->table} d
            LEFT JOIN bookings b ON d.departure_id = b.departure_id AND b.status != 'cancelled'
            WHERE d.departure_id = ?
            GROUP BY d.departure_id
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetch();
    }
}
?>