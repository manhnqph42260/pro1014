<?php
class DepartureAssignmentModel {
    
    private $conn;
    private $table = "departure_assignments";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả phân bổ nhân sự cho departure
    public function getAssignmentsByDeparture($departure_id) {
        $sql = "SELECT * FROM {$this->table} WHERE departure_id = ? ORDER BY assignment_type, assignment_date";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetchAll();
    }

    // Lấy phân bổ theo type
    public function getAssignmentsByType($departure_id, $type) {
        $sql = "SELECT * FROM {$this->table} WHERE departure_id = ? AND assignment_type = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id, $type]);
        return $stmt->fetchAll();
    }

    // Thêm phân bổ mới
    public function createAssignment($data) {
        $sql = "INSERT INTO {$this->table} 
                (departure_id, assignment_type, person_id, person_name, role, 
                 contact_info, status, assignment_date, assignment_notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['departure_id'],
            $data['assignment_type'],
            $data['person_id'] ?? null,
            $data['person_name'],
            $data['role'],
            $data['contact_info'] ?? '',
            $data['status'] ?? 'pending',
            $data['assignment_date'] ?? null,
            $data['assignment_notes'] ?? ''
        ]);
    }

    // Cập nhật phân bổ
    public function updateAssignment($assignment_id, $data) {
        $sql = "UPDATE {$this->table} SET 
                person_id = ?,
                person_name = ?,
                role = ?,
                contact_info = ?,
                status = ?,
                assignment_date = ?,
                assignment_notes = ?
                WHERE assignment_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['person_id'] ?? null,
            $data['person_name'],
            $data['role'],
            $data['contact_info'] ?? '',
            $data['status'] ?? 'pending',
            $data['assignment_date'] ?? null,
            $data['assignment_notes'] ?? '',
            $assignment_id
        ]);
    }

    // Xóa phân bổ
    public function deleteAssignment($assignment_id) {
        $sql = "DELETE FROM {$this->table} WHERE assignment_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$assignment_id]);
    }

    // Lấy thống kê nhân sự
    public function getAssignmentStats($departure_id) {
        $sql = "SELECT 
                    assignment_type,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                FROM {$this->table} 
                WHERE departure_id = ?
                GROUP BY assignment_type";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetchAll();
    }
}
?>