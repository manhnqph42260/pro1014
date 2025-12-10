<?php
class GuideModel
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    public function checkLogin($username, $password)
    {
        $sql = "SELECT * FROM guides WHERE username = :u AND status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch();
        if ($user && ($user['password_hash'] === $password || password_verify($password, $user['password_hash']))) {
            return $user;
        }
        return false;
    }

    public function getAssignedTours($guide_id)
    {
        $sql = "SELECT 
                ds.departure_id,
                ds.departure_date,
                ds.departure_time,  
                ds.meeting_point,
                t.tour_name,
                t.tour_code,
                t.duration_days
            FROM departure_assignments da
            JOIN departure_schedules ds ON da.departure_id = ds.departure_id
            JOIN tours t ON ds.tour_id = t.tour_id
            WHERE da.guide_id = :gid 
            AND da.status = 'confirmed'
            ORDER BY 
                -- Ưu tiên 1: Tương lai (0) lên trước, Quá khứ (1) xuống dưới
                CASE WHEN ds.departure_date >= CURDATE() THEN 0 ELSE 1 END ASC,
                -- Ưu tiên 2: Sắp xếp theo ngày tăng dần (Ngày gần nhất lên đầu)
                ds.departure_date ASC, 
                ds.departure_time ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':gid' => $guide_id]);
        return $stmt->fetchAll();
    }

    public function getDepartureDetail($departure_id)
    {
        $sql = "SELECT ds.*, t.tour_name, t.tour_code 
                FROM departure_schedules ds 
                JOIN tours t ON ds.tour_id = t.tour_id 
                WHERE ds.departure_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetch();
    }

   public function getPassengersByDeparture($departure_id) {
        $sql = "SELECT 
                    bg.guest_id, bg.full_name, bg.gender, bg.birth_date, bg.type, 
                    bg.attendance_status, bg.attendance_note, -- Lấy thêm cột này
                    b.booking_code,
                    b.customer_phone,
                    b.customer_name as booker_name -- Lấy tên người đặt
                FROM bookings b
                JOIN booking_guests bg ON b.booking_id = bg.booking_id
                WHERE b.departure_id = :did 
                ORDER BY b.booking_code, bg.full_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':did' => $departure_id]);
        return $stmt->fetchAll();
    }

    public function createIncident($data)
    {
        $sql = "INSERT INTO incident_reports 
                (departure_id, guide_id, incident_type, severity, title, description, action_taken) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['departure_id'],
            $data['guide_id'],
            $data['incident_type'],
            $data['severity'],
            $data['title'],
            $data['description'],
            $data['action_taken']
        ]);
    }

    // THÊM HÀM MỚI: LƯU ĐIỂM DANH
    public function saveAttendance($guest_id, $status, $note) {
        $sql = "UPDATE booking_guests 
                SET attendance_status = :status, 
                    attendance_note = :note 
                WHERE guest_id = :guest_id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':note' => $note,
            ':guest_id' => $guest_id
        ]);
    }

    /**
     * 6. Lấy danh sách các điểm đến của Tour
     */
    public function getTourItinerary($tour_id) {
        $sql = "SELECT * FROM tour_itineraries WHERE tour_id = :tid ORDER BY ordering ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':tid' => $tour_id]);
        return $stmt->fetchAll();
    }

    /**
     * 7. Lấy danh sách các điểm ĐÃ điểm danh xong của chuyến đi này
     */
    public function getCompletedCheckpoints($departure_id) {
        $sql = "SELECT itinerary_id FROM departure_checkpoints WHERE departure_id = :did";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':did' => $departure_id]);
        // Trả về mảng dạng [1, 2, 5] (các ID đã xong)
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * 8. Lưu trạng thái đã hoàn thành điểm danh tại 1 điểm
     */
    public function saveCheckpoint($departure_id, $itinerary_id) {
        // Kiểm tra xem đã có chưa để tránh trùng
        $sqlCheck = "SELECT id FROM departure_checkpoints WHERE departure_id = ? AND itinerary_id = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([$departure_id, $itinerary_id]);
        
        if (!$stmtCheck->fetch()) {
            $sql = "INSERT INTO departure_checkpoints (departure_id, itinerary_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$departure_id, $itinerary_id]);
        }
    }
}
