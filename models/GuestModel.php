<?php
class GuestModel {
    
    private $conn;
    private $table = "booking_guests";

    public function __construct($db) {
        $this->conn = $db;
    }
    // Lấy thông tin chi tiết khách hàng với đầy đủ thông tin
public function getGuestDetailWithAllInfo($guest_id) {
    $sql = "SELECT 
                bg.*,
                b.booking_code,
                b.customer_name as booker_name,
                b.customer_phone,
                b.customer_email,
                b.customer_address,
                b.booking_type,
                b.group_name,
                b.company_name,
                b.adult_count,
                b.child_count,
                b.infant_count,
                b.total_guests,
                b.special_requests as booking_special_requests,
                b.total_amount,
                b.deposit_amount,
                b.status as booking_status,
                b.booked_at,
                b.confirmed_at,
                d.departure_id,
                d.departure_date,
                d.departure_time,
                d.meeting_point,
                d.expected_slots,
                d.price_adult,
                d.price_child,
                t.tour_id,
                t.tour_name,
                t.tour_code,
                t.description as tour_description,
                ra.room_number,
                ra.hotel_name,
                ra.room_type,
                ra.check_in_date,
                ra.check_out_date,
                ra.notes as room_notes,
                ga.person_name as guide_name,
                ga.role as guide_role,
                ga.contact_info as guide_contact
            FROM booking_guests bg
            JOIN bookings b ON bg.booking_id = b.booking_id
            JOIN departure_schedules d ON b.departure_id = d.departure_id
            JOIN tours t ON d.tour_id = t.tour_id
            LEFT JOIN room_assignments ra ON bg.guest_id = ra.guest_id AND ra.departure_id = d.departure_id
            LEFT JOIN departure_assignments ga ON d.departure_id = ga.departure_id AND ga.assignment_type = 'guide'
            WHERE bg.guest_id = ?
            LIMIT 1";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$guest_id]);
    return $stmt->fetch();
}
    // Lấy tất cả khách theo departure_id
       public function getGuestsByDeparture($departure_id) {
        $sql = "SELECT 
                    g.*,
                    b.booking_code,
                    b.customer_name as booking_customer,
                    b.customer_phone,
                    b.booking_type,
                    b.status as booking_status,
                    d.departure_date,
                    t.tour_name,
                    t.tour_code
                FROM {$this->table} g
                JOIN bookings b ON g.booking_id = b.booking_id
                JOIN departure_schedules d ON b.departure_id = d.departure_id
                JOIN tours t ON d.tour_id = t.tour_id
                WHERE b.departure_id = ? AND b.status != 'cancelled'
                ORDER BY b.booking_id, g.guest_type, g.full_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetchAll();
    }

    // Lấy khách theo booking_id
    public function getGuestsByBooking($booking_id) {
        $sql = "SELECT g.*, b.booking_code FROM {$this->table} g
                JOIN bookings b ON g.booking_id = b.booking_id
                WHERE g.booking_id = ?
                ORDER BY g.guest_type, g.full_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id]);
        return $stmt->fetchAll();
    }

    // Tìm kiếm khách với bộ lọc
    public function searchGuests($filters) {
        $sql = "SELECT 
                    g.*,
                    b.booking_code,
                    b.customer_name as booking_customer,
                    b.customer_phone,
                    b.booking_type,
                    b.status as booking_status,
                    d.departure_date,
                    t.tour_name,
                    t.tour_code
                FROM {$this->table} g
                JOIN bookings b ON g.booking_id = b.booking_id
                JOIN departure_schedules d ON b.departure_id = d.departure_id
                JOIN tours t ON d.tour_id = t.tour_id
                WHERE b.status != 'cancelled'";
        
        $params = [];
        
        if (!empty($filters['departure_id'])) {
            $sql .= " AND b.departure_id = ?";
            $params[] = $filters['departure_id'];
        }
        
        if (!empty($filters['booking_id'])) {
            $sql .= " AND g.booking_id = ?";
            $params[] = $filters['booking_id'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND g.check_status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND d.departure_date >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND d.departure_date <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (g.full_name LIKE ? OR g.id_number LIKE ? OR b.booking_code LIKE ? OR b.customer_name LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        }
        
        $sql .= " ORDER BY d.departure_date DESC, b.booking_id, g.guest_type, g.full_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Thêm khách mới (phiên bản đầy đủ)
    public function createGuest($data) {
        $sql = "INSERT INTO {$this->table} 
                (booking_id, full_name, date_of_birth, gender, id_number, guest_type,
                 dietary_restrictions, medical_notes, special_requests, emergency_contact) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['booking_id'],
            $data['full_name'],
            $data['date_of_birth'] ?? null,
            $data['gender'] ?? null,
            $data['id_number'] ?? null,
            $data['guest_type'] ?? 'adult',
            $data['dietary_restrictions'] ?? null,
            $data['medical_notes'] ?? null,
            $data['special_requests'] ?? null,
            $data['emergency_contact'] ?? null
        ]);
    }

    // Cập nhật thông tin khách (phiên bản đầy đủ)
    public function updateGuest($guest_id, $data) {
        $sql = "UPDATE {$this->table} SET 
                full_name = ?,
                date_of_birth = ?,
                gender = ?,
                id_number = ?,
                guest_type = ?,
                dietary_restrictions = ?,
                medical_notes = ?,
                special_requests = ?,
                emergency_contact = ?
                WHERE guest_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['full_name'],
            $data['date_of_birth'] ?? null,
            $data['gender'] ?? null,
            $data['id_number'] ?? null,
            $data['guest_type'] ?? 'adult',
            $data['dietary_restrictions'] ?? null,
            $data['medical_notes'] ?? null,
            $data['special_requests'] ?? null,
            $data['emergency_contact'] ?? null,
            $guest_id
        ]);
    }

    // Xóa khách
    public function deleteGuest($guest_id) {
        $sql = "DELETE FROM {$this->table} WHERE guest_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$guest_id]);
    }

    // Lấy thống kê khách theo departure
    public function getGuestStats($departure_id, $status_filter = 'all') {
        $sql = "SELECT 
                    COUNT(*) as total_guests,
                    SUM(CASE WHEN g.guest_type = 'adult' THEN 1 ELSE 0 END) as adults,
                    SUM(CASE WHEN g.guest_type = 'child' THEN 1 ELSE 0 END) as children,
                    SUM(CASE WHEN g.guest_type = 'infant' THEN 1 ELSE 0 END) as infants,
                    SUM(CASE WHEN g.gender = 'male' THEN 1 ELSE 0 END) as male,
                    SUM(CASE WHEN g.gender = 'female' THEN 1 ELSE 0 END) as female,
                    SUM(CASE WHEN g.check_status = 'checked_in' THEN 1 ELSE 0 END) as checked_in,
                    SUM(CASE WHEN g.check_status = 'no_show' THEN 1 ELSE 0 END) as no_show,
                    COUNT(DISTINCT g.booking_id) as total_bookings
                FROM {$this->table} g
                JOIN bookings b ON g.booking_id = b.booking_id
                WHERE b.departure_id = ? AND b.status != 'cancelled'";
        
        $params = [$departure_id];
        
        if ($status_filter !== 'all') {
            $sql .= " AND g.check_status = ?";
            $params[] = $status_filter;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    // Check-in/Check-out khách
    public function updateGuestStatus($guest_id, $status, $check_time = null) {
        $sql = "";
        $params = [];
        
        if ($status === 'checked_in') {
            $sql = "UPDATE {$this->table} SET 
                    check_status = 'checked_in',
                    check_in_time = ?
                    WHERE guest_id = ?";
            $params = [$check_time ?: date('Y-m-d H:i:s'), $guest_id];
        } elseif ($status === 'checked_out') {
            $sql = "UPDATE {$this->table} SET 
                    check_status = 'checked_out',
                    check_out_time = ?
                    WHERE guest_id = ?";
            $params = [$check_time ?: date('Y-m-d H:i:s'), $guest_id];
        } elseif ($status === 'no_show') {
            $sql = "UPDATE {$this->table} SET 
                    check_status = 'no_show'
                    WHERE guest_id = ?";
            $params = [$guest_id];
        }
        
        if ($sql) {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        }
        
        return false;
    }

    // Lấy thông tin chi tiết khách
    public function getGuestDetail($guest_id) {
        $sql = "SELECT 
                    g.*,
                    b.booking_code,
                    b.customer_name as booking_customer,
                    b.customer_phone,
                    b.customer_email,
                    b.booking_type,
                    b.status as booking_status,
                    d.departure_id,
                    d.departure_date,
                    t.tour_name,
                    t.tour_code
                FROM {$this->table} g
                JOIN bookings b ON g.booking_id = b.booking_id
                JOIN departure_schedules d ON b.departure_id = d.departure_id
                JOIN tours t ON d.tour_id = t.tour_id
                WHERE g.guest_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$guest_id]);
        return $stmt->fetch();
    }
}

// Model cho phân phòng khách sạn
class RoomAssignmentModel {
    
    private $conn;
    private $table = "room_assignments";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo table room_assignments nếu chưa có
    public function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `room_assignments` (
                `assignment_id` int NOT NULL AUTO_INCREMENT,
                `departure_id` int NOT NULL,
                `guest_id` int NOT NULL,
                `room_number` varchar(50) NOT NULL,
                `room_type` enum('single','double','triple','family','suite') DEFAULT 'double',
                `hotel_name` varchar(255) DEFAULT NULL,
                `check_in_date` date DEFAULT NULL,
                `check_out_date` date DEFAULT NULL,
                `notes` text,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`assignment_id`),
                UNIQUE KEY `unique_guest_room` (`guest_id`, `departure_id`),
                KEY `departure_id` (`departure_id`),
                KEY `guest_id` (`guest_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
        
        return $this->conn->exec($sql);
    }

    // Phân phòng cho khách
    public function assignRoom($data) {
        $sql = "INSERT INTO {$this->table} 
                (departure_id, guest_id, room_number, room_type, hotel_name, 
                 check_in_date, check_out_date, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                room_number = VALUES(room_number),
                room_type = VALUES(room_type),
                hotel_name = VALUES(hotel_name),
                check_in_date = VALUES(check_in_date),
                check_out_date = VALUES(check_out_date),
                notes = VALUES(notes)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['departure_id'],
            $data['guest_id'],
            $data['room_number'],
            $data['room_type'] ?? 'double',
            $data['hotel_name'] ?? null,
            $data['check_in_date'] ?? null,
            $data['check_out_date'] ?? null,
            $data['notes'] ?? ''
        ]);
    }

    // Lấy danh sách phân phòng theo departure
    public function getRoomAssignments($departure_id) {
        $sql = "SELECT 
                    ra.*,
                    g.full_name,
                    g.gender,
                    g.guest_type,
                    b.booking_code,
                    b.customer_name as booking_customer
                FROM {$this->table} ra
                JOIN booking_guests g ON ra.guest_id = g.guest_id
                JOIN bookings b ON g.booking_id = b.booking_id
                WHERE ra.departure_id = ?
                ORDER BY ra.hotel_name, ra.room_number, ra.room_type";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetchAll();
    }

    // Xóa phân phòng
    public function deleteAssignment($assignment_id) {
        $sql = "DELETE FROM {$this->table} WHERE assignment_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$assignment_id]);
    }

    // Lấy thống kê phòng
    public function getRoomStats($departure_id) {
        $sql = "SELECT 
                    COUNT(DISTINCT room_number) as total_rooms,
                    COUNT(*) as total_guests,
                    room_type,
                    COUNT(*) as count
                FROM {$this->table}
                WHERE departure_id = ?
                GROUP BY room_type";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetchAll();
    }

    // Lấy thông tin phân phòng của một khách
    public function getGuestRoomAssignment($guest_id, $departure_id) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE guest_id = ? AND departure_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$guest_id, $departure_id]);
        return $stmt->fetch();
    }
}
?>