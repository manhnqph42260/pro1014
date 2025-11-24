<?php
class BookingController {
    
    public function adminList() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $conn = connectDB();
        
        // Xử lý tìm kiếm và filter
        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status'] ?? '';
        
        $query = "SELECT b.*, t.tour_name, d.departure_date 
                  FROM bookings b
                  JOIN departure_schedules d ON b.departure_id = d.departure_id
                  JOIN tours t ON d.tour_id = t.tour_id
                  WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (b.booking_code LIKE :search OR b.customer_name LIKE :search OR b.customer_phone LIKE :search)";
            $params['search'] = "%$search%";
        }
        
        if (!empty($status_filter)) {
            $query .= " AND b.status = :status";
            $params['status'] = $status_filter;
        }
        
        $query .= " ORDER BY b.booked_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();
        
        // Thống kê
        $stats = $conn->query("
            SELECT 
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(total_guests) as total_guests
            FROM bookings
        ")->fetch();
        
        require_once './views/admin/bookings/list.php';
    }
    
    public function adminCreate() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $conn = connectDB();
        
        // Lấy danh sách lịch khởi hành sắp tới
        $departures = $conn->query("
            SELECT d.departure_id, t.tour_name, d.departure_date, d.available_slots, 
                   d.price_adult, d.price_child, t.duration_days
            FROM departure_schedules d
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE d.departure_date >= CURDATE() AND d.status = 'scheduled'
            ORDER BY d.departure_date ASC
        ")->fetchAll();
        
        if ($_POST) {
            try {
                $conn->beginTransaction();
                
                // Generate booking code
                $booking_code = 'BK' . date('Ymd') . rand(100, 999);
                
                // Calculate totals
                $adult_count = intval($_POST['adult_count']);
                $child_count = intval($_POST['child_count']);
                $infant_count = intval($_POST['infant_count']);
                $total_guests = $adult_count + $child_count + $infant_count;
                
                // Get departure info for pricing
                $departure_stmt = $conn->prepare("SELECT price_adult, price_child, available_slots FROM departure_schedules WHERE departure_id = ?");
                $departure_stmt->execute([$_POST['departure_id']]);
                $departure = $departure_stmt->fetch();
                
                // Check available slots
                if ($departure['available_slots'] < $total_guests) {
                    throw new Exception("Chỉ còn " . $departure['available_slots'] . " chỗ trống. Không đủ cho " . $total_guests . " khách.");
                }
                
                $total_amount = ($adult_count * $departure['price_adult']) + ($child_count * $departure['price_child']);
                $deposit_amount = $total_amount * 0.3; // 30% deposit
                
                // Insert booking
                $query = "INSERT INTO bookings (booking_code, departure_id, customer_name, customer_phone, customer_email, 
                          customer_address, booking_type, group_name, company_name, adult_count, child_count, 
                          infant_count, total_guests, special_requests, total_amount, deposit_amount, booked_by) 
                          VALUES (:code, :departure_id, :name, :phone, :email, :address, :type, :group_name, 
                          :company, :adults, :children, :infants, :total_guests, :requests, :total, :deposit, :booked_by)";
                
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    'code' => $booking_code,
                    'departure_id' => $_POST['departure_id'],
                    'name' => $_POST['customer_name'],
                    'phone' => $_POST['customer_phone'],
                    'email' => $_POST['customer_email'] ?? '',
                    'address' => $_POST['customer_address'] ?? '',
                    'type' => $_POST['booking_type'],
                    'group_name' => $_POST['group_name'] ?? '',
                    'company' => $_POST['company_name'] ?? '',
                    'adults' => $adult_count,
                    'children' => $child_count,
                    'infants' => $infant_count,
                    'total_guests' => $total_guests,
                    'requests' => $_POST['special_requests'] ?? '',
                    'total' => $total_amount,
                    'deposit' => $deposit_amount,
                    'booked_by' => $_SESSION['admin_id']
                ]);
                
                $booking_id = $conn->lastInsertId();
                
                // Insert guest details if provided
                if (isset($_POST['guest_names']) && is_array($_POST['guest_names'])) {
                    $guest_stmt = $conn->prepare("INSERT INTO booking_guests (booking_id, full_name, date_of_birth, gender, guest_type) VALUES (?, ?, ?, ?, ?)");
                    
                    foreach ($_POST['guest_names'] as $index => $guest_name) {
                        if (!empty(trim($guest_name))) {
                            $guest_type = $_POST['guest_types'][$index] ?? 'adult';
                            $guest_dob = $_POST['guest_dobs'][$index] ?? null;
                            $guest_gender = $_POST['guest_genders'][$index] ?? null;
                            
                            $guest_stmt->execute([
                                $booking_id,
                                trim($guest_name),
                                $guest_dob ?: null,
                                $guest_gender ?: null,
                                $guest_type
                            ]);
                        }
                    }
                }
                
                // Update available slots
                $update_slots = $conn->prepare("UPDATE departure_schedules SET available_slots = available_slots - ? WHERE departure_id = ?");
                $update_slots->execute([$total_guests, $_POST['departure_id']]);
                
                $conn->commit();
                
                $_SESSION['success'] = "Tạo booking thành công! Mã booking: " . $booking_code;
                header("Location: ?act=admin_bookings_view&id=" . $booking_id);
                exit();
                
            } catch (Exception $e) {
                $conn->rollBack();
                $error = "Lỗi khi tạo booking: " . $e->getMessage();
            }
        }
        
        require_once './views/admin/bookings/create.php';
    }
    
    public function adminView() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $booking_id = $_GET['id'] ?? 0;
        $conn = connectDB();
        
        // Lấy thông tin booking
        $query = "SELECT b.*, t.tour_name, t.tour_code, d.departure_date, d.departure_time, 
                         d.meeting_point, a1.username as booked_by_name, a2.username as confirmed_by_name
                  FROM bookings b
                  JOIN departure_schedules d ON b.departure_id = d.departure_id
                  JOIN tours t ON d.tour_id = t.tour_id
                  LEFT JOIN admins a1 ON b.booked_by = a1.admin_id
                  LEFT JOIN admins a2 ON b.confirmed_by = a2.admin_id
                  WHERE b.booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            $_SESSION['error'] = "Booking không tồn tại!";
            header("Location: ?act=admin_bookings");
            exit();
        }
        
        // Lấy danh sách khách
        $guests = $conn->query("SELECT * FROM booking_guests WHERE booking_id = " . $booking_id)->fetchAll();
        
        // Lấy lịch sử thanh toán
        $payments = $conn->query("SELECT * FROM payments WHERE booking_id = " . $booking_id . " ORDER BY created_at DESC")->fetchAll();
        
        require_once './views/admin/bookings/view.php';
    }
    
    public function adminConfirm() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $booking_id = $_GET['id'] ?? 0;
        $conn = connectDB();
        
        try {
            $query = "UPDATE bookings SET status = 'confirmed', confirmed_by = ?, confirmed_at = NOW() WHERE booking_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$_SESSION['admin_id'], $booking_id]);
            
            $_SESSION['success'] = "Xác nhận booking thành công!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi xác nhận booking: " . $e->getMessage();
        }
        
        header("Location: ?act=admin_bookings_view&id=" . $booking_id);
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