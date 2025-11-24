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

    // ==================== ITINERARY METHODS ====================

    public function getTourItineraries($tour_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tour_itineraries WHERE tour_id = ? ORDER BY day_number ASC");
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    public function addItinerary($data) {
        $sql = "INSERT INTO tour_itineraries (tour_id, day_number, title, description, activities, accommodation, meals, guide_notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_id'],
            $data['day_number'],
            $data['title'],
            $data['description'],
            $data['activities'],
            $data['accommodation'],
            $data['meals'],
            $data['guide_notes']
        ]);
    }

    public function deleteItinerary($itinerary_id) {
        $stmt = $this->conn->prepare("DELETE FROM tour_itineraries WHERE itinerary_id = ?");
        return $stmt->execute([$itinerary_id]);
    }

    // ==================== IMAGE METHODS ====================

    public function getTourImages($tour_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tour_images WHERE tour_id = ? ORDER BY display_order ASC, image_id ASC");
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    public function addImage($data) {
        $sql = "INSERT INTO tour_images (tour_id, image_url, caption, is_primary, display_order) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_id'],
            $data['image_url'],
            $data['caption'],
            $data['is_primary'],
            $data['display_order']
        ]);
    }

    public function getImageById($image_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tour_images WHERE image_id = ?");
        $stmt->execute([$image_id]);
        return $stmt->fetch();
    }

    public function deleteImage($image_id) {
        $stmt = $this->conn->prepare("DELETE FROM tour_images WHERE image_id = ?");
        return $stmt->execute([$image_id]);
    }

    public function setPrimaryImage($tour_id, $image_id) {
        // Reset all images to non-primary
        $reset_stmt = $this->conn->prepare("UPDATE tour_images SET is_primary = 0 WHERE tour_id = ?");
        $reset_stmt->execute([$tour_id]);
        
        // Set the selected image as primary
        $set_stmt = $this->conn->prepare("UPDATE tour_images SET is_primary = 1 WHERE image_id = ? AND tour_id = ?");
        return $set_stmt->execute([$image_id, $tour_id]);
    }

    // ==================== POLICY METHODS ====================

    public function getTourPolicies($tour_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tour_policies WHERE tour_id = ? ORDER BY policy_type, policy_id");
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    public function addPolicy($data) {
        $sql = "INSERT INTO tour_policies (tour_id, policy_type, title, content) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tour_id'],
            $data['policy_type'],
            $data['title'],
            $data['content']
        ]);
    }

    public function deletePolicy($policy_id) {
        $stmt = $this->conn->prepare("DELETE FROM tour_policies WHERE policy_id = ?");
        return $stmt->execute([$policy_id]);
    }

    // ==================== TAG METHODS ====================

    public function getAllTags() {
        $stmt = $this->conn->prepare("SELECT * FROM tags ORDER BY tag_type, tag_name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTourTags($tour_id) {
        $sql = "SELECT t.* FROM tags t 
                JOIN tour_tags tt ON t.tag_id = tt.tag_id 
                WHERE tt.tour_id = ? 
                ORDER BY t.tag_type, t.tag_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$tour_id]);
        return $stmt->fetchAll();
    }

    public function addTag($data) {
        $sql = "INSERT INTO tags (tag_name, tag_type, description) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['tag_name'],
            $data['tag_type'],
            $data['description']
        ]);
    }

    public function assignTagToTour($tour_id, $tag_id) {
        $sql = "INSERT IGNORE INTO tour_tags (tour_id, tag_id) VALUES (?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$tour_id, $tag_id]);
    }

    public function removeTagFromTour($tour_id, $tag_id) {
        $stmt = $this->conn->prepare("DELETE FROM tour_tags WHERE tour_id = ? AND tag_id = ?");
        return $stmt->execute([$tour_id, $tag_id]);
    }

    // ==================== STATISTICS METHODS ====================

    public function getTourStatistics() {
        $stats = [];
        
        // Total tours
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM tours");
        $stmt->execute();
        $stats['total_tours'] = $stmt->fetch()['total'];
        
        // Published tours
        $stmt = $this->conn->prepare("SELECT COUNT(*) as published FROM tours WHERE status = 'active'");
        $stmt->execute();
        $stats['published_tours'] = $stmt->fetch()['published'];
        
        // Draft tours
        $stmt = $this->conn->prepare("SELECT COUNT(*) as draft FROM tours WHERE status = 'inactive'");
        $stmt->execute();
        $stats['draft_tours'] = $stmt->fetch()['draft'];
        
        return $stats;
    }
}
?>