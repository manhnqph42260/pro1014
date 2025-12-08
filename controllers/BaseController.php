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
            $this->setFlash('error', 'Vui lòng đăng nhập admin để tiếp tục');
            $this->redirect('?act=admin_login');
        }
    }
    
    /**
     * Kiểm tra HDV đã đăng nhập chưa
     */
    protected function checkGuideAuth() {
        if (!isset($_SESSION['guide_id'])) {
            $this->setFlash('error', 'Vui lòng đăng nhập HDV để tiếp tục');
            $this->redirect('?act=guide_login');
        }
    }
    
    /**
     * Kiểm tra xem user có role cụ thể không
     */
    protected function checkRole($requiredRole) {
        $userRole = $_SESSION['role'] ?? $_SESSION['guide_role'] ?? null;
        
        if (!$userRole) {
            $this->setFlash('error', 'Không có quyền truy cập');
            $this->redirect('?act=admin_login');
        }
        
        // Nếu yêu cầu admin nhưng user là guide
        if ($requiredRole === 'admin' && $userRole === 'guide') {
            $this->setFlash('error', 'Không có quyền truy cập');
            $this->redirect('?act=guide_dashboard');
        }
        
        // Nếu yêu cầu guide nhưng user là admin
        if ($requiredRole === 'guide' && $userRole === 'admin') {
            $this->setFlash('error', 'Vui lòng đăng nhập với tài khoản HDV');
            $this->redirect('?act=guide_login');
        }
        
        return true;
    }
    
    /**
     * Get current user info
     */
    protected function getCurrentUser() {
        if (isset($_SESSION['admin_id'])) {
            return [
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['username'] ?? null,
                'full_name' => $_SESSION['full_name'] ?? null,
                'email' => $_SESSION['email'] ?? null,
                'phone' => $_SESSION['phone'] ?? null,
                'role' => 'admin',
                'is_admin' => true,
                'is_guide' => false
            ];
        } elseif (isset($_SESSION['guide_id'])) {
            return [
                'id' => $_SESSION['guide_id'],
                'code' => $_SESSION['guide_code'] ?? null,
                'name' => $_SESSION['guide_name'] ?? null,
                'email' => $_SESSION['guide_email'] ?? null,
                'phone' => $_SESSION['guide_phone'] ?? null,
                'languages' => $_SESSION['guide_languages'] ?? [],
                'skills' => $_SESSION['guide_skills'] ?? [],
                'role' => 'guide',
                'is_admin' => false,
                'is_guide' => true
            ];
        }
        
        return null;
    }
    
    /**
     * Render view với data
     */
    protected function renderView($viewPath, $data = []) {
        // Extract data thành biến
        extract($data);
        
        // Add flash message to view if exists
        $flashMessages = $this->getFlashMessages();
        if (!empty($flashMessages)) {
            $data['flash_messages'] = $flashMessages;
        }
        
        // Add current user info to all views
        $data['currentUser'] = $this->getCurrentUser();
        
        // Kiểm tra file view tồn tại
        if (!file_exists($viewPath)) {
            throw new Exception("View file not found: " . $viewPath);
        }
        
        // Start output buffering
        ob_start();
        
        // Include view file
        require_once $viewPath;
        
        // Get buffered content
        $content = ob_get_clean();
        
        // Xác định layout dựa trên role
        $layoutPath = $this->getLayoutPath();
        
        // Nếu có layout, sử dụng layout
        if ($layoutPath && file_exists($layoutPath)) {
            // Truyền content vào layout
            $data['content'] = $content;
            extract($data);
            require_once $layoutPath;
        } else {
            // Nếu không có layout, xuất nội dung trực tiếp
            echo $content;
        }
    }
    
    /**
     * Xác định layout path dựa trên role
     */
    protected function getLayoutPath() {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            // Không đăng nhập - dùng layout public hoặc không dùng layout
            return './views/layouts/public.php';
        }
        
        if ($user['role'] === 'guide') {
            // HDV - dùng layout guide
            $guideLayout = './views/guide/layout.php';
            if (file_exists($guideLayout)) {
                return $guideLayout;
            }
            // Fallback nếu không có layout guide
            return './views/layouts/guide.php';
        }
        
        if ($user['role'] === 'admin') {
            // Admin - dùng layout admin
            $adminLayout = './views/admin/layout.php';
            if (file_exists($adminLayout)) {
                return $adminLayout;
            }
            // Fallback nếu không có layout admin
            return './views/layouts/admin.php';
        }
        
        // Default layout
        return './views/layouts/main.php';
    }
    
    /**
     * Render partial view (không có layout)
     */
    protected function renderPartial($viewPath, $data = []) {
        // Add flash message to view if exists
        $flashMessages = $this->getFlashMessages();
        if (!empty($flashMessages)) {
            $data['flash_messages'] = $flashMessages;
        }
        
        // Add current user info
        $data['currentUser'] = $this->getCurrentUser();
        
        // Kiểm tra file view tồn tại
        if (!file_exists($viewPath)) {
            throw new Exception("View file not found: " . $viewPath);
        }
        
        // Extract data
        extract($data);
        
        // Include view file
        require_once $viewPath;
    }
    
    /**
     * Trả về JSON response
     */
    protected function jsonResponse($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    /**
     * Trả về JSON error response
     */
    protected function jsonError($message, $status = 400, $errors = []) {
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        
        $this->jsonResponse($response, $status);
    }
    
    /**
     * Trả về JSON success response
     */
    protected function jsonSuccess($data = null, $message = 'Success') {
        $response = [
            'success' => true,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $this->jsonResponse($response, 200);
    }
    
    /**
     * Redirect đến URL
     */
    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
    
    /**
     * Redirect với flash message
     */
    protected function redirectWithFlash($url, $type, $message) {
        $this->setFlash($type, $message);
        $this->redirect($url);
    }
    
    /**
     * Redirect back (trở lại trang trước)
     */
    protected function redirectBack() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '?act=admin_dashboard';
        $this->redirect($referer);
    }
    
    /**
     * Set flash message
     */
    protected function setFlash($type, $message) {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        
        $_SESSION['flash_messages'][] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Get all flash messages
     */
    protected function getFlashMessages() {
        if (isset($_SESSION['flash_messages'])) {
            $messages = $_SESSION['flash_messages'];
            unset($_SESSION['flash_messages']);
            return $messages;
        }
        return [];
    }
    
    /**
     * Get first flash message (for backward compatibility)
     */
    protected function getFlash() {
        $messages = $this->getFlashMessages();
        return !empty($messages) ? $messages[0] : null;
    }
    
    /**
     * Validate required fields
     */
    protected function validateRequired($fields, $data) {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = "Trường '$field' là bắt buộc";
            }
        }
        return $errors;
    }
    
    /**
     * Validate email
     */
    protected function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Email không hợp lệ";
        }
        return null;
    }
    
    /**
     * Validate phone number (Vietnamese format)
     */
    protected function validatePhone($phone) {
        // Vietnamese phone number regex
        $pattern = '/^(0|\+84)(3[2-9]|5[6|8|9]|7[0|6-9]|8[1-9]|9[0-9])[0-9]{7}$/';
        if (!preg_match($pattern, $phone)) {
            return "Số điện thoại không hợp lệ";
        }
        return null;
    }
    
    /**
     * Validate date format
     */
    protected function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        if (!$d || $d->format($format) !== $date) {
            return "Ngày không hợp lệ. Định dạng đúng: $format";
        }
        return null;
    }
    
    /**
     * Upload file
     */
    protected function uploadFile($file, $uploadDir, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File vượt quá kích thước cho phép',
                UPLOAD_ERR_FORM_SIZE => 'File vượt quá kích thước form',
                UPLOAD_ERR_PARTIAL => 'File chỉ được tải lên một phần',
                UPLOAD_ERR_NO_FILE => 'Không có file được tải lên',
                UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm',
                UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file',
                UPLOAD_ERR_EXTENSION => 'File bị dừng bởi extension'
            ];
            
            throw new Exception($errorMessages[$file['error']] ?? "Upload error: " . $file['error']);
        }
        
        // Check file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            throw new Exception("File quá lớn. Kích thước tối đa là 5MB");
        }
        
        // Check file type by extension and MIME type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Loại file không được hỗ trợ. Chỉ chấp nhận: " . implode(', ', $allowedExtensions));
        }
        
        // Verify MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowedTypes)) {
            throw new Exception("Loại MIME không hợp lệ");
        }
        
        // Create upload directory if not exists
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception("Không thể tạo thư mục upload");
            }
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = rtrim($uploadDir, '/') . '/' . $filename;
        
        // Sanitize filename
        $filename = preg_replace('/[^a-zA-Z0-9\._-]/', '', $filename);
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Không thể di chuyển file đã tải lên");
        }
        
        return [
            'filename' => $filename,
            'path' => $filepath,
            'original_name' => $file['name'],
            'size' => $file['size'],
            'mime_type' => $mime,
            'extension' => $extension,
            'url' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $filepath)
        ];
    }
    
    /**
     * Delete uploaded file
     */
    protected function deleteFile($filepath) {
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken() {
        if (!isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_tokens'][$token] = time();
        
        // Clean old tokens (older than 1 hour)
        foreach ($_SESSION['csrf_tokens'] as $storedToken => $timestamp) {
            if (time() - $timestamp > 3600) {
                unset($_SESSION['csrf_tokens'][$storedToken]);
            }
        }
        
        return $token;
    }
    
    /**
     * Verify CSRF token
     */
    protected function verifyCsrfToken($token) {
        if (!isset($_SESSION['csrf_tokens'][$token])) {
            return false;
        }
        
        $timestamp = $_SESSION['csrf_tokens'][$token];
        
        // Token is valid for 1 hour
        if (time() - $timestamp > 3600) {
            unset($_SESSION['csrf_tokens'][$token]);
            return false;
        }
        
        // Remove used token
        unset($_SESSION['csrf_tokens'][$token]);
        return true;
    }
    
    /**
     * Add CSRF token to form
     */
    protected function csrfField() {
        $token = $this->generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
    
    /**
     * Format date for display
     */
    protected function formatDate($date, $format = 'd/m/Y') {
        if (!$date || $date == '0000-00-00') return '';
        
        try {
            $dateObj = new DateTime($date);
            return $dateObj->format($format);
        } catch (Exception $e) {
            return $date;
        }
    }
    
    /**
     * Format datetime for display
     */
    protected function formatDateTime($datetime, $format = 'd/m/Y H:i') {
        if (!$datetime || $datetime == '0000-00-00 00:00:00') return '';
        
        try {
            $dateObj = new DateTime($datetime);
            return $dateObj->format($format);
        } catch (Exception $e) {
            return $datetime;
        }
    }
    
    /**
     * Format currency (VND)
     */
    protected function formatCurrency($amount) {
        if (!is_numeric($amount)) return '0 ₫';
        return number_format($amount, 0, ',', '.') . ' ₫';
    }
    
    /**
     * Format number
     */
    protected function formatNumber($number, $decimals = 0) {
        if (!is_numeric($number)) return '0';
        return number_format($number, $decimals, ',', '.');
    }
    
    /**
     * Sanitize input data
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
            return $data;
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    /**
     * Escape output for HTML
     */
    protected function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Get pagination data
     */
    protected function getPagination($page, $perPage, $totalItems) {
        $page = (int)$page;
        $perPage = (int)$perPage;
        $totalItems = (int)$totalItems;
        
        if ($page < 1) $page = 1;
        if ($perPage < 1) $perPage = 10;
        
        $totalPages = ceil($totalItems / $perPage);
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;
        
        return [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'offset' => $offset,
            'has_previous' => $page > 1,
            'has_next' => $page < $totalPages,
            'previous_page' => max(1, $page - 1),
            'next_page' => min($totalPages, $page + 1),
            'start_item' => $offset + 1,
            'end_item' => min($offset + $perPage, $totalItems)
        ];
    }
    
    /**
     * Generate pagination HTML
     */
    protected function paginationHtml($pagination, $urlPattern = '?page={page}') {
        if ($pagination['total_pages'] <= 1) return '';
        
        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        
        // Previous button
        if ($pagination['has_previous']) {
            $prevUrl = str_replace('{page}', $pagination['previous_page'], $urlPattern);
            $html .= '<li class="page-item"><a class="page-link" href="' . $prevUrl . '">&laquo; Trước</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&laquo; Trước</span></li>';
        }
        
        // Page numbers
        $startPage = max(1, $pagination['current_page'] - 2);
        $endPage = min($pagination['total_pages'], $pagination['current_page'] + 2);
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $pagination['current_page']) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $pageUrl = str_replace('{page}', $i, $urlPattern);
                $html .= '<li class="page-item"><a class="page-link" href="' . $pageUrl . '">' . $i . '</a></li>';
            }
        }
        
        // Next button
        if ($pagination['has_next']) {
            $nextUrl = str_replace('{page}', $pagination['next_page'], $urlPattern);
            $html .= '<li class="page-item"><a class="page-link" href="' . $nextUrl . '">Sau &raquo;</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Sau &raquo;</span></li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
    
    /**
     * Log activity
     */
    protected function logActivity($action, $details = null, $userId = null, $userType = null) {
        try {
            require_once './commons/env.php';
            $conn = connectDB();
            
            if (!$userId) {
                $user = $this->getCurrentUser();
                if ($user) {
                    $userId = $user['id'];
                    $userType = $user['role'];
                }
            }
            
            $query = "INSERT INTO activity_logs (user_id, user_type, action, details, ip_address, user_agent) 
                     VALUES (:user_id, :user_type, :action, :details, :ip_address, :user_agent)";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':user_type' => $userType,
                ':action' => $action,
                ':details' => $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
            
            return true;
        } catch (Exception $e) {
            // Log to file if database logging fails
            $logMessage = date('Y-m-d H:i:s') . " - Activity log failed: " . $e->getMessage() . PHP_EOL;
            file_put_contents('./logs/activity.log', $logMessage, FILE_APPEND);
            return false;
        }
    }
    
    /**
     * Send email
     */
    protected function sendEmail($to, $subject, $body, $isHtml = true, $attachments = []) {
        try {
            // For now, log email instead of actually sending
            // In production, use PHPMailer or similar library
            $logData = [
                'to' => $to,
                'subject' => $subject,
                'body' => $body,
                'is_html' => $isHtml,
                'timestamp' => date('Y-m-d H:i:s'),
                'attachments' => $attachments
            ];
            
            // Ensure logs directory exists
            if (!is_dir('./logs')) {
                mkdir('./logs', 0777, true);
            }
            
            file_put_contents('./logs/emails.log', json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);
            
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate random string
     */
    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $randomString;
    }
    
    /**
     * Generate password hash
     */
    protected function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify password
     */
    protected function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Get client IP address
     */
    protected function getClientIp() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
    /**
     * Get current URL
     */
    protected function getCurrentUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Check if request is AJAX
     */
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * Check if request is POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Check if request is GET
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Get POST data
     */
    protected function getPostData() {
        return $_POST;
    }
    
    /**
     * Get GET data
     */
    protected function getGetData() {
        return $_GET;
    }
    
    /**
     * Get request data (POST or GET)
     */
    protected function getRequestData() {
        if ($this->isPost()) {
            return $this->getPostData();
        }
        return $this->getGetData();
    }
    
    /**
     * Get uploaded files
     */
    protected function getFiles() {
        return $_FILES;
    }
    
    /**
     * Set response header
     */
    protected function setHeader($name, $value) {
        header("$name: $value");
    }
    
    /**
     * Set response status code
     */
    protected function setStatusCode($code) {
        http_response_code($code);
    }
    
    /**
     * Load model
     */
    protected function loadModel($modelName) {
        $modelFile = './models/' . $modelName . '.php';
        
        if (!file_exists($modelFile)) {
            throw new Exception("Model file not found: " . $modelFile);
        }
        
        require_once $modelFile;
        
        $modelClass = ucfirst($modelName) . 'Model';
        if (!class_exists($modelClass)) {
            throw new Exception("Model class not found: " . $modelClass);
        }
        
        return new $modelClass();
    }
    
    /**
     * Load helper
     */
    protected function loadHelper($helperName) {
        $helperFile = './helpers/' . $helperName . '.php';
        
        if (!file_exists($helperFile)) {
            throw new Exception("Helper file not found: " . $helperFile);
        }
        
        require_once $helperFile;
        
        return true;
    }
    
    /**
     * Load library
     */
    protected function loadLibrary($libraryName) {
        $libraryFile = './libraries/' . $libraryName . '.php';
        
        if (!file_exists($libraryFile)) {
            throw new Exception("Library file not found: " . $libraryFile);
        }
        
        require_once $libraryFile;
        
        return true;
    }
    
    /**
     * Debug data
     */
    protected function debug($data, $exit = false) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        
        if ($exit) {
            exit();
        }
    }
    
    /**
     * Log error
     */
    protected function logError($message, $data = null) {
        $logMessage = date('Y-m-d H:i:s') . " - ERROR: " . $message;
        
        if ($data) {
            $logMessage .= " - Data: " . json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        
        $logMessage .= PHP_EOL;
        
        // Ensure logs directory exists
        if (!is_dir('./logs')) {
            mkdir('./logs', 0777, true);
        }
        
        file_put_contents('./logs/error.log', $logMessage, FILE_APPEND);
    }
}
?>
