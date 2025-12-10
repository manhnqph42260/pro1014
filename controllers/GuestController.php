<?php
require_once './commons/env.php';
require_once './commons/function.php';

class GuestController {
    
    private $conn;
    
    public function __construct() {
        $this->conn = connectDB();
    }
    
    // Danh sách khách hàng theo tour/departure
    public function adminGuestManagement() {
        $this->checkAdminAuth();
        
        // Lấy danh sách tour
        $tours = $this->conn->query("SELECT tour_id, tour_code, tour_name FROM tours ORDER BY tour_name")->fetchAll();
        
        $tour_id = $_GET['tour_id'] ?? 0;
        $departure_id = $_GET['departure_id'] ?? 0;
        $search = $_GET['search'] ?? '';
        
        $departures = [];
        $guests = [];
        $rooms = [];
        $guest_stats = [];
        $departure = null;
        
        // Nếu có tour_id, lấy danh sách departure
        if ($tour_id > 0) {
            $departures = $this->conn->prepare("
                SELECT d.*, t.tour_name, t.tour_code 
                FROM departure_schedules d
                JOIN tours t ON d.tour_id = t.tour_id
                WHERE d.tour_id = ? AND d.departure_date >= CURDATE()
                ORDER BY d.departure_date ASC
            ");
            $departures->execute([$tour_id]);
            $departures = $departures->fetchAll();
        }
        
        // Nếu có departure_id, lấy danh sách khách
        if ($departure_id > 0) {
            // Lấy thông tin departure
            $departure = $this->conn->prepare("
                SELECT d.*, t.tour_name, t.tour_code 
                FROM departure_schedules d
                JOIN tours t ON d.tour_id = t.tour_id
                WHERE d.departure_id = ?
            ");
            $departure->execute([$departure_id]);
            $departure = $departure->fetch();
            
            // Query lấy danh sách khách
            $query = "
                SELECT 
                    bg.guest_id,
                    bg.full_name,
                    bg.date_of_birth,
                    bg.gender,
                    bg.id_number,
                    bg.guest_type,
                    bg.check_status,
                    bg.check_in_time,
                    bg.check_out_time,
                    bg.special_requests,
                    bg.dietary_restrictions,
                    bg.medical_notes,
                    bg.emergency_contact,
                    bg.passport_number,
                    bg.passport_expiry,
                    bg.nationality,
                    b.booking_id,
                    b.booking_code,
                    b.customer_name as booker_name,
                    b.customer_phone,
                    b.customer_email,
                    b.booking_type,
                    b.group_name,
                    b.company_name,
                    b.status as booking_status,
                    b.booked_at,
                    b.confirmed_at,
                    ra.room_number,
                    ra.hotel_name,
                    ra.room_type,
                    ra.check_in_date,
                    ra.check_out_date,
                    ra.notes as room_notes
                FROM booking_guests bg
                JOIN bookings b ON bg.booking_id = b.booking_id
                LEFT JOIN room_assignments ra ON bg.guest_id = ra.guest_id AND ra.departure_id = ?
                WHERE b.departure_id = ? 
                AND b.status != 'cancelled'
            ";
            
            $params = [$departure_id, $departure_id];
            
            if ($search) {
                $query .= " AND (bg.full_name LIKE ? OR b.booking_code LIKE ? OR bg.id_number LIKE ? OR b.customer_phone LIKE ?)";
                $search_param = "%$search%";
                $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
            }
            
            $query .= " ORDER BY bg.full_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $guests = $stmt->fetchAll();
            
            // Lấy thống kê
            $stats_query = "
                SELECT 
                    COUNT(*) as total_guests,
                    SUM(CASE WHEN bg.guest_type = 'adult' THEN 1 ELSE 0 END) as adults,
                    SUM(CASE WHEN bg.guest_type = 'child' THEN 1 ELSE 0 END) as children,
                    SUM(CASE WHEN bg.guest_type = 'infant' THEN 1 ELSE 0 END) as infants,
                    SUM(CASE WHEN bg.check_status = 'checked_in' THEN 1 ELSE 0 END) as checked_in,
                    SUM(CASE WHEN bg.check_status IS NULL OR bg.check_status = '' THEN 1 ELSE 0 END) as not_checked
                FROM booking_guests bg
                JOIN bookings b ON bg.booking_id = b.booking_id
                WHERE b.departure_id = ? AND b.status != 'cancelled'
            ";
            $stats_stmt = $this->conn->prepare($stats_query);
            $stats_stmt->execute([$departure_id]);
            $guest_stats = $stats_stmt->fetch();
            
            // Lấy danh sách phòng đã phân
            $rooms_query = "
                SELECT ra.*, bg.full_name 
                FROM room_assignments ra
                JOIN booking_guests bg ON ra.guest_id = bg.guest_id
                WHERE ra.departure_id = ?
                ORDER BY ra.hotel_name, ra.room_number
            ";
            $rooms_stmt = $this->conn->prepare($rooms_query);
            $rooms_stmt->execute([$departure_id]);
            $rooms = $rooms_stmt->fetchAll();
        }
        
        require_once './views/admin/guests/management.php';
    }
    public function editSpecialNotes() {
    $this->checkAdminAuth();
    
    $guest_id = $_GET['guest_id'] ?? 0;
    $departure_id = $_GET['departure_id'] ?? 0;
    
    if (!$guest_id) {
        $_SESSION['error'] = "Không tìm thấy khách hàng!";
        header("Location: ?act=admin_guest_management" . ($departure_id ? "&departure_id=" . $departure_id : ""));
        exit();
    }
    
    // Lấy thông tin khách
    $stmt = $this->conn->prepare("
        SELECT 
            bg.*,
            b.booking_code,
            t.tour_name,
            d.departure_date
        FROM booking_guests bg
        JOIN bookings b ON bg.booking_id = b.booking_id
        JOIN departure_schedules d ON b.departure_id = d.departure_id
        JOIN tours t ON d.tour_id = t.tour_id
        WHERE bg.guest_id = ?
    ");
    $stmt->execute([$guest_id]);
    $guest = $stmt->fetch();
    
    if (!$guest) {
        $_SESSION['error'] = "Khách hàng không tồn tại!";
        header("Location: ?act=admin_guest_management");
        exit();
    }
    
    // Lấy các trường mở rộng nếu có
    $extended_fields = $this->conn->prepare("
        SELECT field_name, field_value 
        FROM guest_extended_fields 
        WHERE guest_id = ?
    ");
    $extended_fields->execute([$guest_id]);
    $extended_data = $extended_fields->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Merge extended fields vào guest array
    foreach ($extended_data as $field => $value) {
        $guest[$field] = $value;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $this->conn->beginTransaction();
            
            // Cập nhật các trường cơ bản
            $update_basic = $this->conn->prepare("
                UPDATE booking_guests SET
                    dietary_restrictions = ?,
                    medical_notes = ?,
                    special_requests = ?,
                    emergency_contact = ?,
                    updated_at = NOW()
                WHERE guest_id = ?
            ");
            
            $dietary = implode(', ', $_POST['dietary_options'] ?? []);
            if (!empty($_POST['dietary_other'])) {
                $dietary .= ' - ' . $_POST['dietary_other'];
            }
            
            $medical = implode(', ', $_POST['medical_conditions'] ?? []);
            if (!empty($_POST['medical_other'])) {
                $medical .= ' - ' . $_POST['medical_other'];
            }
            
            $emergency_contact = $_POST['emergency_contact_name'] . ' - ' . $_POST['emergency_contact_phone'];
            if (!empty($_POST['emergency_relationship'])) {
                $emergency_contact .= ' (' . $_POST['emergency_relationship'] . ')';
            }
            
            $update_basic->execute([
                $dietary,
                $medical,
                $_POST['special_requests'] ?? '',
                $emergency_contact,
                $guest_id
            ]);
            
            // Xóa các trường mở rộng cũ
            $delete_old = $this->conn->prepare("DELETE FROM guest_extended_fields WHERE guest_id = ?");
            $delete_old->execute([$guest_id]);
            
            // Thêm các trường mở rộng mới
            $extended_fields_to_save = [
                'food_allergies' => $_POST['food_allergies'] ?? '',
                'medications' => $_POST['medications'] ?? '',
                'blood_type' => $_POST['blood_type'] ?? '',
                'emergency_notes' => $_POST['emergency_notes'] ?? '',
                'room_requests' => json_encode($_POST['room_requests'] ?? []),
                'room_requests_other' => $_POST['room_requests_other'] ?? '',
                'transport_requests' => json_encode($_POST['transport_requests'] ?? []),
                'transport_requests_other' => $_POST['transport_requests_other'] ?? '',
                'hobbies_interests' => $_POST['hobbies_interests'] ?? '',
                'travel_history' => $_POST['travel_history'] ?? '',
                'emergency_contact_name' => $_POST['emergency_contact_name'] ?? '',
                'emergency_contact_phone' => $_POST['emergency_contact_phone'] ?? '',
                'emergency_relationship' => $_POST['emergency_relationship'] ?? '',
                'emergency_contact_email' => $_POST['emergency_contact_email'] ?? '',
                'emergency_contact_address' => $_POST['emergency_contact_address'] ?? '',
                'notes_for_guide' => $_POST['notes_for_guide'] ?? '',
                'notes_for_hotel' => $_POST['notes_for_hotel'] ?? '',
                'requires_special_attention' => $_POST['requires_special_attention'] ?? 0,
                'last_updated_by' => $_SESSION['admin_id'],
                'last_updated_at' => date('Y-m-d H:i:s')
            ];
            
            $insert_extended = $this->conn->prepare("
                INSERT INTO guest_extended_fields (guest_id, field_name, field_value)
                VALUES (?, ?, ?)
            ");
            
            foreach ($extended_fields_to_save as $field => $value) {
                if (!empty($value)) {
                    $insert_extended->execute([$guest_id, $field, $value]);
                }
            }
            
            $this->conn->commit();
            
            $_SESSION['success'] = "Cập nhật ghi chú đặc biệt thành công!";
            header("Location: ?act=edit_special_notes&guest_id=" . $guest_id . "&departure_id=" . $departure_id);
            exit();
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            $error = "Lỗi khi cập nhật: " . $e->getMessage();
        }
    }
    
    require_once './views/admin/guests/edit_special_notes.php';
}

    // Xem chi tiết khách hàng
    public function adminGuestDetail() {
        $this->checkAdminAuth();
        
        $guest_id = $_GET['guest_id'] ?? 0;
        $departure_id = $_GET['departure_id'] ?? 0;
        
        if (!$guest_id) {
            $_SESSION['error'] = "Không tìm thấy khách hàng!";
            header("Location: ?act=admin_guest_management");
            exit();
        }
        
        // Lấy thông tin chi tiết khách
        $query = "
            SELECT 
                bg.*,
                b.booking_id,
                b.booking_code,
                b.customer_name as booker_name,
                b.customer_phone,
                b.customer_email,
                b.customer_address,
                b.booking_type,
                b.group_name,
                b.company_name,
                b.status as booking_status,
                b.booked_at,
                b.confirmed_at,
                d.departure_id,
                d.departure_date,
                d.departure_time,
                d.meeting_point,
                t.tour_id,
                t.tour_code,
                t.tour_name,
                ra.room_number,
                ra.hotel_name,
                ra.room_type,
                ra.check_in_date,
                ra.check_out_date,
                ra.notes as room_notes
            FROM booking_guests bg
            JOIN bookings b ON bg.booking_id = b.booking_id
            JOIN departure_schedules d ON b.departure_id = d.departure_id
            JOIN tours t ON d.tour_id = t.tour_id
            LEFT JOIN room_assignments ra ON bg.guest_id = ra.guest_id AND ra.departure_id = d.departure_id
            WHERE bg.guest_id = ?
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$guest_id]);
        $guest = $stmt->fetch();
        
        if (!$guest) {
            $_SESSION['error'] = "Khách hàng không tồn tại!";
            header("Location: ?act=admin_guest_management");
            exit();
        }
        
        require_once './views/admin/guests/detail.php';
    }
    
    // AJAX: Cập nhật trạng thái check-in
    public function updateCheckStatus() {
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');
        
        try {
            $guest_id = $_POST['guest_id'] ?? 0;
            $check_status = $_POST['check_status'] ?? '';
            $notes = $_POST['notes'] ?? '';
            
            if (!$guest_id || !$check_status) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
                exit();
            }
            
            $query = "UPDATE booking_guests SET check_status = ?, check_in_time = NOW() WHERE guest_id = ?";
            if ($check_status == 'no_show') {
                $query = "UPDATE booking_guests SET check_status = ?, check_in_time = NULL WHERE guest_id = ?";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$check_status, $guest_id]);
            
            echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit();
    }
    
    // AJAX: Phân phòng
    public function assignRoom() {
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');
        
        try {
            $guest_id = $_POST['guest_id'] ?? 0;
            $departure_id = $_POST['departure_id'] ?? 0;
            $hotel_name = $_POST['hotel_name'] ?? '';
            $room_number = $_POST['room_number'] ?? '';
            $room_type = $_POST['room_type'] ?? 'double';
            $check_in_date = $_POST['check_in_date'] ?? null;
            $check_out_date = $_POST['check_out_date'] ?? null;
            $notes = $_POST['notes'] ?? '';
            
            if (!$guest_id || !$departure_id || !$hotel_name || !$room_number) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
                exit();
            }
            
            // Kiểm tra xem khách đã có phòng chưa
            $check_stmt = $this->conn->prepare("
                SELECT * FROM room_assignments 
                WHERE guest_id = ? AND departure_id = ?
            ");
            $check_stmt->execute([$guest_id, $departure_id]);
            $existing = $check_stmt->fetch();
            
            if ($existing) {
                // Cập nhật phòng hiện tại
                $query = "
                    UPDATE room_assignments SET 
                        hotel_name = ?,
                        room_number = ?,
                        room_type = ?,
                        check_in_date = ?,
                        check_out_date = ?,
                        notes = ?
                    WHERE assignment_id = ?
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([
                    $hotel_name,
                    $room_number,
                    $room_type,
                    $check_in_date,
                    $check_out_date,
                    $notes,
                    $existing['assignment_id']
                ]);
            } else {
                // Thêm phòng mới
                $query = "
                    INSERT INTO room_assignments 
                    (departure_id, guest_id, hotel_name, room_number, room_type, check_in_date, check_out_date, notes)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([
                    $departure_id,
                    $guest_id,
                    $hotel_name,
                    $room_number,
                    $room_type,
                    $check_in_date,
                    $check_out_date,
                    $notes
                ]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Phân phòng thành công']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit();
    }
    
    // AJAX: Trả phòng
    public function returnRoom() {
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');
        
        try {
            $assignment_id = $_POST['assignment_id'] ?? 0;
            
            if (!$assignment_id) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin phòng']);
                exit();
            }
            
            $query = "DELETE FROM room_assignments WHERE assignment_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$assignment_id]);
            
            echo json_encode(['success' => true, 'message' => 'Đã trả phòng thành công']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit();
    }
    
    // In danh sách đoàn
    public function showGuestList() {
        $this->checkAdminAuth();
        
        $departure_id = $_GET['departure_id'] ?? 0;
        
        if (!$departure_id) {
            $_SESSION['error'] = "Vui lòng chọn lịch khởi hành";
            header("Location: ?act=admin_guest_management");
            exit();
        }
        
        // Lấy thông tin departure
        $departure = $this->conn->prepare("
            SELECT d.*, t.tour_name, t.tour_code, t.duration_days 
            FROM departure_schedules d
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE d.departure_id = ?
        ");
        $departure->execute([$departure_id]);
        $departure = $departure->fetch();
        
        // Lấy danh sách khách
        $guests = $this->conn->prepare("
            SELECT 
                bg.*,
                b.booking_code,
                b.customer_name as booker_name,
                b.customer_phone,
                b.booking_type,
                b.group_name,
                b.company_name
            FROM booking_guests bg
            JOIN bookings b ON bg.booking_id = b.booking_id
            WHERE b.departure_id = ? AND b.status != 'cancelled'
            ORDER BY bg.full_name
        ");
        $guests->execute([$departure_id]);
        $guest_list = $guests->fetchAll();
        
        // Thống kê
        $stats = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_guests,
                SUM(CASE WHEN bg.guest_type = 'adult' THEN 1 ELSE 0 END) as adults,
                SUM(CASE WHEN bg.guest_type = 'child' THEN 1 ELSE 0 END) as children,
                SUM(CASE WHEN bg.check_status = 'checked_in' THEN 1 ELSE 0 END) as checked_in
            FROM booking_guests bg
            JOIN bookings b ON bg.booking_id = b.booking_id
            WHERE b.departure_id = ? AND b.status != 'cancelled'
        ");
        $stats->execute([$departure_id]);
        $stats = $stats->fetch();
        
        require_once './views/admin/guests/print_guest_list.php';
    }
    
    // In danh sách phòng
    public function showRoomList() {
        $this->checkAdminAuth();
        
        $departure_id = $_GET['departure_id'] ?? 0;
        
        if (!$departure_id) {
            $_SESSION['error'] = "Vui lòng chọn lịch khởi hành";
            header("Location: ?act=admin_guest_management");
            exit();
        }
        
        // Lấy thông tin departure
        $departure = $this->conn->prepare("
            SELECT d.*, t.tour_name, t.tour_code 
            FROM departure_schedules d
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE d.departure_id = ?
        ");
        $departure->execute([$departure_id]);
        $departure = $departure->fetch();
        
        // Lấy danh sách phòng
        $rooms = $this->conn->prepare("
            SELECT 
                ra.*,
                bg.full_name,
                bg.gender,
                bg.guest_type,
                b.booking_code
            FROM room_assignments ra
            JOIN booking_guests bg ON ra.guest_id = bg.guest_id
            JOIN bookings b ON bg.booking_id = b.booking_id
            WHERE ra.departure_id = ?
            ORDER BY ra.hotel_name, ra.room_number
        ");
        $rooms->execute([$departure_id]);
        $room_list = $rooms->fetchAll();
        
        // Nhóm theo khách sạn và phòng
        $hotels = [];
        foreach ($room_list as $room) {
            $hotel_key = $room['hotel_name'] . '_' . $room['room_number'];
            if (!isset($hotels[$hotel_key])) {
                $hotels[$hotel_key] = [
                    'hotel_name' => $room['hotel_name'],
                    'room_number' => $room['room_number'],
                    'room_type' => $room['room_type'],
                    'check_in_date' => $room['check_in_date'],
                    'check_out_date' => $room['check_out_date'],
                    'guests' => []
                ];
            }
            $hotels[$hotel_key]['guests'][] = $room;
        }
        
        require_once './views/admin/guests/print_room_list.php';
    }
    
    // AJAX: Lấy thông tin khách
    public function ajaxGetGuestInfo() {
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');
        
        $guest_id = $_GET['guest_id'] ?? 0;
        
        if (!$guest_id) {
            echo json_encode(['error' => 'Không tìm thấy khách']);
            exit();
        }
        
        $stmt = $this->conn->prepare("SELECT * FROM booking_guests WHERE guest_id = ?");
        $stmt->execute([$guest_id]);
        $guest = $stmt->fetch();
        
        echo json_encode($guest ?: []);
        exit();
    }
    
    // AJAX: Cập nhật thông tin khách
    public function updateGuestInfo() {
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');
        
        try {
            $guest_id = $_POST['guest_id'] ?? 0;
            $field = $_POST['field'] ?? '';
            $value = $_POST['value'] ?? '';
            
            if (!$guest_id || !$field) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
                exit();
            }
            
            $allowed_fields = ['full_name', 'date_of_birth', 'gender', 'id_number', 'guest_type', 
                             'special_requests', 'dietary_restrictions', 'medical_notes', 
                             'emergency_contact', 'passport_number', 'passport_expiry', 'nationality'];
            
            if (!in_array($field, $allowed_fields)) {
                echo json_encode(['success' => false, 'message' => 'Trường không hợp lệ']);
                exit();
            }
            
            $query = "UPDATE booking_guests SET $field = ? WHERE guest_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$value, $guest_id]);
            
            echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit();
    }
    
    // AJAX: Lấy departure theo tour
    public function ajaxGetDepartures() {
        $this->checkAdminAuth();
        
        header('Content-Type: application/json');
        
        $tour_id = $_GET['tour_id'] ?? 0;
        
        if (!$tour_id) {
            echo json_encode([]);
            exit();
        }
        
        $stmt = $this->conn->prepare("
            SELECT departure_id, departure_date, meeting_point 
            FROM departure_schedules 
            WHERE tour_id = ? AND departure_date >= CURDATE()
            ORDER BY departure_date ASC
        ");
        $stmt->execute([$tour_id]);
        $departures = $stmt->fetchAll();
        
        // Format date
        foreach ($departures as &$dep) {
            $dep['formatted_date'] = date('d/m/Y', strtotime($dep['departure_date']));
        }
        
        echo json_encode($departures);
        exit();
    }
    
    // Chỉnh sửa thông tin phòng (Edit room)
    public function editRoom() {
    $this->checkAdminAuth();
    
    $assignment_id = $_GET['id'] ?? 0;
    $departure_id = $_GET['departure_id'] ?? 0;
    
    if (!$assignment_id || !$departure_id) {
        $_SESSION['error'] = "Thiếu thông tin cần thiết!";
        header("Location: ?act=admin_guest_management&departure_id=" . $departure_id);
        exit();
    }
    
    // SỬA: Lấy thông tin phòng với đúng assignment_id
    $stmt = $this->conn->prepare("
        SELECT ra.*, bg.full_name, b.booking_code
        FROM room_assignments ra
        JOIN booking_guests bg ON ra.guest_id = bg.guest_id
        JOIN bookings b ON bg.booking_id = b.booking_id
        WHERE ra.assignment_id = ? AND ra.departure_id = ?
    ");
    $stmt->execute([$assignment_id, $departure_id]);
    $room_info = $stmt->fetch();
    
    if (!$room_info) {
        $_SESSION['error'] = "Không tìm thấy thông tin phòng!";
        header("Location: ?act=admin_guest_management&departure_id=" . $departure_id);
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $update_data = [
                'hotel_name' => $_POST['hotel_name'],
                'room_number' => $_POST['room_number'],
                'room_type' => $_POST['room_type'],
                'check_in_date' => !empty($_POST['check_in_date']) ? $_POST['check_in_date'] : null,
                'check_out_date' => !empty($_POST['check_out_date']) ? $_POST['check_out_date'] : null,
                'notes' => $_POST['notes'] ?? ''
            ];
            
            $update_query = "UPDATE room_assignments SET 
                hotel_name = :hotel_name,
                room_number = :room_number,
                room_type = :room_type,
                check_in_date = :check_in_date,
                check_out_date = :check_out_date,
                notes = :notes
                WHERE assignment_id = :assignment_id";
            
            $update_data['assignment_id'] = $assignment_id;
            
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->execute($update_data);
            
            $_SESSION['success'] = "Cập nhật thông tin phòng thành công!";
            header("Location: ?act=admin_guest_management&departure_id=" . $departure_id);
            exit();
            
        } catch (Exception $e) {
            $error = "Lỗi khi cập nhật: " . $e->getMessage();
        }
    }
    
    require_once './views/admin/guests/edit_room.php';
}
    
    // Xóa phòng
    public function deleteRoom() {
    $this->checkAdminAuth();
    
    $assignment_id = $_GET['id'] ?? 0;
    $departure_id = $_GET['departure_id'] ?? 0;
    
    if (!$assignment_id || !$departure_id) {
        $_SESSION['error'] = "Thiếu thông tin cần thiết!";
        header("Location: ?act=admin_guest_management");
        exit();
    }
    
    try {
        $stmt = $this->conn->prepare("DELETE FROM room_assignments WHERE assignment_id = ?");
        $stmt->execute([$assignment_id]);
        
        $_SESSION['success'] = "Xóa phòng thành công!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Lỗi khi xóa phòng: " . $e->getMessage();
    }
    
    header("Location: ?act=admin_guest_management&departure_id=" . $departure_id);
    exit();
}
    
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=admin_login");
            exit();
        }
    }
}
?>