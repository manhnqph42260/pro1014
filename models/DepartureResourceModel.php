<?php
class DepartureResourceModel {
    
    private $conn;
    private $table = "departure_resources";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả tài nguyên cho departure
    public function getResourcesByDeparture($departure_id) {
        $sql = "SELECT * FROM {$this->table} WHERE departure_id = ? ORDER BY schedule_date, schedule_time";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetchAll();
    }

    // Lấy tài nguyên theo type
    public function getResourcesByType($departure_id, $type) {
        $sql = "SELECT * FROM {$this->table} WHERE departure_id = ? AND resource_type = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id, $type]);
        return $stmt->fetchAll();
    }

    // Thêm tài nguyên mới
    public function createResource($data) {
        $sql = "INSERT INTO {$this->table} 
                (departure_id, resource_type, service_name, provider_name, 
                 quantity, unit, unit_price, total_price, schedule_date, schedule_time,
                 location, contact_person, contact_info, status, confirmation_number, resource_notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Tính total price
        $quantity = $data['quantity'] ?? 1;
        $unit_price = $data['unit_price'] ?? 0;
        $total_price = $quantity * $unit_price;
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['departure_id'],
            $data['resource_type'],
            $data['service_name'],
            $data['provider_name'] ?? '',
            $quantity,
            $data['unit'] ?? '',
            $unit_price,
            $total_price,
            $data['schedule_date'],
            $data['schedule_time'] ?? null,
            $data['location'] ?? '',
            $data['contact_person'] ?? '',
            $data['contact_info'] ?? '',
            $data['status'] ?? 'pending',
            $data['confirmation_number'] ?? '',
            $data['resource_notes'] ?? ''
        ]);
    }

    // Cập nhật tài nguyên
    public function updateResource($resource_id, $data) {
        $sql = "UPDATE {$this->table} SET 
                resource_type = ?,
                service_name = ?,
                provider_name = ?,
                quantity = ?,
                unit = ?,
                unit_price = ?,
                total_price = ?,
                schedule_date = ?,
                schedule_time = ?,
                location = ?,
                contact_person = ?,
                contact_info = ?,
                status = ?,
                confirmation_number = ?,
                resource_notes = ?
                WHERE resource_id = ?";
        
        // Tính total price
        $quantity = $data['quantity'] ?? 1;
        $unit_price = $data['unit_price'] ?? 0;
        $total_price = $quantity * $unit_price;
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['resource_type'],
            $data['service_name'],
            $data['provider_name'] ?? '',
            $quantity,
            $data['unit'] ?? '',
            $unit_price,
            $total_price,
            $data['schedule_date'],
            $data['schedule_time'] ?? null,
            $data['location'] ?? '',
            $data['contact_person'] ?? '',
            $data['contact_info'] ?? '',
            $data['status'] ?? 'pending',
            $data['confirmation_number'] ?? '',
            $data['resource_notes'] ?? '',
            $resource_id
        ]);
    }

    // Xóa tài nguyên
    public function deleteResource($resource_id) {
        $sql = "DELETE FROM {$this->table} WHERE resource_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$resource_id]);
    }

    // Lấy thống kê tài nguyên
    public function getResourceStats($departure_id) {
        $sql = "SELECT 
                    resource_type,
                    COUNT(*) as total,
                    SUM(total_price) as total_cost,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed
                FROM {$this->table} 
                WHERE departure_id = ?
                GROUP BY resource_type";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$departure_id]);
        return $stmt->fetchAll();
    }
}
?>