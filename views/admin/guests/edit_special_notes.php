<?php
$pageTitle = "Chỉnh sửa Ghi chú Đặc biệt";
require_once './views/admin/header.php';
?>

<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header py-3 bg-warning">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="bi bi-exclamation-triangle"></i> <?= $pageTitle ?>
            </h6>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Khách hàng:</strong> <?= htmlspecialchars($guest['full_name'] ?? '') ?>
                        <?php if (isset($guest['booking_code'])): ?>
                            | <strong>Booking:</strong> <?= htmlspecialchars($guest['booking_code']) ?>
                        <?php endif; ?>
                        <?php if (isset($guest['tour_name'])): ?>
                            | <strong>Tour:</strong> <?= htmlspecialchars($guest['tour_name']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="guest_id" value="<?= $guest['guest_id'] ?? '' ?>">
                
                <!-- Dietary Restrictions Section -->
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-25">
                        <h5 class="mb-0">
                            <i class="bi bi-egg-fried text-warning"></i> 
                            Yêu cầu Ăn uống / Chế độ Ăn
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Chế độ ăn đặc biệt</label>
                            <div class="row mb-3">
                                <?php 
                                $dietary_options = [
                                    'none' => 'Không có yêu cầu đặc biệt',
                                    'vegetarian' => 'Ăn chay',
                                    'vegan' => 'Thuần chay',
                                    'halal' => 'Halal (Đồ ăn Hồi giáo)',
                                    'kosher' => 'Kosher (Đồ ăn Do Thái)',
                                    'no_beef' => 'Không ăn thịt bò',
                                    'no_pork' => 'Không ăn thịt heo',
                                    'no_seafood' => 'Không ăn hải sản',
                                    'gluten_free' => 'Không gluten',
                                    'lactose_free' => 'Không lactose',
                                    'low_salt' => 'Ăn nhạt (Giảm muối)',
                                    'low_sugar' => 'Giảm đường',
                                    'low_fat' => 'Giảm chất béo',
                                    'other' => 'Khác'
                                ];
                                
                                $current_diet = isset($guest['dietary_restrictions']) ? strtolower($guest['dietary_restrictions']) : '';
                                ?>
                                
                                <?php foreach (array_chunk($dietary_options, 4, true) as $chunk): ?>
                                <div class="col-md-6">
                                    <?php foreach ($chunk as $key => $label): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input dietary-checkbox" 
                                               type="checkbox" 
                                               name="dietary_options[]" 
                                               value="<?= $key ?>"
                                               id="diet_<?= $key ?>"
                                               <?= strpos($current_diet, $key) !== false ? 'checked' : '' ?>
                                               <?= $key === 'none' ? 'onchange="toggleDietaryOther()"' : '' ?>>
                                        <label class="form-check-label" for="diet_<?= $key ?>">
                                            <?= $label ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mb-3" id="dietaryOtherSection" style="<?= strpos($current_diet, 'other') === false ? 'display:none;' : '' ?>">
                                <label class="form-label">Yêu cầu ăn uống khác (chi tiết)</label>
                                <textarea class="form-control" name="dietary_other" rows="3" 
                                          placeholder="Mô tả chi tiết yêu cầu ăn uống đặc biệt..."><?= 
                                    htmlspecialchars($guest['dietary_restrictions'] ?? '') 
                                ?></textarea>
                                <small class="text-muted">Ví dụ: Dị ứng đậu phộng, không ăn đồ cay, chỉ ăn chín uống sôi...</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dị ứng thực phẩm</label>
                            <textarea class="form-control" name="food_allergies" rows="2" 
                                      placeholder="Liệt kê các loại thực phẩm dị ứng..."><?= 
                                htmlspecialchars($guest['food_allergies'] ?? '') 
                            ?></textarea>
                            <small class="text-muted">Ví dụ: Dị ứng hải sản, trứng, sữa, đậu phộng...</small>
                        </div>
                    </div>
                </div>
                
                <!-- Medical Information Section -->
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger bg-opacity-25">
                        <h5 class="mb-0">
                            <i class="bi bi-heart-pulse text-danger"></i> 
                            Thông tin Y tế / Sức khỏe
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bệnh lý / Tình trạng sức khỏe</label>
                            <div class="row mb-3">
                                <?php 
                                $medical_options = [
                                    'none' => 'Không có vấn đề sức khỏe đặc biệt',
                                    'hypertension' => 'Cao huyết áp',
                                    'diabetes' => 'Tiểu đường',
                                    'heart_disease' => 'Bệnh tim mạch',
                                    'asthma' => 'Hen suyễn',
                                    'epilepsy' => 'Động kinh',
                                    'motion_sickness' => 'Say tàu xe',
                                    'back_pain' => 'Đau lưng',
                                    'pregnant' => 'Đang mang thai',
                                    'disabled' => 'Khuyết tật / Hạn chế vận động',
                                    'other' => 'Khác'
                                ];
                                
                                $current_medical = isset($guest['medical_notes']) ? strtolower($guest['medical_notes']) : '';
                                ?>
                                
                                <?php foreach (array_chunk($medical_options, 3, true) as $chunk): ?>
                                <div class="col-md-6">
                                    <?php foreach ($chunk as $key => $label): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input medical-checkbox" 
                                               type="checkbox" 
                                               name="medical_conditions[]" 
                                               value="<?= $key ?>"
                                               id="med_<?= $key ?>"
                                               <?= strpos($current_medical, $key) !== false ? 'checked' : '' ?>
                                               <?= $key === 'none' ? 'onchange="toggleMedicalOther()"' : '' ?>>
                                        <label class="form-check-label" for="med_<?= $key ?>">
                                            <?= $label ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mb-3" id="medicalOtherSection" style="<?= strpos($current_medical, 'other') === false ? 'display:none;' : '' ?>">
                                <label class="form-label">Thông tin y tế khác (chi tiết)</label>
                                <textarea class="form-control" name="medical_other" rows="3" 
                                          placeholder="Mô tả chi tiết tình trạng sức khỏe, bệnh lý..."><?= 
                                    htmlspecialchars($guest['medical_notes'] ?? '') 
                                ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Thuốc đang sử dụng</label>
                                <textarea class="form-control" name="medications" rows="2" 
                                          placeholder="Liệt kê các loại thuốc đang dùng..."><?= 
                                    htmlspecialchars($guest['medications'] ?? '') 
                                ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nhóm máu</label>
                                <select class="form-control" name="blood_type">
                                    <option value="">-- Chọn nhóm máu --</option>
                                    <option value="A" <?= ($guest['blood_type'] ?? '') == 'A' ? 'selected' : '' ?>>A</option>
                                    <option value="B" <?= ($guest['blood_type'] ?? '') == 'B' ? 'selected' : '' ?>>B</option>
                                    <option value="AB" <?= ($guest['blood_type'] ?? '') == 'AB' ? 'selected' : '' ?>>AB</option>
                                    <option value="O" <?= ($guest['blood_type'] ?? '') == 'O' ? 'selected' : '' ?>>O</option>
                                    <option value="unknown" <?= ($guest['blood_type'] ?? '') == 'unknown' ? 'selected' : '' ?>>Không rõ</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lưu ý sơ cứu / Cấp cứu</label>
                            <textarea class="form-control" name="emergency_notes" rows="2" 
                                      placeholder="Hướng dẫn sơ cứu khi cần thiết..."><?= 
                                htmlspecialchars($guest['emergency_notes'] ?? '') 
                            ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Special Requests Section -->
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info bg-opacity-25">
                        <h5 class="mb-0">
                            <i class="bi bi-star text-info"></i> 
                            Yêu cầu Đặc biệt Khác
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Yêu cầu về Phòng nghỉ</label>
                                <div class="mb-2">
                                    <?php 
                                    $room_request_options = [
                                        'low_floor' => 'Tầng thấp',
                                        'high_floor' => 'Tầng cao',
                                        'quiet_room' => 'Phòng yên tĩnh',
                                        'connecting_rooms' => 'Phòng thông nhau',
                                        'extra_bed' => 'Thêm giường phụ',
                                        'non_smoking' => 'Phòng không hút thuốc',
                                        'near_elevator' => 'Gần thang máy',
                                        'away_elevator' => 'Xa thang máy',
                                        'bathtub' => 'Có bồn tắm',
                                        'shower' => 'Chỉ vòi sen',
                                        'balcony' => 'Có ban công'
                                    ];
                                    
                                    $current_room_requests = isset($guest['room_requests']) ? json_decode($guest['room_requests'], true) : [];
                                    ?>
                                    
                                    <?php foreach (array_chunk($room_request_options, 4, true) as $chunk): ?>
                                    <div class="col-12">
                                        <?php foreach ($chunk as $key => $label): ?>
                                        <div class="form-check form-check-inline mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="room_requests[]" 
                                                   value="<?= $key ?>"
                                                   id="room_<?= $key ?>"
                                                   <?= in_array($key, $current_room_requests) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="room_<?= $key ?>">
                                                <?= $label ?>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <textarea class="form-control mt-2" name="room_requests_other" rows="2" 
                                          placeholder="Yêu cầu khác về phòng..."><?= 
                                    htmlspecialchars($guest['room_requests_other'] ?? '') 
                                ?></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Yêu cầu về Di chuyển</label>
                                <div class="mb-2">
                                    <?php 
                                    $transport_options = [
                                        'front_seat' => 'Ghế trước xe',
                                        'window_seat' => 'Ghế cửa sổ',
                                        'aisle_seat' => 'Ghế lối đi',
                                        'near_door' => 'Gần cửa lên/xuống',
                                        'wheelchair_access' => 'Có xe lăn',
                                        'extra_legroom' => 'Cần thêm khoảng chân'
                                    ];
                                    
                                    $current_transport = isset($guest['transport_requests']) ? json_decode($guest['transport_requests'], true) : [];
                                    ?>
                                    
                                    <?php foreach (array_chunk($transport_options, 3, true) as $chunk): ?>
                                    <div class="col-12">
                                        <?php foreach ($chunk as $key => $label): ?>
                                        <div class="form-check form-check-inline mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="transport_requests[]" 
                                                   value="<?= $key ?>"
                                                   id="transport_<?= $key ?>"
                                                   <?= in_array($key, $current_transport) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="transport_<?= $key ?>">
                                                <?= $label ?>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <textarea class="form-control mt-2" name="transport_requests_other" rows="2" 
                                          placeholder="Yêu cầu khác về di chuyển..."><?= 
                                    htmlspecialchars($guest['transport_requests_other'] ?? '') 
                                ?></textarea>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Yêu cầu Chung / Đặc biệt Khác</label>
                            <textarea class="form-control" name="special_requests" rows="3" 
                                      placeholder="Các yêu cầu đặc biệt khác..."><?= 
                                htmlspecialchars($guest['special_requests'] ?? '') 
                            ?></textarea>
                            <small class="text-muted">Ví dụ: Kỷ niệm ngày cưới, sinh nhật, yêu cầu hướng dẫn viên nói tiếng Anh...</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Sở thích / Hoạt động yêu thích</label>
                                <textarea class="form-control" name="hobbies_interests" rows="2" 
                                          placeholder="Sở thích của khách..."><?= 
                                    htmlspecialchars($guest['hobbies_interests'] ?? '') 
                                ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Lịch sử Du lịch / Trải nghiệm trước đây</label>
                                <textarea class="form-control" name="travel_history" rows="2" 
                                          placeholder="Các tour đã tham gia, trải nghiệm..."><?= 
                                    htmlspecialchars($guest['travel_history'] ?? '') 
                                ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Emergency Contact Section -->
                <div class="card mb-4 border-success">
                    <div class="card-header bg-success bg-opacity-25">
                        <h5 class="mb-0">
                            <i class="bi bi-telephone text-success"></i> 
                            Thông tin Liên hệ Khẩn cấp
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Người liên hệ khẩn cấp *</label>
                                <input type="text" class="form-control" name="emergency_contact_name" 
                                       value="<?= htmlspecialchars($guest['emergency_contact_name'] ?? $guest['emergency_contact'] ?? '') ?>"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Số điện thoại *</label>
                                <input type="tel" class="form-control" name="emergency_contact_phone" 
                                       value="<?= htmlspecialchars($guest['emergency_contact_phone'] ?? '') ?>"
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Mối quan hệ</label>
                                <select class="form-control" name="emergency_relationship">
                                    <option value="">-- Chọn mối quan hệ --</option>
                                    <option value="spouse" <?= ($guest['emergency_relationship'] ?? '') == 'spouse' ? 'selected' : '' ?>>Vợ/Chồng</option>
                                    <option value="parent" <?= ($guest['emergency_relationship'] ?? '') == 'parent' ? 'selected' : '' ?>>Cha/Mẹ</option>
                                    <option value="child" <?= ($guest['emergency_relationship'] ?? '') == 'child' ? 'selected' : '' ?>>Con</option>
                                    <option value="sibling" <?= ($guest['emergency_relationship'] ?? '') == 'sibling' ? 'selected' : '' ?>>Anh/Chị/Em</option>
                                    <option value="friend" <?= ($guest['emergency_relationship'] ?? '') == 'friend' ? 'selected' : '' ?>>Bạn</option>
                                    <option value="colleague" <?= ($guest['emergency_relationship'] ?? '') == 'colleague' ? 'selected' : '' ?>>Đồng nghiệp</option>
                                    <option value="other" <?= ($guest['emergency_relationship'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email liên hệ</label>
                                <input type="email" class="form-control" name="emergency_contact_email" 
                                       value="<?= htmlspecialchars($guest['emergency_contact_email'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Địa chỉ liên hệ khẩn cấp</label>
                            <textarea class="form-control" name="emergency_contact_address" rows="2"><?= 
                                htmlspecialchars($guest['emergency_contact_address'] ?? '') 
                            ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Notes for Staff -->
                <div class="card mb-4 border-secondary">
                    <div class="card-header bg-secondary bg-opacity-25">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-left-text text-secondary"></i> 
                            Ghi chú Nội bộ cho Nhân viên/HDV
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ghi chú riêng cho Hướng dẫn viên</label>
                            <textarea class="form-control" name="notes_for_guide" rows="3" 
                                      placeholder="Ghi chú đặc biệt cần lưu ý cho HDV..."><?= 
                                htmlspecialchars($guest['notes_for_guide'] ?? '') 
                            ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ghi chú cho Nhà hàng/Khách sạn</label>
                            <textarea class="form-control" name="notes_for_hotel" rows="2" 
                                      placeholder="Ghi chú cần thông báo cho khách sạn/nhà hàng..."><?= 
                                htmlspecialchars($guest['notes_for_hotel'] ?? '') 
                            ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="requires_special_attention" 
                                       id="special_attention" value="1"
                                       <?= ($guest['requires_special_attention'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold text-danger" for="special_attention">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    CẦN ĐẶC BIỆT QUAN TÂM / THEO DÕI
                                </label>
                            </div>
                            <small class="text-muted">Đánh dấu nếu khách cần sự quan tâm đặc biệt từ nhân viên/HDV</small>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Buttons -->
                <div class="mt-4">
                    <a href="?act=admin_guest_management&departure_id=<?= $departure_id ?>" 
                       class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách
                    </a>
                    
                    <a href="?act=admin_guest_detail&guest_id=<?= $guest['guest_id'] ?>&departure_id=<?= $departure_id ?>" 
                       class="btn btn-info">
                        <i class="bi bi-person-badge"></i> Xem chi tiết
                    </a>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu tất cả thay đổi
                    </button>
                    
                    <button type="button" class="btn btn-success" onclick="printSpecialNotes()">
                        <i class="bi bi-printer"></i> In ghi chú đặc biệt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Print Preview Modal -->
<div class="modal fade" id="printPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem trước khi in - Ghi chú Đặc biệt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="printPreviewContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> In ngay
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .form-check-label {
        cursor: pointer;
    }
    .card-header {
        border-bottom: 2px solid rgba(0,0,0,0.1);
    }
    .required-field::after {
        content: " *";
        color: red;
    }
    
    /* Print Styles */
    @media print {
        body * {
            visibility: hidden;
        }
        #printPreviewContent, #printPreviewContent * {
            visibility: visible;
        }
        #printPreviewContent {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>

<script>
    // Toggle dietary other section
    function toggleDietaryOther() {
        const noneChecked = document.getElementById('diet_none').checked;
        const otherChecked = document.getElementById('diet_other').checked;
        const otherSection = document.getElementById('dietaryOtherSection');
        
        if (noneChecked) {
            // Uncheck all other dietary options
            document.querySelectorAll('.dietary-checkbox:not(#diet_none)').forEach(cb => {
                cb.checked = false;
            });
            otherSection.style.display = 'none';
        } else if (otherChecked) {
            otherSection.style.display = 'block';
        } else {
            otherSection.style.display = 'none';
        }
    }
    
    // Toggle medical other section
    function toggleMedicalOther() {
        const noneChecked = document.getElementById('med_none').checked;
        const otherChecked = document.getElementById('med_other').checked;
        const otherSection = document.getElementById('medicalOtherSection');
        
        if (noneChecked) {
            // Uncheck all other medical options
            document.querySelectorAll('.medical-checkbox:not(#med_none)').forEach(cb => {
                cb.checked = false;
            });
            otherSection.style.display = 'none';
        } else if (otherChecked) {
            otherSection.style.display = 'block';
        } else {
            otherSection.style.display = 'none';
        }
    }
    
    // Initialize checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        toggleDietaryOther();
        toggleMedicalOther();
        
        // Add event listeners
        document.querySelectorAll('.dietary-checkbox').forEach(cb => {
            cb.addEventListener('change', toggleDietaryOther);
        });
        
        document.querySelectorAll('.medical-checkbox').forEach(cb => {
            cb.addEventListener('change', toggleMedicalOther);
        });
    });
    
    // Print special notes
    function printSpecialNotes() {
        // Collect form data
        const formData = new FormData(document.querySelector('form'));
        
        // Create print preview
        let printContent = `
            <div style="font-family: Arial, sans-serif; padding: 20px;">
                <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px;">
                    <h2 style="color: #333; margin-bottom: 5px;">GHI CHÚ ĐẶC BIỆT KHÁCH HÀNG</h2>
                    <h3 style="color: #666; margin-top: 0;">${document.querySelector('.alert-info strong').textContent}</h3>
                    <p>Ngày in: ${new Date().toLocaleDateString('vi-VN')}</p>
                </div>
        `;
        
        // Add sections
        const sections = [
            { title: 'YÊU CẦU ĂN UỐNG', icon: '🍽️', fields: ['dietary_options', 'food_allergies'] },
            { title: 'THÔNG TIN Y TẾ', icon: '🏥', fields: ['medical_conditions', 'medications', 'blood_type', 'emergency_notes'] },
            { title: 'YÊU CẦU PHÒNG NGHỈ', icon: '🏨', fields: ['room_requests', 'room_requests_other'] },
            { title: 'YÊU CẦU DI CHUYỂN', icon: '🚌', fields: ['transport_requests', 'transport_requests_other'] },
            { title: 'YÊU CẦU CHUNG', icon: '⭐', fields: ['special_requests', 'hobbies_interests', 'travel_history'] },
            { title: 'LIÊN HỆ KHẨN CẤP', icon: '📞', fields: ['emergency_contact_name', 'emergency_contact_phone', 'emergency_relationship', 'emergency_contact_email', 'emergency_contact_address'] },
            { title: 'GHI CHÚ NỘI BỘ', icon: '📝', fields: ['notes_for_guide', 'notes_for_hotel', 'requires_special_attention'] }
        ];
        
        sections.forEach(section => {
            let sectionContent = '';
            section.fields.forEach(field => {
                const value = formData.get(field) || formData.getAll(field).join(', ');
                if (value) {
                    sectionContent += `<p><strong>${field.replace(/_/g, ' ').toUpperCase()}:</strong> ${value}</p>`;
                }
            });
            
            if (sectionContent) {
                printContent += `
                    <div style="margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; padding: 15px;">
                        <h4 style="color: #2c3e50; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                            ${section.icon} ${section.title}
                        </h4>
                        ${sectionContent}
                    </div>
                `;
            }
        });
        
        printContent += `
                <div style="margin-top: 30px; padding-top: 10px; border-top: 1px dashed #999; text-align: center; font-size: 12px; color: #666;">
                    <p>--- Tài liệu nội bộ - Vui lòng giữ bí mật thông tin khách hàng ---</p>
                </div>
            </div>
        `;
        
        // Show in modal
        document.getElementById('printPreviewContent').innerHTML = printContent;
        const modal = new bootstrap.Modal(document.getElementById('printPreviewModal'));
        modal.show();
    }
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const emergencyName = document.querySelector('input[name="emergency_contact_name"]').value.trim();
        const emergencyPhone = document.querySelector('input[name="emergency_contact_phone"]').value.trim();
        
        if (!emergencyName || !emergencyPhone) {
            e.preventDefault();
            alert('Vui lòng nhập đầy đủ thông tin liên hệ khẩn cấp!');
            return false;
        }
        
        // Show loading
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang lưu...';
        submitBtn.disabled = true;
        
        return true;
    });
</script>

<?php require_once './views/admin/footer.php'; ?>