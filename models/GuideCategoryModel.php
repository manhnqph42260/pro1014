<?php
class GuideCategoryModel {
    
    private $conn;
    private $table = "guide_categories";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả categories
    public function getAllCategories($type = null, $active_only = true) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if ($active_only) {
            $sql .= " AND is_active = 1";
        }
        
        if (!empty($type)) {
            $sql .= " AND category_type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY category_type, category_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lấy category theo ID
    public function getCategoryById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Tạo category mới
    public function createCategory($data) {
        $sql = "INSERT INTO {$this->table} (category_name, category_type, description, color_code, icon, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['category_name'],
            $data['category_type'],
            $data['description'] ?? '',
            $data['color_code'] ?? '#6c757d',
            $data['icon'] ?? '',
            $data['is_active'] ?? 1
        ]);
    }

    // Cập nhật category
    public function updateCategory($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                category_name = ?,
                category_type = ?,
                description = ?,
                color_code = ?,
                icon = ?,
                is_active = ?
                WHERE category_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['category_name'],
            $data['category_type'],
            $data['description'] ?? '',
            $data['color_code'] ?? '#6c757d',
            $data['icon'] ?? '',
            $data['is_active'] ?? 1,
            $id
        ]);
    }

    // Xóa category
    public function deleteCategory($id) {
        // Kiểm tra xem có HDV nào đang sử dụng category này không
        $check_sql = "SELECT COUNT(*) as guide_count FROM guides WHERE category_id = ?";
        $check_stmt = $this->conn->prepare($check_sql);
        $check_stmt->execute([$id]);
        $result = $check_stmt->fetch();
        
        if ($result['guide_count'] > 0) {
            throw new Exception("Không thể xóa category đang có HDV sử dụng!");
        }
        
        $sql = "DELETE FROM {$this->table} WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Lấy categories theo type
    public function getCategoriesByType($type) {
        $sql = "SELECT * FROM {$this->table} WHERE category_type = ? AND is_active = 1 ORDER BY category_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    // Lấy HDV theo category
    public function getGuidesByCategory($category_id) {
        $sql = "SELECT g.* FROM guides g WHERE g.category_id = ? AND g.status = 'active' ORDER BY g.full_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$category_id]);
        return $stmt->fetchAll();
    }

    // Thống kê số HDV theo category
    public function getCategoryStats() {
        $sql = "SELECT 
                    c.category_id,
                    c.category_name,
                    c.category_type,
                    c.color_code,
                    COUNT(g.guide_id) as guide_count,
                    SUM(CASE WHEN g.status = 'active' THEN 1 ELSE 0 END) as active_guides
                FROM guide_categories c
                LEFT JOIN guides g ON c.category_id = g.category_id
                WHERE c.is_active = 1
                GROUP BY c.category_id
                ORDER BY c.category_type, c.category_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy tất cả category types
    public function getCategoryTypes() {
        return [
            'location' => 'Theo địa điểm',
            'specialization' => 'Theo chuyên môn',
            'client_type' => 'Theo loại khách'
        ];
    }
}
?>