<?php
class BookingController {
    
    // Danh sách booking
    public function adminList() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        require_once './models/BookingModel.php';
        
        $search = $_GET['search'] ?? '';
        $status_filter = $_GET['status'] ?? '';
        
        $bookings = BookingModel::getAll($search, $status_filter);
        $stats = BookingModel::getStats();
        
        require_once './views/admin/booking/list.php';
    }
    
    // Tạo booking mới
    public function adminCreate() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        require_once './models/BookingModel.php';
        
        $departures = BookingModel::getAvailableDepartures();
        
        if ($_POST) {
            try {
                $conn = connectDB();
                $conn->beginTransaction();
                
                // Generate booking code
                $booking_code = 'BK' . date('Ymd') . rand(100, 999);
                
                // Calculate totals
                $adult_count = intval($_POST['adult_count']);
                $child_count = intval($_POST['child_count']);
                $infant_count = intval($_POST['infant_count']);
                $total_guests = $adult_count + $child_count + $infant_count;
                
                // Get departure info for pricing và kiểm tra số chỗ
                $departure_stmt = $conn->prepare("
                    SELECT 
                        d.departure_id,
                        d.price_adult, 
                        d.price_child, 
                        d.expected_slots,
                        COALESCE((
                            SELECT SUM(b2.total_guests) 
                            FROM bookings b2 
                            WHERE b2.departure_id = d.departure_id 
                            AND b2.status != 'cancelled'
                        ), 0) as booked_slots
                    FROM departure_schedules d
                    WHERE d.departure_id = ?
                ");
                $departure_stmt->execute([$_POST['departure_id']]);
                $departure = $departure_stmt->fetch();
                
                if (!$departure) {
                    throw new Exception("Không tìm thấy thông tin lịch khởi hành!");
                }
                
                // Check available slots
                $expected_slots = $departure['expected_slots'] ?? 0;
                $booked_slots = $departure['booked_slots'] ?? 0;
                $available_slots = $expected_slots - $booked_slots;
                
                if ($available_slots < $total_guests) {
                    throw new Exception("Chỉ còn " . $available_slots . " chỗ trống. Không đủ cho " . $total_guests . " khách.");
                }
                
                $total_amount = ($adult_count * $departure['price_adult']) + ($child_count * $departure['price_child']);
                $deposit_amount = $total_amount * 0.3;
                
                // Insert booking
                $booking_data = [
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
                ];
                
                $booking_id = BookingModel::create($booking_data);
                
                // Insert guest details
                if (isset($_POST['guest_names']) && is_array($_POST['guest_names'])) {
                    foreach ($_POST['guest_names'] as $index => $guest_name) {
                        if (!empty(trim($guest_name))) {
                            $guest_data = [
                                $booking_id,
                                trim($guest_name),
                                $_POST['guest_dobs'][$index] ?? null,
                                $_POST['guest_genders'][$index] ?? null,
                                $_POST['guest_types'][$index] ?? 'adult'
                            ];
                            BookingGuestModel::create($guest_data);
                        }
                    }
                }
                
                $conn->commit();
                
                $_SESSION['success'] = "Tạo booking thành công! Mã booking: " . $booking_code;
                header("Location: ?act=admin_bookings_view&id=" . $booking_id);
                exit();
                
            } catch (Exception $e) {
                if (isset($conn)) {
                    $conn->rollBack();
                }
                $error = "Lỗi khi tạo booking: " . $e->getMessage();
            }
        }
        
        require_once './views/admin/booking/create.php';
    }
    
    // Xem chi tiết booking - XÓA REQUIRE PaymentModel
    public function adminView() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        require_once './models/BookingModel.php';
        
        $booking_id = $_GET['id'] ?? 0;
        $booking = BookingModel::getById($booking_id);
        
        if (!$booking) {
            $_SESSION['error'] = "Booking không tồn tại!";
            header("Location: ?act=admin_bookings");
            exit();
        }
        
        // GỌI TRỰC TIẾP CÁC MODEL
        $guests = BookingGuestModel::getByBookingId($booking_id);
        $payments = PaymentModel::getByBookingId($booking_id);
        $total_paid = PaymentModel::getTotalPaid($booking_id);
        
        require_once './views/admin/booking/view.php';
    }
    
    // Chỉnh sửa booking
    public function adminEdit() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        require_once './models/BookingModel.php';
        
        $booking_id = $_GET['id'] ?? 0;
        $booking = BookingModel::getById($booking_id);
        
        if (!$booking) {
            $_SESSION['error'] = "Booking không tồn tại!";
            header("Location: ?act=admin_bookings");
            exit();
        }
        
        $departures = BookingModel::getAvailableDepartures();
        $guests = BookingGuestModel::getByBookingId($booking_id);
        
        if ($_POST) {
            try {
                $adult_count = intval($_POST['adult_count']);
                $child_count = intval($_POST['child_count']);
                $infant_count = intval($_POST['infant_count']);
                $total_guests = $adult_count + $child_count + $infant_count;
                
                $update_data = [
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
                    'total' => $booking['total_amount']
                ];
                
                BookingModel::update($booking_id, $update_data);
                
                // Xóa khách cũ và thêm mới
                BookingGuestModel::deleteByBookingId($booking_id);
                
                if (isset($_POST['guest_names']) && is_array($_POST['guest_names'])) {
                    foreach ($_POST['guest_names'] as $index => $guest_name) {
                        if (!empty(trim($guest_name))) {
                            $guest_data = [
                                $booking_id,
                                trim($guest_name),
                                $_POST['guest_dobs'][$index] ?? null,
                                $_POST['guest_genders'][$index] ?? null,
                                $_POST['guest_types'][$index] ?? 'adult'
                            ];
                            BookingGuestModel::create($guest_data);
                        }
                    }
                }
                
                $_SESSION['success'] = "Cập nhật booking thành công!";
                header("Location: ?act=admin_bookings_view&id=" . $booking_id);
                exit();
                
            } catch (Exception $e) {
                $error = "Lỗi khi cập nhật booking: " . $e->getMessage();
            }
        }
        
        require_once './views/admin/booking/edit.php';
    }
    
    // Xác nhận booking
    public function adminConfirm() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        require_once './models/BookingModel.php';
        
        $booking_id = $_GET['id'] ?? 0;
        
        try {
            BookingModel::confirm($booking_id, $_SESSION['admin_id']);
            $_SESSION['success'] = "Xác nhận booking thành công!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi xác nhận booking: " . $e->getMessage();
        }
        
        header("Location: ?act=admin_bookings_view&id=" . $booking_id);
        exit();
    }
    
    // Hủy booking
    public function adminCancel() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        require_once './models/BookingModel.php';
        
        $booking_id = $_GET['id'] ?? 0;
        
        try {
            $conn = connectDB();
            $conn->beginTransaction();
            
            // Lấy thông tin booking để trả lại số chỗ
            $booking = BookingModel::getById($booking_id);
            
            // Cập nhật trạng thái booking
            BookingModel::cancel($booking_id);
            
            // Trả lại số chỗ trống
            $update_slots = $conn->prepare("UPDATE departure_schedules SET available_slots = available_slots + ? WHERE departure_id = ?");
            $update_slots->execute([$booking['total_guests'], $booking['departure_id']]);
            
            $conn->commit();
            
            $_SESSION['success'] = "Hủy booking thành công!";
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['error'] = "Lỗi khi hủy booking: " . $e->getMessage();
        }
        
        header("Location: ?act=admin_bookings_view&id=" . $booking_id);
        exit();
    }
    
    // Thêm thanh toán - XÓA REQUIRE PaymentModel
    public function adminAddPayment() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        require_once './models/BookingModel.php';
        
        $booking_id = $_GET['id'] ?? 0;
        $booking = BookingModel::getById($booking_id);
        
        if ($_POST) {
            try {
                $transaction_code = $_POST['transaction_code'] ?? 'PMT' . date('YmdHis') . rand(100, 999);
                
                $payment_data = [
                    'booking_id' => $booking_id,
                    'amount' => $_POST['amount'],
                    'method' => $_POST['payment_method'],
                    'date' => $_POST['payment_date'],
                    'status' => $_POST['status'],
                    'code' => $transaction_code,
                    'notes' => $_POST['notes'] ?? '',
                    'created_by' => $_SESSION['admin_id']
                ];
                
                // GỌI TRỰC TIẾP PaymentModel
                PaymentModel::create($payment_data);
                
                $_SESSION['success'] = "Thêm thanh toán thành công!";
                header("Location: ?act=admin_bookings_view&id=" . $booking_id);
                exit();
                
            } catch (Exception $e) {
                $error = "Lỗi khi thêm thanh toán: " . $e->getMessage();
            }
        }
        
        require_once './views/admin/booking/add_payment.php';
    }
    
    // Xóa thanh toán - XÓA REQUIRE PaymentModel
    public function adminDeletePayment() {
        $this->checkAdminAuth();
        require_once './commons/env.php';
        require_once './commons/function.php';
        require_once './models/BookingModel.php';
        
        $payment_id = $_GET['payment_id'] ?? 0;
        $booking_id = $_GET['booking_id'] ?? 0;
        
        try {
            // GỌI TRỰC TIẾP PaymentModel
            PaymentModel::delete($payment_id);
            $_SESSION['success'] = "Xóa thanh toán thành công!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi xóa thanh toán: " . $e->getMessage();
        }
        
        header("Location: ?act=admin_bookings_view&id=" . $booking_id);
        exit();
    }
    
    // Kiểm tra đăng nhập admin
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=admin_login");
            exit();
        }
    }
}
?>