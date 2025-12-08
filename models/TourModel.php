<?php
// models/TourModel.php - THÊM METHOD updateTourStatus

class TourModel
{
    private $db;

    public function __construct()
    {
        try {
            $host = DB_HOST;
            $dbname = DB_NAME;
            $username = DB_USERNAME;
            $password = DB_PASSWORD;

            $this->db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Lỗi kết nối database: " . $e->getMessage());
        }
    }
    
    // ... CÁC METHOD HIỆN CÓ ...

    /**
     * CẬP NHẬT TRẠNG THÁI TOUR
     */
    public function updateTourStatus($tourId, $status)
    {
        $sql = "UPDATE tours SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE tour_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $tourId]);
    }

    /**
     * TẠO TOUR MỚI - CẬP NHẬT LOẠI BỎ ĐỘ KHÓ
     */
    public function createTour($tourData)
    {
        $sql = "INSERT INTO tours 
                (tour_code, tour_name, description, route, destination, duration_days, 
                 price_adult, price_child, max_participants, status, featured_image, tags, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $tourData['tour_code'],
            $tourData['tour_name'],
            $tourData['description'],
            $tourData['route'],
            $tourData['destination'],
            $tourData['duration_days'],
            $tourData['price_adult'],
            $tourData['price_child'],
            $tourData['max_participants'],
            $tourData['status'],
            $tourData['featured_image'],
            $tourData['tags'],
            $_SESSION['admin_id'] ?? 1 // Fallback nếu không có session
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * LẤY TOUR THEO ID
     */
    public function getTourById($tourId)
    {
        $sql = "SELECT * FROM tours WHERE tour_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tourId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * THÊM LỊCH TRÌNH CHO TOUR
     */
    public function addItinerary($tourId, $dayData)
    {
        // Kiểm tra nếu dữ liệu ngày không rỗng
        if (empty($dayData['title']) && empty($dayData['description'])) {
            return false; // Bỏ qua ngày trống
        }

        $sql = "INSERT INTO tour_itineraries 
                (tour_id, day_number, title, description, activities, accommodation, meals, guide_notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $tourId,
            $dayData['day_number'],
            $dayData['title'] ?? '',
            $dayData['description'] ?? '',
            $dayData['activities'] ?? '',
            $dayData['accommodation'] ?? '',
            $dayData['meals'] ?? '',
            $dayData['guide_notes'] ?? ''
        ]);
    }

    /**
     * LẤY LỊCH TRÌNH THEO TOUR ID
     */
    public function getItinerariesByTour($tourId)
    {
        $sql = "SELECT * FROM tour_itineraries 
                WHERE tour_id = ? 
                ORDER BY day_number ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tourId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * XÓA LỊCH TRÌNH CŨ KHI UPDATE
     */
    public function deleteItineraries($tourId)
    {
        $sql = "DELETE FROM tour_itineraries WHERE tour_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$tourId]);
    }

    /**
     * CẬP NHẬT TOUR - LOẠI BỎ ĐỘ KHÓ
     */
    public function updateTour($tourId, $tourData)
    {
        $sql = "UPDATE tours SET 
                tour_code = ?, 
                tour_name = ?, 
                description = ?, 
                route = ?, 
                destination = ?, 
                duration_days = ?, 
                price_adult = ?, 
                price_child = ?, 
                max_participants = ?, 
                status = ?, 
                featured_image = ?, 
                tags = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE tour_id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $tourData['tour_code'],
            $tourData['tour_name'],
            $tourData['description'],
            $tourData['route'],
            $tourData['destination'],
            $tourData['duration_days'],
            $tourData['price_adult'],
            $tourData['price_child'],
            $tourData['max_participants'],
            $tourData['status'],
            $tourData['featured_image'],
            $tourData['tags'],
            $tourId
        ]);
    }

    /**
     * KIỂM TRA MÃ TOUR ĐÃ TỒN TẠI CHƯA
     */
    public function isTourCodeExists($tourCode, $excludeTourId = null)
    {
        $sql = "SELECT COUNT(*) FROM tours WHERE tour_code = ?";
        $params = [$tourCode];

        if ($excludeTourId) {
            $sql .= " AND tour_id != ?";
            $params[] = $excludeTourId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * LẤY DANH SÁCH TOUR (CÓ PHÂN TRANG)
     */
    /**
     * LẤY DANH SÁCH TOUR (CÓ PHÂN TRANG) - CÁCH ĐƠN GIẢN NHẤT
     */
    public function getAllTours($page = 1, $limit = 10, $search = '')
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM tours WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (tour_name LIKE ? OR tour_code LIKE ? OR destination LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        // ĐƠN GIẢN: Nối trực tiếp LIMIT và OFFSET vào SQL
        $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ĐẾM TỔNG SỐ TOUR (CHO PHÂN TRANG)
     */
    public function countTours($search = '')
    {
        $sql = "SELECT COUNT(*) FROM tours WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (tour_name LIKE ? OR tour_code LIKE ? OR destination LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
}
