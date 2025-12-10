<?php require_once './views/admin/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Thêm Nhóm HDV mới</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin Nhóm HDV</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="?act=admin_guide_category_create">
                        <div class="form-group">
                            <label for="category_name">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                            <small class="form-text text-muted">Ví dụ: Nội địa, Quốc tế, Chuyên tuyến Sapa...</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_type">Loại nhóm <span class="text-danger">*</span></label>
                            <select class="form-control" id="category_type" name="category_type" required>
                                <option value="">-- Chọn loại nhóm --</option>
                                <?php foreach ($categoryTypes as $key => $name): ?>
                                <option value="<?= $key ?>"><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color_code">Màu sắc</label>
                                    <input type="color" class="form-control" id="color_code" name="color_code" 
                                           value="#6c757d" style="height: 45px; padding: 5px;">
                                    <small class="form-text text-muted">Chọn màu đại diện cho nhóm</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="icon">Biểu tượng (Icon)</label>
                                    <select class="form-control" id="icon" name="icon">
                                        <option value="">-- Chọn biểu tượng --</option>
                                        <option value="map-marker-alt">📍 Địa điểm</option>
                                        <option value="globe-asia">🌏 Quốc tế</option>
                                        <option value="mountain">⛰️ Núi</option>
                                        <option value="ship">🚢 Biển</option>
                                        <option value="umbrella-beach">🏖️ Biển</option>
                                        <option value="landmark">🏛️ Văn hóa</option>
                                        <option value="utensils">🍽️ Ẩm thực</option>
                                        <option value="users">👥 Đoàn</option>
                                        <option value="user">👤 Lẻ</option>
                                        <option value="crown">👑 VIP</option>
                                        <option value="home">🏠 Gia đình</option>
                                        <option value="briefcase">💼 Doanh nghiệp</option>
                                        <option value="star">⭐ Chuyên môn</option>
                                        <option value="history">📜 Lịch sử</option>
                                        <option value="camera">📷 Chụp ảnh</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt nhóm này
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu Nhóm
                            </button>
                            <a href="?act=admin_guide_categories" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hướng dẫn phân loại</h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-3">Các loại nhóm HDV:</h6>
                    
                    <div class="mb-3">
                        <span class="badge badge-primary mb-1">Theo địa điểm</span>
                        <p class="small mb-2">Phân loại theo khu vực địa lý hoạt động</p>
                        <ul class="small pl-3 mb-0">
                            <li>Nội địa / Quốc tế</li>
                            <li>Miền Bắc / Trung / Nam</li>
                            <li>Tỉnh thành cụ thể</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge badge-success mb-1">Theo chuyên môn</span>
                        <p class="small mb-2">Phân loại theo chuyên môn đặc thù</p>
                        <ul class="small pl-3 mb-0">
                            <li>Chuyên tuyến (Sapa, Hạ Long...)</li>
                            <li>Chuyên văn hóa / lịch sử</li>
                            <li>Chuyên ẩm thực</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge badge-warning mb-1">Theo loại khách</span>
                        <p class="small mb-2">Phân loại theo đối tượng khách hàng</p>
                        <ul class="small pl-3 mb-0">
                            <li>Khách đoàn / Khách lẻ</li>
                            <li>Khách VIP / Doanh nghiệp</li>
                            <li>Khách gia đình / Người lớn tuổi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview màu sắc
document.getElementById('color_code').addEventListener('input', function(e) {
    document.getElementById('color_preview').style.backgroundColor = e.target.value;
});

// Auto select icon based on category name
document.getElementById('category_name').addEventListener('input', function(e) {
    const name = e.target.value.toLowerCase();
    const iconSelect = document.getElementById('icon');
    
    const iconMapping = {
        'nội địa': 'map-marker-alt',
        'quốc tế': 'globe-asia',
        'miền bắc': 'mountain',
        'miền trung': 'sun',
        'miền nam': 'water',
        'sapa': 'mountain',
        'hạ long': 'ship',
        'đà nẵng': 'umbrella-beach',
        'phú quốc': 'fish',
        'văn hóa': 'landmark',
        'ẩm thực': 'utensils',
        'đoàn': 'users',
        'lẻ': 'user',
        'vip': 'crown',
        'gia đình': 'home',
        'doanh nghiệp': 'briefcase'
    };
    
    for (const [keyword, icon] of Object.entries(iconMapping)) {
        if (name.includes(keyword)) {
            iconSelect.value = icon;
            break;
        }
    }
});
</script>

<?php require_once './views/admin/footer.php'; ?>