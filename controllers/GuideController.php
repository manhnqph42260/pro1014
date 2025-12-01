<?php
require_once 'BaseController.php';

class GuideController extends BaseController {
    
    // Danh sách HDV
    public function adminList() {
        $this->checkAdminAuth();
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $conn = connectDB();
        
        // Xử lý search và filter
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $query = "SELECT * FROM guides WHERE 1=1";
        $params = [];
        
        if ($search) {
            $query .= " AND (full_name LIKE :search OR guide_code LIKE :search OR email LIKE :search OR phone LIKE :search)";
            $params['search'] = "%$search%";
        }
        
        if ($status) {
            $query .= " AND status = :status";
            $params['status'] = $status;
        }
        
        $query .= " ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $guides = $stmt->fetchAll();
        
        $this->renderView('./views/admin/guides/list.php', [
            'guides' => $guides,
            'search' => $search,
            'status' => $status
        ]);
    }
    
    // Tạo HDV mới - ĐÃ FIX
    public function adminCreate() {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreateGuide();
        } else {
            $this->renderView('./views/admin/guides/create.php');
        }
    }
    
    // Sửa HDV
    public function adminEdit($id = null) {
        $this->checkAdminAuth();
        $guide_id = $id ?? $_GET['id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUpdateGuide($guide_id);
        }
        
        // Lấy thông tin HDV
        require_once './commons/env.php';
        require_once './commons/function.php';
        $conn = connectDB();
        
        $stmt = $conn->prepare("SELECT * FROM guides WHERE guide_id = :id");
        $stmt->execute(['id' => $guide_id]);
        $guide = $stmt->fetch();
        
        if (!$guide) {
            $this->setFlash('error', 'HDV không tồn tại');
            $this->redirect('?act=admin_guides');
        }
        
        // Chuyển đổi JSON fields thành array
        if (!empty($guide['languages'])) {
            $guide['languages'] = json_decode($guide['languages'], true);
        }
        if (!empty($guide['skills'])) {
            $guide['skills'] = json_decode($guide['skills'], true);
        }
        if (!empty($guide['certifications'])) {
            $guide['certifications'] = json_decode($guide['certifications'], true);
        }
        
        $this->renderView('./views/admin/guides/edit.php', ['guide' => $guide]);
    }
    
    // Xóa HDV
    public function adminDelete() {
        $this->checkAdminAuth();
        $guide_id = $_GET['id'];
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $conn = connectDB();
        
        try {
            // Kiểm tra xem HDV có đang được phân công không
            $check_stmt = $conn->prepare("SELECT COUNT(*) as assignment_count FROM guide_assignments WHERE guide_id = :id AND status != 'completed'");
            $check_stmt->execute(['id' => $guide_id]);
            $result = $check_stmt->fetch();
            
            if ($result['assignment_count'] > 0) {
                $this->setFlash('error', 'Không thể xóa HDV đang có phân công tour chưa hoàn thành');
                $this->redirect('?act=admin_guides');
            }
            
            $stmt = $conn->prepare("DELETE FROM guides WHERE guide_id = :id");
            $stmt->execute(['id' => $guide_id]);
            
            $this->setFlash('success', 'Xóa HDV thành công');
        } catch (Exception $e) {
            $this->setFlash('error', 'Lỗi khi xóa HDV: ' . $e->getMessage());
        }
        
        $this->redirect('?act=admin_guides');
    }
    
    // Chi tiết HDV
    public function adminView() {
        $this->checkAdminAuth();
        $guide_id = $_GET['id'];
        
        require_once './commons/env.php';
        require_once './commons/function.php';
        $conn = connectDB();
        
        // Lấy thông tin HDV
        $stmt = $conn->prepare("SELECT * FROM guides WHERE guide_id = :id");
        $stmt->execute(['id' => $guide_id]);
        $guide = $stmt->fetch();
        
        if (!$guide) {
            $this->setFlash('error', 'HDV không tồn tại');
            $this->redirect('?act=admin_guides');
        }
        
        // Chuyển đổi JSON fields thành array
        if (!empty($guide['languages'])) {
            $guide['languages'] = json_decode($guide['languages'], true);
        }
        if (!empty($guide['skills'])) {
            $guide['skills'] = json_decode($guide['skills'], true);
        }
        if (!empty($guide['certifications'])) {
            $guide['certifications'] = json_decode($guide['certifications'], true);
        }
        
        // Lấy lịch sử phân công
        $assignments_stmt = $conn->prepare("
            SELECT ga.*, t.tour_name, d.departure_date, d.status as departure_status
            FROM guide_assignments ga
            JOIN departure_schedules d ON ga.departure_id = d.departure_id
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE ga.guide_id = :guide_id
            ORDER BY d.departure_date DESC
        ");
        $assignments_stmt->execute(['guide_id' => $guide_id]);
        $assignments = $assignments_stmt->fetchAll();
        
        // Lấy báo cáo sự cố
        $incidents_stmt = $conn->prepare("
            SELECT ir.*, t.tour_name, d.departure_date
            FROM incident_reports ir
            JOIN departure_schedules d ON ir.departure_id = d.departure_id
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE ir.guide_id = :guide_id
            ORDER BY ir.incident_date DESC
        ");
        $incidents_stmt->execute(['guide_id' => $guide_id]);
        $incidents = $incidents_stmt->fetchAll();
        
        // Lấy nhật ký tour
        $journals_stmt = $conn->prepare("
            SELECT gj.*, t.tour_name, d.departure_date
            FROM guide_journals gj
            JOIN departure_schedules d ON gj.departure_id = d.departure_id
            JOIN tours t ON d.tour_id = t.tour_id
            WHERE gj.guide_id = :guide_id
            ORDER BY gj.journal_date DESC
        ");
        $journals_stmt->execute(['guide_id' => $guide_id]);
        $journals = $journals_stmt->fetchAll();
        
        $this->renderView('./views/admin/guides/view.php', [
            'guide' => $guide,
            'assignments' => $assignments,
            'incidents' => $incidents,
            'journals' => $journals
        ]);
    }
    
    // ========== CÁC PHƯƠNG THỨC XỬ LÝ ==========
    
    private function handleCreateGuide() {
    require_once './commons/env.php';
    require_once './commons/function.php';
    
    $conn = connectDB();
    
    try {
        $conn->beginTransaction();
        
        // Validation
        $errors = [];
        if (empty($_POST['full_name'])) {
            $errors[] = "Họ tên là bắt buộc";
        }
        
        // Validate id_number length (max 20 ký tự)
        if (!empty($_POST['id_number']) && strlen($_POST['id_number']) > 20) {
            $errors[] = "Số CMND/CCCD không được quá 20 ký tự";
        }
        
        if (!empty($errors)) {
            $this->setFlash('error', implode("<br>", $errors));
            $this->renderView('./views/admin/guides/create.php', ['form_data' => $_POST]);
            return;
        }
        
        // Generate guide code if not provided
        $guide_code = $_POST['guide_code'] ?? 'HDV' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Prepare JSON data - FIX: Kiểm tra và encode đúng cách
        $languages = [];
        if (isset($_POST['languages']) && is_array($_POST['languages'])) {
            $languages = array_filter($_POST['languages']); // Loại bỏ giá trị rỗng
        }
        // Thêm ngôn ngữ khác nếu có
        if (!empty($_POST['other_languages'])) {
            $other_langs = array_map('trim', explode(',', $_POST['other_languages']));
            $languages = array_merge($languages, $other_langs);
        }
        $languages_json = !empty($languages) ? json_encode(array_unique($languages)) : '[]';
        
        $skills = [];
        if (isset($_POST['skills']) && is_array($_POST['skills'])) {
            $skills = array_filter($_POST['skills']); // Loại bỏ giá trị rỗng
        }
        $skills_json = !empty($skills) ? json_encode(array_unique($skills)) : '[]';
        
        // Process certifications
        $certifications = [];
        if (!empty($_POST['certifications_text'])) {
            $cert_lines = explode("\n", $_POST['certifications_text']);
            foreach ($cert_lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $certifications[] = $line;
                }
            }
        }
        $certifications_json = !empty($certifications) ? json_encode($certifications) : '[]';
        
        // INSERT GUIDE với id_number giới hạn độ dài
        $query = "INSERT INTO guides (
            guide_code, full_name, email, phone, id_number, date_of_birth, address, 
            emergency_contact, languages, skills, certifications, experience_years, 
            status, rating, avatar_url
        ) VALUES (
            :code, :name, :email, :phone, :id_number, :dob, :address, 
            :emergency_contact, :languages, :skills, :certifications, :experience, 
            :status, :rating, :avatar_url
        )";
        
        $stmt = $conn->prepare($query);
        
        // Upload avatar if exists
        $avatar_url = null;
        if (!empty($_FILES['avatar']['name'])) {
            $avatar_url = $this->uploadAvatar();
        }
        
        // Giới hạn id_number tối đa 20 ký tự
        $id_number = !empty($_POST['id_number']) ? substr($_POST['id_number'], 0, 20) : '';
        
        $result = $stmt->execute([
            'code' => $guide_code,
            'name' => $_POST['full_name'],
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'id_number' => $id_number, // Đã giới hạn độ dài
            'dob' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
            'address' => $_POST['address'] ?? '',
            'emergency_contact' => $_POST['emergency_contact'] ?? '',
            'languages' => $languages_json,
            'skills' => $skills_json,
            'certifications' => $certifications_json,
            'experience' => $_POST['experience_years'] ?? 0,
            'status' => $_POST['status'] ?? 'active',
            'rating' => $_POST['rating'] ?? 0,
            'avatar_url' => $avatar_url
        ]);
        
        if (!$result) {
            throw new Exception("Không thể thêm HDV vào database");
        }
        
        $guide_id = $conn->lastInsertId();
        
        $conn->commit();
        
        $this->setFlash('success', 'Tạo HDV thành công!');
        $this->redirect('?act=admin_guides_view&id=' . $guide_id);
        
    } catch (PDOException $e) {
        if (isset($conn)) {
            $conn->rollBack();
        }
        $this->setFlash('error', "Lỗi database: " . $e->getMessage());
        $this->renderView('./views/admin/guides/create.php', ['form_data' => $_POST]);
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollBack();
        }
        $this->setFlash('error', "Lỗi: " . $e->getMessage());
        $this->renderView('./views/admin/guides/create.php', ['form_data' => $_POST]);
    }
}
    
    private function handleUpdateGuide($guide_id) {
        require_once './commons/env.php';
        require_once './commons/function.php';
        
        $conn = connectDB();
        
        try {
            $conn->beginTransaction();
            
            // Validation
            $errors = [];
            if (empty($_POST['full_name'])) {
                $errors[] = "Họ tên là bắt buộc";
            }
            
            if (!empty($errors)) {
                $this->setFlash('error', implode("<br>", $errors));
                $this->redirect('?act=admin_guides_edit&id=' . $guide_id);
                return;
            }
            
            // Prepare JSON data
            $languages = isset($_POST['languages']) && is_array($_POST['languages']) 
                ? json_encode($_POST['languages']) 
                : '[]';
            
            $skills = isset($_POST['skills']) && is_array($_POST['skills']) 
                ? json_encode($_POST['skills']) 
                : '[]';
            
            // Process certifications
            $certifications = [];
            if (!empty($_POST['certifications_text'])) {
                $cert_lines = explode("\n", $_POST['certifications_text']);
                foreach ($cert_lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $certifications[] = $line;
                    }
                }
            }
            $certifications_json = json_encode($certifications);
            
            // Process other languages
            if (!empty($_POST['other_languages'])) {
                $other_langs = array_map('trim', explode(',', $_POST['other_languages']));
                $existing_langs = isset($_POST['languages']) ? $_POST['languages'] : [];
                $all_langs = array_merge($existing_langs, $other_langs);
                $languages = json_encode(array_unique($all_langs));
            }
            
            // Check if avatar should be updated
            $avatar_update = "";
            $avatar_params = [];
            
            if (!empty($_FILES['avatar']['name'])) {
                $avatar_url = $this->uploadAvatar();
                $avatar_update = ", avatar_url = :avatar_url";
                $avatar_params['avatar_url'] = $avatar_url;
            }
            
            // UPDATE GUIDE
            $query = "UPDATE guides SET
                guide_code = :code,
                full_name = :name,
                email = :email,
                phone = :phone,
                id_number = :id_number,
                date_of_birth = :dob,
                address = :address,
                emergency_contact = :emergency_contact,
                languages = :languages,
                skills = :skills,
                certifications = :certifications,
                experience_years = :experience,
                status = :status,
                rating = :rating
                {$avatar_update}
            WHERE guide_id = :id";
            
            $stmt = $conn->prepare($query);
            
            $params = [
                'code' => $_POST['guide_code'] ?? '',
                'name' => $_POST['full_name'],
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'id_number' => $_POST['id_number'] ?? '',
                'dob' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'address' => $_POST['address'] ?? '',
                'emergency_contact' => $_POST['emergency_contact'] ?? '',
                'languages' => $languages,
                'skills' => $skills,
                'certifications' => $certifications_json,
                'experience' => $_POST['experience_years'] ?? 0,
                'status' => $_POST['status'] ?? 'active',
                'rating' => $_POST['rating'] ?? 0,
                'id' => $guide_id
            ];
            
            // Merge avatar params if exists
            $params = array_merge($params, $avatar_params);
            
            $result = $stmt->execute($params);
            
            if (!$result) {
                throw new Exception("Không thể cập nhật HDV");
            }
            
            $conn->commit();
            
            $this->setFlash('success', 'Cập nhật HDV thành công!');
            $this->redirect('?act=admin_guides_view&id=' . $guide_id);
            
        } catch (PDOException $e) {
            if (isset($conn)) {
                $conn->rollBack();
            }
            $this->setFlash('error', "Lỗi database: " . $e->getMessage());
            $this->redirect('?act=admin_guides_edit&id=' . $guide_id);
        } catch (Exception $e) {
            if (isset($conn)) {
                $conn->rollBack();
            }
            $this->setFlash('error', "Lỗi: " . $e->getMessage());
            $this->redirect('?act=admin_guides_edit&id=' . $guide_id);
        }
    }
    
    private function uploadAvatar() {
        if (empty($_FILES['avatar']['name'])) {
            return null;
        }
        
        $upload_dir = './uploads/guides/';
        
        // Create directory if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['avatar']['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            throw new Exception("Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)");
        }
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_name = 'avatar_' . time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        // Move uploaded file
        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
            throw new Exception("Không thể upload file ảnh");
        }
        
        return 'uploads/guides/' . $file_name;
    }
}
?>