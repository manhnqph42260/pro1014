<?php
class ChecklistModel {
    
    private $conn;
    private $table = "checklist_items";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả checklist items cho departure
    public function getChecklistByDeparture($departure_id) {
        $sql = "SELECT * FROM {$this->table} WHERE departure_id = ? ORDER BY category, deadline";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetchAll();
    }

    // Lấy checklist theo category
    public function getChecklistByCategory($departure_id, $category) {
        $sql = "SELECT * FROM {$this->table} WHERE departure_id = ? AND category = ? ORDER BY deadline";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id, $category]);
        return $stmt->fetchAll();
    }

    // Thêm checklist item mới
    public function createChecklistItem($data) {
        $sql = "INSERT INTO {$this->table} 
                (departure_id, category, item_name, assigned_to, deadline, 
                 status, completion_notes, completed_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['departure_id'],
            $data['category'],
            $data['item_name'],
            $data['assigned_to'] ?? '',
            $data['deadline'] ?? null,
            $data['status'] ?? 'pending',
            $data['completion_notes'] ?? '',
            $data['completed_at'] ?? null
        ]);
    }

    // Cập nhật checklist item
    public function updateChecklistItem($item_id, $data) {
        $sql = "UPDATE {$this->table} SET 
                item_name = ?,
                category = ?,
                assigned_to = ?,
                deadline = ?,
                status = ?,
                completion_notes = ?,
                completed_at = ?
                WHERE item_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['item_name'],
            $data['category'],
            $data['assigned_to'] ?? '',
            $data['deadline'] ?? null,
            $data['status'] ?? 'pending',
            $data['completion_notes'] ?? '',
            $data['completed_at'] ?? null,
            $item_id
        ]);
    }

    // Xóa checklist item
    public function deleteChecklistItem($item_id) {
        $sql = "DELETE FROM {$this->table} WHERE item_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$item_id]);
    }

    // Cập nhật trạng thái checklist item
    public function updateChecklistStatus($item_id, $status, $notes = '', $completed_at = null) {
        $sql = "UPDATE {$this->table} SET 
                status = ?,
                completion_notes = ?,
                completed_at = ?
                WHERE item_id = ?";
        
        if ($status === 'completed' && !$completed_at) {
            $completed_at = date('Y-m-d H:i:s');
        }
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $status,
            $notes,
            $completed_at,
            $item_id
        ]);
    }

    // Lấy thống kê checklist
    public function getChecklistStats($departure_id) {
        $sql = "SELECT 
                    category,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                FROM {$this->table} 
                WHERE departure_id = ?
                GROUP BY category";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetchAll();
    }

    // Lấy các item sắp đến hạn
    public function getUpcomingDeadlines($departure_id, $days = 3) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE departure_id = ? 
                AND deadline BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? DAY)
                AND status IN ('pending', 'in_progress')
                ORDER BY deadline";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id, $days]);
        return $stmt->fetchAll();
    }
}
?>