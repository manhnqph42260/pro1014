<?php
/**
 * BaseController - Controller cơ sở cho tất cả controllers
 * Cung cấp các method chung: auth check, render view, redirect, json response
 */
class BaseController {
    
    /**
     * Kiểm tra admin đã đăng nhập chưa
     */
    protected function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ?act=admin_login");
            exit();
        }
    }
    
    /**
     * Render view với data
     */
    protected function renderView($viewPath, $data = []) {
        // Extract data thành biến
        extract($data);
        
        // Kiểm tra file view tồn tại
        if (!file_exists($viewPath)) {
            throw new Exception("View file not found: " . $viewPath);
        }
        
        require_once $viewPath;
    }
    
    /**
     * Trả về JSON response
     */
    protected function jsonResponse($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit();
    }
    
    /**
     * Redirect đến URL
     */
    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
    
    /**
     * Set flash message
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Get flash message
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
    
    /**
     * Validate required fields
     */
    protected function validateRequired($fields, $data) {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field '$field' is required";
            }
        }
        return $errors;
    }
    
    /**
     * Upload file
     */
    protected function uploadFile($file, $uploadDir, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload error: " . $file['error']);
        }
        
        // Check file type
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid file type: " . $file['type']);
        }
        
        // Create upload directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Failed to move uploaded file");
        }
        
        return $filename;
    }
}
?>