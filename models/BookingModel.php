<?php
class BookingModel {
    
    // Lấy tất cả booking với filter
    public static function getAll($search = '', $status = '') {
        $conn = connectDB();
        
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
        
        if (!empty($status)) {
            $query .= " AND b.status = :status";
            $params['status'] = $status;
        }
        
        $query .= " ORDER BY b.booked_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Lấy booking theo ID
    public static function getById($booking_id) {
        $conn = connectDB();
        
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
        return $stmt->fetch();
    }
    
    // Tạo booking mới
    public static function create($data) {
        $conn = connectDB();
        
        $query = "INSERT INTO bookings (booking_code, departure_id, customer_name, customer_phone, customer_email, 
                  customer_address, booking_type, group_name, company_name, adult_count, child_count, 
                  infant_count, total_guests, special_requests, total_amount, deposit_amount, booked_by) 
                  VALUES (:code, :departure_id, :name, :phone, :email, :address, :type, :group_name, 
                  :company, :adults, :children, :infants, :total_guests, :requests, :total, :deposit, :booked_by)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute($data);
        return $conn->lastInsertId();
    }
    
    // Cập nhật booking
    public static function update($booking_id, $data) {
        $conn = connectDB();
        
        $query = "UPDATE bookings SET 
                  customer_name = :name, customer_phone = :phone, customer_email = :email,
                  customer_address = :address, booking_type = :type, group_name = :group_name,
                  company_name = :company, adult_count = :adults, child_count = :children,
                  infant_count = :infants, total_guests = :total_guests, 
                  special_requests = :requests, total_amount = :total
                  WHERE booking_id = :id";
        
        $data['id'] = $booking_id;
        $stmt = $conn->prepare($query);
        return $stmt->execute($data);
    }
    
    // Xác nhận booking
    public static function confirm($booking_id, $admin_id) {
        $conn = connectDB();
        
        $query = "UPDATE bookings SET status = 'confirmed', confirmed_by = ?, confirmed_at = NOW() WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        return $stmt->execute([$admin_id, $booking_id]);
    }
    
    // Hủy booking
    public static function cancel($booking_id) {
        $conn = connectDB();
        
        $query = "UPDATE bookings SET status = 'cancelled' WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        return $stmt->execute([$booking_id]);
    }
    
    // Lấy thống kê
    public static function getStats() {
        $conn = connectDB();
        
        $query = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(total_guests) as total_guests
                FROM bookings";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    // Lấy danh sách lịch khởi hành có sẵn
public static function getAvailableDepartures() {
    $conn = connectDB();
    
    $query = "
        SELECT 
            d.departure_id, 
            t.tour_name, 
            d.departure_date, 
            d.expected_slots,
            COALESCE((
                SELECT SUM(b2.total_guests) 
                FROM bookings b2 
                WHERE b2.departure_id = d.departure_id 
                AND b2.status != 'cancelled'
            ), 0) as booked_slots,
            (d.expected_slots - COALESCE((
                SELECT SUM(b2.total_guests) 
                FROM bookings b2 
                WHERE b2.departure_id = d.departure_id 
                AND b2.status != 'cancelled'
            ), 0)) as available_slots,
            d.price_adult, 
            d.price_child, 
            t.duration_days
        FROM departure_schedules d
        JOIN tours t ON d.tour_id = t.tour_id
        WHERE d.departure_date >= CURDATE() 
          AND d.status = 'scheduled'
        HAVING available_slots > 0
        ORDER BY d.departure_date ASC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}
}

// Model cho Booking Guests

class BookingGuestModel {
    
    public static function getByBookingId($booking_id) {
        $conn = connectDB();
        
        $query = "SELECT * FROM booking_guests WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$booking_id]);
        return $stmt->fetchAll();
    }
    
    public static function create($data) {
        $conn = connectDB();
        
        $query = "INSERT INTO booking_guests (booking_id, full_name, date_of_birth, gender, guest_type) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        return $stmt->execute($data);
    }
    
    public static function deleteByBookingId($booking_id) {
        $conn = connectDB();
        
        $query = "DELETE FROM booking_guests WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        return $stmt->execute([$booking_id]);
    }
}



// Model cho Payments

class PaymentModel {
    
    public static function getByBookingId($booking_id) {
        $conn = connectDB();
        
        $query = "SELECT * FROM payments WHERE booking_id = ? ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute([$booking_id]);
        return $stmt->fetchAll();
    }
    
    public static function create($data) {
        $conn = connectDB();
        
        $query = "INSERT INTO payments (booking_id, amount, payment_method, payment_date, 
                  status, transaction_code, notes, created_by) 
                  VALUES (:booking_id, :amount, :method, :date, :status, :code, :notes, :created_by)";
        
        $stmt = $conn->prepare($query);
        return $stmt->execute($data);
    }
    
    public static function delete($payment_id) {
        $conn = connectDB();
        
        $query = "DELETE FROM payments WHERE payment_id = ?";
        $stmt = $conn->prepare($query);
        return $stmt->execute([$payment_id]);
    }
    
    public static function getTotalPaid($booking_id) {
        $conn = connectDB();
        
        $query = "SELECT COALESCE(SUM(amount), 0) as total_paid 
                  FROM payments 
                  WHERE booking_id = ? AND status = 'completed'";
        $stmt = $conn->prepare($query);
        $stmt->execute([$booking_id]);
        $result = $stmt->fetch();
        return $result['total_paid'];
    }
}


// Model cho Reviews
class ReviewModel {
    
    public static function getAll($tour_id = null, $rating = null) {
        $conn = connectDB();
        
        $query = "SELECT r.*, t.tour_name, b.customer_name 
                  FROM reviews r
                  JOIN bookings b ON r.booking_id = b.booking_id
                  JOIN departure_schedules d ON b.departure_id = d.departure_id
                  JOIN tours t ON d.tour_id = t.tour_id
                  WHERE 1=1";
        $params = [];
        
        if (!empty($tour_id)) {
            $query .= " AND t.tour_id = :tour_id";
            $params['tour_id'] = $tour_id;
        }
        
        if (!empty($rating)) {
            $query .= " AND r.rating = :rating";
            $params['rating'] = $rating;
        }
        
        $query .= " ORDER BY r.created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public static function getById($review_id) {
        $conn = connectDB();
        
        $query = "SELECT r.*, t.tour_name, b.customer_name, b.customer_email
                  FROM reviews r
                  JOIN bookings b ON r.booking_id = b.booking_id
                  JOIN departure_schedules d ON b.departure_id = d.departure_id
                  JOIN tours t ON d.tour_id = t.tour_id
                  WHERE r.review_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$review_id]);
        return $stmt->fetch();
    }
    
    public static function create($data) {
        $conn = connectDB();
        
        $query = "INSERT INTO reviews (booking_id, rating, title, comment, customer_name, status) 
                  VALUES (:booking_id, :rating, :title, :comment, :customer_name, :status)";
        
        $stmt = $conn->prepare($query);
        return $stmt->execute($data);
    }
    
    public static function update($review_id, $data) {
        $conn = connectDB();
        
        $query = "UPDATE reviews SET 
                  rating = :rating, title = :title, comment = :comment, status = :status
                  WHERE review_id = :review_id";
        
        $data['review_id'] = $review_id;
        $stmt = $conn->prepare($query);
        return $stmt->execute($data);
    }
    
    public static function delete($review_id) {
        $conn = connectDB();
        
        $query = "DELETE FROM reviews WHERE review_id = ?";
        $stmt = $conn->prepare($query);
        return $stmt->execute([$review_id]);
    }
    
    public static function getTourStats($tour_id) {
        $conn = connectDB();
        
        $query = "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                  FROM reviews r
                  JOIN bookings b ON r.booking_id = b.booking_id
                  JOIN departure_schedules d ON b.departure_id = d.departure_id
                  WHERE d.tour_id = ? AND r.status = 'approved'";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$tour_id]);
        return $stmt->fetch();
    }
    
    public static function getByBookingId($booking_id) {
        $conn = connectDB();
        
        $query = "SELECT * FROM reviews WHERE booking_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$booking_id]);
        return $stmt->fetch();
    }
}
?>