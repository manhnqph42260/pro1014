<?php require_once './views/admin/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thêm Hướng dẫn viên mới</h1>
    
    <!-- Hiển thị flash message -->
    <?php if ($flash = $this->getFlash()): ?>
    <div class="alert alert-<?= $flash['type'] == 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
        <?= $flash['message'] ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thông tin HDV</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="?act=admin_guides_create" enctype="multipart/form-data">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="guide_code">Mã HDV</label>
                            <input type="text" class="form-control" id="guide_code" name="guide_code" 
                                   placeholder="HDV001 (để trống để tự động tạo)"
                                   value="<?= htmlspecialchars($_POST['guide_code'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required
                                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="date_of_birth">Ngày sinh</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                   value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="id_number">Số CMND/CCCD</label>
                            <input type="text" class="form-control" id="id_number" name="id_number"
                                   value="<?= htmlspecialchars($_POST['id_number'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="emergency_contact">Liên hệ khẩn cấp</label>
                            <input type="text" class="form-control" id="emergency_contact" name="emergency_contact"
                                   value="<?= htmlspecialchars($_POST['emergency_contact'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active" <?= ($_POST['status'] ?? '') == 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                                <option value="inactive" <?= ($_POST['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                                <option value="on_leave" <?= ($_POST['status'] ?? '') == 'on_leave' ? 'selected' : '' ?>>Nghỉ phép</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="experience_years">Số năm kinh nghiệm</label>
                            <input type="number" class="form-control" id="experience_years" name="experience_years" 
                                   min="0" value="<?= $_POST['experience_years'] ?? 0 ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="rating">Đánh giá (0-5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" 
                                   min="0" max="5" step="0.1" value="<?= $_POST['rating'] ?? 0 ?>">
                        </div>
                        
                        <!-- Languages -->
                        <div class="form-group">
                            <label>Ngôn ngữ</label>
                            <div>
                                <?php 
                                $common_languages = ['Vietnamese', 'English', 'French', 'Chinese', 'Japanese', 'Korean', 'Russian'];
                                $selected_langs = isset($_POST['languages']) ? $_POST['languages'] : [];
                                
                                foreach ($common_languages as $lang): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="languages[]" 
                                           value="<?= $lang ?>" id="lang_<?= $lang ?>"
                                           <?= in_array($lang, $selected_langs) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="lang_<?= $lang ?>"><?= $lang ?></label>
                                </div>
                                <?php endforeach; ?>
                                <input type="text" class="form-control mt-2" placeholder="Ngôn ngữ khác (phân cách bằng dấu phẩy)" 
                                       name="other_languages" value="<?= htmlspecialchars($_POST['other_languages'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <!-- Skills -->
                        <div class="form-group">
                            <label>Kỹ năng chuyên môn</label>
                            <div>
                                <?php 
                                $common_skills = ['first_aid', 'photography', 'cooking', 'history', 'storytelling', 'team_management'];
                                $skill_labels = [
                                    'first_aid' => 'Sơ cứu',
                                    'photography' => 'Chụp ảnh',
                                    'cooking' => 'Nấu ăn',
                                    'history' => 'Lịch sử',
                                    'storytelling' => 'Kể chuyện',
                                    'team_management' => 'Quản lý nhóm'
                                ];
                                $selected_skills = isset($_POST['skills']) ? $_POST['skills'] : [];
                                
                                foreach ($common_skills as $skill): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="skills[]" 
                                           value="<?= $skill ?>" id="skill_<?= $skill ?>"
                                           <?= in_array($skill, $selected_skills) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="skill_<?= $skill ?>">
                                        <?= $skill_labels[$skill] ?? $skill ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Avatar Upload -->
                        <div class="form-group">
                            <label for="avatar">Ảnh đại diện</label>
                            <input type="file" class="form-control-file" id="avatar" name="avatar" accept="image/*">
                            <small class="form-text text-muted">Chấp nhận: JPEG, PNG, GIF, WebP (tối đa 2MB)</small>
                        </div>
                    </div>
                </div>
                
                <!-- Address -->
                <div class="form-group">
                    <label for="address">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
                
                <!-- Certifications -->
                <div class="form-group">
                    <label for="certifications_text">Chứng chỉ (mỗi chứng chỉ một dòng)</label>
                    <textarea class="form-control" id="certifications_text" name="certifications_text" rows="3"><?= htmlspecialchars($_POST['certifications_text'] ?? '') ?></textarea>
                    <small class="form-text text-muted">Mỗi chứng chỉ trên một dòng, format: Tên chứng chỉ - Năm cấp</small>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu HDV
                    </button>
                    <a href="?act=admin_guides" class="btn btn-secondary">Hủy</a>
                    <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Simple form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const fullName = document.getElementById('full_name').value.trim();
    if (!fullName) {
        e.preventDefault();
        alert('Vui lòng nhập họ tên HDV');
        document.getElementById('full_name').focus();
        return false;
    }
    
    // Validate rating
    const rating = parseFloat(document.getElementById('rating').value);
    if (rating < 0 || rating > 5) {
        e.preventDefault();
        alert('Đánh giá phải từ 0 đến 5');
        document.getElementById('rating').focus();
        return false;
    }
    
    // Validate experience
    const experience = parseInt(document.getElementById('experience_years').value);
    if (experience < 0) {
        e.preventDefault();
        alert('Số năm kinh nghiệm không được âm');
        document.getElementById('experience_years').focus();
        return false;
    }
    
    return true;
});
</script>

<?php require_once './views/admin/footer.php'; ?>