<?php require_once './views/admin/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Chỉnh sửa Hướng dẫn viên: <?= htmlspecialchars($guide['full_name']) ?></h1>
    
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
            <form method="POST" action="?act=admin_guides_edit&id=<?= $guide['guide_id'] ?>" enctype="multipart/form-data">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="guide_code">Mã HDV</label>
                            <input type="text" class="form-control" id="guide_code" name="guide_code" 
                                   value="<?= htmlspecialchars($guide['guide_code']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required
                                   value="<?= htmlspecialchars($guide['full_name']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="date_of_birth">Ngày sinh</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                   value="<?= !empty($guide['date_of_birth']) ? $guide['date_of_birth'] : '' ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="id_number">Số CMND/CCCD</label>
                            <input type="text" class="form-control" id="id_number" name="id_number"
                                   value="<?= htmlspecialchars($guide['id_number']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($guide['email']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="<?= htmlspecialchars($guide['phone']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="emergency_contact">Liên hệ khẩn cấp</label>
                            <input type="text" class="form-control" id="emergency_contact" name="emergency_contact"
                                   value="<?= htmlspecialchars($guide['emergency_contact']) ?>">
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Current Avatar -->
                        <?php if (!empty($guide['avatar_url'])): ?>
                        <div class="form-group">
                            <label>Ảnh hiện tại</label><br>
                            <img src="<?= BASE_URL . '/' . $guide['avatar_url'] ?>" 
                                 class="img-thumbnail mb-2" style="max-width: 150px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_avatar" id="remove_avatar" value="1">
                                <label class="form-check-label" for="remove_avatar">
                                    Xóa ảnh hiện tại
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="avatar">Cập nhật ảnh đại diện</label>
                            <input type="file" class="form-control-file" id="avatar" name="avatar" accept="image/*">
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active" <?= $guide['status'] == 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                                <option value="inactive" <?= $guide['status'] == 'inactive' ? 'selected' : '' ?>>Không hoạt động</option>
                                <option value="on_leave" <?= $guide['status'] == 'on_leave' ? 'selected' : '' ?>>Nghỉ phép</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="experience_years">Số năm kinh nghiệm</label>
                            <input type="number" class="form-control" id="experience_years" name="experience_years" 
                                   min="0" value="<?= $guide['experience_years'] ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="rating">Đánh giá (0-5)</label>
                            <input type="number" class="form-control" id="rating" name="rating" 
                                   min="0" max="5" step="0.1" value="<?= $guide['rating'] ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Address -->
                <div class="form-group">
                    <label for="address">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($guide['address']) ?></textarea>
                </div>
                <div class="form-group">
    <label for="category_id">Nhóm HDV</label>
    <select class="form-control" id="category_id" name="category_id">
        <option value="">-- Chọn nhóm HDV --</option>
        <?php
        // Lấy danh sách categories
        require_once './commons/env.php';
        require_once './commons/function.php';
        $conn = connectDB();
        
        $stmt = $conn->query("SELECT category_id, category_name, category_type FROM guide_categories WHERE is_active = 1 ORDER BY category_type, category_name");
        $categories = $stmt->fetchAll();
        
        $currentCategory = $guide['category_id'] ?? ($_POST['category_id'] ?? '');
        
        $groupedCategories = [];
        foreach ($categories as $cat) {
            $groupedCategories[$cat['category_type']][] = $cat;
        }
        
        $categoryTypes = [
            'location' => '📍 Theo địa điểm',
            'specialization' => '⭐ Theo chuyên môn',
            'client_type' => '👥 Theo loại khách'
        ];
        
        foreach ($categoryTypes as $typeKey => $typeLabel) {
            if (!empty($groupedCategories[$typeKey])) {
                echo '<optgroup label="' . htmlspecialchars($typeLabel) . '">';
                foreach ($groupedCategories[$typeKey] as $cat) {
                    $selected = ($currentCategory == $cat['category_id']) ? 'selected' : '';
                    echo '<option value="' . $cat['category_id'] . '" ' . $selected . '>' . 
                         htmlspecialchars($cat['category_name']) . '</option>';
                }
                echo '</optgroup>';
            }
        }
        ?>
    </select>
    <small class="form-text text-muted">Phân loại HDV theo nhóm để dễ quản lý</small>
</div>
                <!-- Languages -->
                <div class="form-group">
                    <label>Ngôn ngữ</label>
                    <div>
                        <?php 
                        $common_languages = ['Vietnamese', 'English', 'French', 'Chinese', 'Japanese', 'Korean', 'Russian'];
                        $selected_langs = is_array($guide['languages']) ? $guide['languages'] : [];
                        
                        foreach ($common_languages as $lang): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="languages[]" 
                                   value="<?= $lang ?>" id="lang_<?= $lang ?>"
                                   <?= in_array($lang, $selected_langs) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="lang_<?= $lang ?>"><?= $lang ?></label>
                        </div>
                        <?php endforeach; ?>
                        <input type="text" class="form-control mt-2" placeholder="Ngôn ngữ khác (phân cách bằng dấu phẩy)" 
                               name="other_languages" value="">
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
                        $selected_skills = is_array($guide['skills']) ? $guide['skills'] : [];
                        
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
                
                <!-- Certifications -->
                <div class="form-group">
                    <label for="certifications_text">Chứng chỉ (mỗi chứng chỉ một dòng)</label>
                    <textarea class="form-control" id="certifications_text" name="certifications_text" rows="3"><?php
                        if (is_array($guide['certifications']) && !empty($guide['certifications'])) {
                            echo htmlspecialchars(implode("\n", $guide['certifications']));
                        }
                    ?></textarea>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật HDV
                    </button>
                    <a href="?act=admin_guides_view&id=<?= $guide['guide_id'] ?>" class="btn btn-secondary">Hủy</a>
                    <a href="?act=admin_guides" class="btn btn-outline-secondary">Quay lại danh sách</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once './views/admin/footer.php'; ?>