<?php
$page_title = "Tạo Tour mới";
require_once __DIR__ . '/../header.php'; 

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Lấy dữ liệu từ form
        $tour_data = [
            'tour_code' => $_POST['tour_code'] ?? '',
            'tour_name' => $_POST['tour_name'] ?? '',
            'destination' => $_POST['destination'] ?? '',
            'adult_count' => $_POST['adult_count'] ?? 2,
            'child_count' => $_POST['child_count'] ?? 0,
            'departure_date' => $_POST['departure_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'departure_location' => $_POST['departure_location'] ?? '',
            'end_location' => $_POST['end_location'] ?? '',
            'description' => $_POST['description'] ?? '',
            'route' => $_POST['route'] ?? '',
            'price_adult' => $_POST['price_adult'] ?? 0,
            'price_child' => $_POST['price_child'] ?? 0,
            'cancellation_policy' => $_POST['cancellation_policy'] ?? '',
            'terms_conditions' => $_POST['terms_conditions'] ?? '',
            'special_notes' => $_POST['special_notes'] ?? '',
            'supplier_notes' => $_POST['supplier_notes'] ?? '',
            'status' => $_POST['status'] ?? 'draft',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Validate dữ liệu bắt buộc
        if (empty($tour_data['tour_code']) || empty($tour_data['tour_name']) || empty($tour_data['destination'])) {
            throw new Exception("Vui lòng điền đầy đủ thông tin bắt buộc");
        }

        // Xử lý itinerary data
        $itinerary_data = [];
        if (isset($_POST['itinerary']) && is_array($_POST['itinerary'])) {
            foreach ($_POST['itinerary'] as $day_index => $day) {
                if (!empty($day['date']) || !empty($day['title'])) {
                    $day_data = [
                        'day_number' => $day_index + 1,
                        'date' => $day['date'] ?? '',
                        'title' => $day['title'] ?? 'Ngày ' . ($day_index + 1),
                        'time_slots' => []
                    ];
                    
                    // Xử lý time slots
                    if (isset($day['time_slots']) && is_array($day['time_slots'])) {
                        foreach ($day['time_slots'] as $slot_index => $slot) {
                            if (!empty($slot['time']) || !empty($slot['activity'])) {
                                $day_data['time_slots'][] = [
                                    'time' => $slot['time'] ?? '',
                                    'activity' => $slot['activity'] ?? ''
                                ];
                            }
                        }
                    }
                    
                    $itinerary_data[] = $day_data;
                }
            }
        }

        // Xử lý suppliers
        $suppliers_data = $_POST['suppliers'] ?? [];

        // Xử lý upload ảnh
        $uploaded_images = [];
        
        // Upload featured image
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $featured_image = handleImageUpload($_FILES['featured_image']);
            if ($featured_image) {
                $tour_data['featured_image'] = $featured_image;
            }
        } else {
            throw new Exception("Vui lòng chọn ảnh đại diện cho tour");
        }

        // Upload gallery images
        $gallery_images = [];
        if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
            for ($i = 0; $i < count($_FILES['gallery_images']['name']); $i++) {
                if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file_data = [
                        'name' => $_FILES['gallery_images']['name'][$i],
                        'type' => $_FILES['gallery_images']['type'][$i],
                        'tmp_name' => $_FILES['gallery_images']['tmp_name'][$i],
                        'error' => $_FILES['gallery_images']['error'][$i],
                        'size' => $_FILES['gallery_images']['size'][$i]
                    ];
                    
                    try {
                        $gallery_images[] = handleImageUpload($file_data);
                    } catch (Exception $e) {
                        // Bỏ qua lỗi upload từng ảnh, tiếp tục với ảnh khác
                        continue;
                    }
                }
            }
        }

        // TODO: Gọi hàm/model để lưu vào database
        // Giả lập lưu thành công
        $tour_id = rand(1000, 9999); // Tạm thời dùng random ID
        
        // Lưu thông tin thành công
        $_SESSION['success_message'] = "Tour '{$tour_data['tour_name']}' đã được tạo thành công với mã: {$tour_data['tour_code']}";
        
        // Chuyển hướng về trang list
        header('Location: ?act=admin_tours');
        exit;

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Hàm xử lý upload ảnh
function handleImageUpload($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception("Chỉ chấp nhận file ảnh JPG, PNG, WebP");
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception("File ảnh không được vượt quá 5MB");
    }
    
    // Tạo tên file mới
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'tour_' . time() . '_' . uniqid() . '.' . $extension;
    $upload_path = __DIR__ . '/../../uploads/tours/' . $filename;
    
    // Đảm bảo thư mục tồn tại
    if (!is_dir(dirname($upload_path))) {
        mkdir(dirname($upload_path), 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return 'uploads/tours/' . $filename;
    }
    
    throw new Exception("Không thể upload file ảnh");
}

// Dữ liệu mẫu cho các điểm đến với giá mặc định
$destinations = [
    'combo_2n3d_hanoi_halong' => [
        'name' => 'Combo Hà Nội - Hạ Long 2N3D',
        'code' => 'HNHL23',
        'duration' => '2 ngày 3 đêm',
        'duration_days' => 2,
        'price_adult' => 2500000,
        'price_child' => 1250000,
        'departure_location' => 'Hà Nội',
        'end_location' => 'Hà Nội',
        'description' => 'Khám phá thủ đô Hà Nội nghìn năm văn hiến và vịnh Hạ Long - kỳ quan thiên nhiên thế giới. Tour bao gồm tham quan phố cổ, hồ Hoàn Kiếm, chùa Trấn Quốc, và du thuyền ngắm cảnh vịnh Hạ Long.',
        'route' => 'Ngày 1: Hà Nội - Tham quan phố cổ - Hồ Hoàn Kiếm - Nhà hát lớn\nNgày 2: Hà Nội - Hạ Long - Du thuyền vịnh - Hang Sửng Sốt\nNgày 3: Hạ Long - Hà Nội - Chợ địa phương'
    ],
    'combo_3n4d_sapa' => [
        'name' => 'Combo Sapa 3N4D',
        'code' => 'SAPA34',
        'duration' => '3 ngày 4 đêm',
        'duration_days' => 3,
        'price_adult' => 3500000,
        'price_child' => 1750000,
        'departure_location' => 'Hà Nội',
        'end_location' => 'Hà Nội',
        'description' => 'Trải nghiệm vùng núi Tây Bắc với những thửa ruộng bậc thang tuyệt đẹp, gặp gỡ đồng bào dân tộc thiểu số và chinh phục đỉnh Fansipan.',
        'route' => 'Ngày 1: Hà Nội - Sapa - Bản Cát Cát\nNgày 2: Sapa - Bản Tả Phìn - Núi Hàm Rồng\nNgày 3: Sapa - Fansipan - Cầu kính\nNgày 4: Sapa - Hà Nội'
    ]
];

// Dữ liệu mẫu nhà cung cấp
$suppliers = [
    'hotel' => [
        ['id' => 'hotel_1', 'name' => 'Khách sạn Mường Thanh', 'type' => 'hotel'],
        ['id' => 'hotel_2', 'name' => 'Khách sạn Sheraton', 'type' => 'hotel'],
        ['id' => 'hotel_3', 'name' => 'Khách sạn Hilton', 'type' => 'hotel']
    ],
    'transport' => [
        ['id' => 'transport_1', 'name' => 'Công ty Vận tải ABC', 'type' => 'transport'],
        ['id' => 'transport_2', 'name' => 'Xe du lịch VIP', 'type' => 'transport']
    ],
    'restaurant' => [
        ['id' => 'restaurant_1', 'name' => 'Nhà hàng Hương Việt', 'type' => 'restaurant'],
        ['id' => 'restaurant_2', 'name' => 'Nhà hàng Sen Tây Hồ', 'type' => 'restaurant']
    ],
    'guide' => [
        ['id' => 'guide_1', 'name' => 'Hướng dẫn viên Nguyễn Văn A', 'type' => 'guide'],
        ['id' => 'guide_2', 'name' => 'Hướng dẫn viên Trần Thị B', 'type' => 'guide']
    ]
];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
    <div>
        <h1 class="h2">Tạo Tour mới</h1>
        <p class="mb-0">Thêm tour du lịch mới vào hệ thống với đầy đủ thông tin chi tiết</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="?act=admin_tours" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<!-- Hiển thị lỗi nếu có -->
<?php if (isset($error_message)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error_message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="tourForm">
                    <!-- Progress Steps -->
                    <div class="mb-4">
                        <div class="progress mb-3" style="height: 5px;">
                            <div class="progress-bar" id="formProgress" style="width: 0%"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="text-center">
                                <div class="step-indicator active" data-step="1">1</div>
                                <small>Thông tin cơ bản</small>
                            </div>
                            <div class="text-center">
                                <div class="step-indicator" data-step="2">2</div>
                                <small>Lịch trình</small>
                            </div>
                            <div class="text-center">
                                <div class="step-indicator" data-step="3">3</div>
                                <small>Hình ảnh</small>
                            </div>
                            <div class="text-center">
                                <div class="step-indicator" data-step="4">4</div>
                                <small>Giá & Chính sách</small>
                            </div>
                            <div class="text-center">
                                <div class="step-indicator" data-step="5">5</div>
                                <small>Nhà cung cấp</small>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Thông tin cơ bản -->
                    <div class="form-step active" id="step1">
                        <h5 class="mb-4 text-primary">
                            <i class="bi bi-info-circle me-2"></i>Thông tin cơ bản
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Điểm đến (Combo) <span class="text-danger">*</span></label>
                                    <select name="destination" class="form-select" id="destinationSelect" required>
                                        <option value="">-- Chọn điểm đến --</option>
                                        <?php foreach($destinations as $key => $destination): ?>
                                            <option value="<?php echo $key; ?>" 
                                                    data-duration="<?php echo $destination['duration']; ?>"
                                                    data-duration-days="<?php echo $destination['duration_days']; ?>"
                                                    data-code="<?php echo $destination['code']; ?>"
                                                    data-price-adult="<?php echo $destination['price_adult']; ?>"
                                                    data-price-child="<?php echo $destination['price_child']; ?>"
                                                    data-departure-location="<?php echo $destination['departure_location']; ?>"
                                                    data-end-location="<?php echo $destination['end_location']; ?>"
                                                    data-description="<?php echo htmlspecialchars($destination['description']); ?>"
                                                    data-route="<?php echo htmlspecialchars($destination['route']); ?>">
                                                <?php echo $destination['name']; ?> - <?php echo $destination['duration']; ?> [Mã: <?php echo $destination['code']; ?>]
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Chọn điểm đến để tự động điền thông tin tour</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mã Tour <span class="text-danger">*</span></label>
                                    <input type="text" name="tour_code" class="form-control" required 
                                           placeholder="VD: TOUR001" id="tourCode" value="<?php echo $_POST['tour_code'] ?? ''; ?>" readonly>
                                    <div class="form-text">Mã tour được tạo tự động, không thể chỉnh sửa</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tên Tour <span class="text-danger">*</span></label>
                                    <input type="text" name="tour_name" class="form-control" required 
                                           placeholder="Tên tour du lịch" id="tourName" value="<?php echo $_POST['tour_name'] ?? ''; ?>">
                                    <div class="form-text">Có thể chỉnh sửa tên tour nếu cần</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng người lớn <span class="text-danger">*</span></label>
                                            <input type="number" name="adult_count" class="form-control" required 
                                                   min="1" value="<?php echo $_POST['adult_count'] ?? 2; ?>" id="adultCount">
                                            <div class="form-text">Từ 16 tuổi trở lên</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Số lượng trẻ em</label>
                                            <input type="number" name="child_count" class="form-control" 
                                                   min="0" value="<?php echo $_POST['child_count'] ?? 0; ?>" id="childCount">
                                            <div class="form-text">Từ 5 - 15 tuổi (dưới 5 tuổi miễn phí)</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ngày khởi hành <span class="text-danger">*</span></label>
                                    <input type="date" name="departure_date" class="form-control" required 
                                           value="<?php echo $_POST['departure_date'] ?? ''; ?>" id="departureDate"
                                           min="<?php echo date('Y-m-d'); ?>">
                                    <div class="form-text">Chọn ngày bắt đầu tour</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ngày kết thúc</label>
                                    <input type="date" name="end_date" class="form-control" id="endDate" readonly>
                                    <div class="form-text" id="durationText"></div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Điểm khởi hành <span class="text-danger">*</span></label>
                                            <input type="text" name="departure_location" class="form-control" required 
                                                   placeholder="VD: Hà Nội" id="departureLocation" value="<?php echo $_POST['departure_location'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Điểm kết thúc</label>
                                            <input type="text" name="end_location" class="form-control" 
                                                   placeholder="VD: Hà Nội" id="endLocation" value="<?php echo $_POST['end_location'] ?? ''; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả tour</label>
                            <textarea name="description" class="form-control" rows="4" 
                                      placeholder="Mô tả về tour, điểm nổi bật, trải nghiệm..." id="descriptionTextarea"><?php echo $_POST['description'] ?? ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lộ trình tổng quan</label>
                            <textarea name="route" class="form-control" rows="3" 
                                      placeholder="Mô tả lộ trình tour..." id="routeTextarea"><?php echo $_POST['route'] ?? ''; ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <div></div>
                            <button type="button" class="btn btn-primary next-step" data-next="2">
                                Tiếp theo: Lịch trình <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Lịch trình chi tiết -->
                    <div class="form-step" id="step2">
                        <h5 class="mb-4 text-primary">
                            <i class="bi bi-calendar-week me-2"></i>Lịch trình chi tiết
                        </h5>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>Chi tiết từng ngày</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addDayBtn">
                                    <i class="bi bi-plus-circle me-1"></i>Thêm ngày
                                </button>
                            </div>
                            
                            <div id="itineraryDays">
                                <!-- Days will be added here dynamically -->
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-prev="1">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="3">
                                Tiếp theo: Hình ảnh <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Hình ảnh -->
                    <div class="form-step" id="step3">
                        <h5 class="mb-4 text-primary">
                            <i class="bi bi-images me-2"></i>Hình ảnh tour
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">Ảnh đại diện <span class="text-danger">*</span></label>
                                    <input type="file" name="featured_image" class="form-control" accept="image/*" required>
                                    <div class="form-text">Ảnh chính hiển thị cho tour (jpg, png, max 5MB)</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ảnh gallery</label>
                                    <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                                    <div class="form-text">Có thể chọn nhiều ảnh (tối đa 10 ảnh)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light">
                                    <h6>Hướng dẫn upload ảnh</h6>
                                    <ul class="small mb-0">
                                        <li>Ảnh đại diện: Tỷ lệ 16:9, độ phân giải tối thiểu 1200x675</li>
                                        <li>Ảnh gallery: Các góc nhìn khác nhau về tour</li>
                                        <li>Định dạng: JPG, PNG, WebP</li>
                                        <li>Kích thước tối đa: 5MB/ảnh</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-prev="2">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="4">
                                Tiếp theo: Giá & Chính sách <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Giá & Chính sách -->
                    <div class="form-step" id="step4">
                        <h5 class="mb-4 text-primary">
                            <i class="bi bi-currency-dollar me-2"></i>Giá & Chính sách
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h6>Giá tour mặc định</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Giá người lớn</label>
                                                <div class="input-group">
                                                    <input type="number" name="price_adult" class="form-control" required 
                                                           id="priceAdult" readonly>
                                                    <span class="input-group-text">₫</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Giá trẻ em</label>
                                                <div class="input-group">
                                                    <input type="number" name="price_child" class="form-control" 
                                                           id="priceChild" readonly>
                                                    <span class="input-group-text">₫</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-2 bg-white rounded border">
                                        <small class="text-muted"><strong>Tổng giá dự kiến:</strong> <span id="totalPrice">0</span> ₫</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6>Chính sách hủy tour</h6>
                                    <textarea name="cancellation_policy" class="form-control" rows="3" 
                                              placeholder="Chính sách hủy tour, hoàn tiền..."><?php echo $_POST['cancellation_policy'] ?? 'Hủy trước 7 ngày: hoàn 100%
Hủy trước 3-7 ngày: hoàn 50%
Hủy dưới 3 ngày: không hoàn tiền'; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6>Điều khoản & Điều kiện</h6>
                                    <textarea name="terms_conditions" class="form-control" rows="5" 
                                              placeholder="Các điều khoản và điều kiện của tour..."><?php echo $_POST['terms_conditions'] ?? '1. Khách hàng cần mang theo CMND/Passport
2. Trẻ em cần có giấy khai sinh bản sao
3. Không hoàn tiền cho các dịch vụ không sử dụng
4. Công ty có quyền thay đổi lịch trình nếu cần thiết'; ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <h6>Ghi chú đặc biệt</h6>
                                    <textarea name="special_notes" class="form-control" rows="2" 
                                              placeholder="Các ghi chú đặc biệt cho khách hàng..."><?php echo $_POST['special_notes'] ?? 'Mang theo kem chống nắng, thuốc cá nhân nếu cần'; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-prev="3">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="5">
                                Tiếp theo: Nhà cung cấp <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 5: Nhà cung cấp -->
                    <div class="form-step" id="step5">
                        <h5 class="mb-4 text-primary">
                            <i class="bi bi-building me-2"></i>Nhà cung cấp dịch vụ
                        </h5>

                        <div class="row">
                            <?php foreach($suppliers as $type => $supplier_list): 
                                $type_name = [
                                    'hotel' => 'Khách sạn',
                                    'transport' => 'Vận chuyển', 
                                    'restaurant' => 'Nhà hàng',
                                    'guide' => 'Hướng dẫn viên'
                                ][$type] ?? $type;
                            ?>
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><?php echo $type_name; ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <?php foreach($supplier_list as $supplier): ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="suppliers[]" 
                                                   value="<?php echo $supplier['id']; ?>"
                                                   id="supplier_<?php echo $supplier['id']; ?>">
                                            <label class="form-check-label" for="supplier_<?php echo $supplier['id']; ?>">
                                                <?php echo $supplier['name']; ?>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú nhà cung cấp</label>
                            <textarea name="supplier_notes" class="form-control" rows="2" 
                                      placeholder="Ghi chú về nhà cung cấp, hợp đồng..."><?php echo $_POST['supplier_notes'] ?? ''; ?></textarea>
                        </div>

                        <!-- Final Step Actions -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái tour</label>
                                    <select name="status" class="form-select">
                                        <option value="draft">Bản nháp</option>
                                        <option value="published">Đã xuất bản</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary prev-step" data-prev="4">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại
                            </button>
                            <div>
                                <a href="?act=admin_tours" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-x-circle me-1"></i>Hủy bỏ
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-check-circle me-1"></i>Hoàn thành & Tạo Tour
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template for Itinerary Day -->
<template id="dayTemplate">
    <div class="itinerary-day card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 day-title">Ngày <span class="day-number">1</span></h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-day">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Ngày</label>
                        <input type="date" class="form-control day-date" name="itinerary[][date]">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề ngày</label>
                        <input type="text" class="form-control day-title-input" name="itinerary[][title]" placeholder="VD: Khám phá Hà Nội">
                    </div>
                </div>
            </div>
            
            <div class="time-slots">
                <!-- Time slots will be added here -->
            </div>
            
            <button type="button" class="btn btn-sm btn-outline-primary add-time-slot">
                <i class="bi bi-plus-circle me-1"></i>Thêm khung giờ
            </button>
        </div>
    </div>
</template>

<!-- Template for Time Slot -->
<template id="timeSlotTemplate">
    <div class="time-slot row mb-3 border-bottom pb-3">
        <div class="col-md-3">
            <label class="form-label">Khung giờ</label>
            <input type="text" class="form-control" name="itinerary[][time_slots][][time]" placeholder="VD: 08:00-10:00">
        </div>
        <div class="col-md-9">
            <label class="form-label">Hoạt động</label>
            <textarea class="form-control" name="itinerary[][time_slots][][activity]" rows="2" placeholder="Mô tả hoạt động..."></textarea>
        </div>
    </div>
</template>

<style>
.form-step {
    display: none;
}
.form-step.active {
    display: block;
}
.step-indicator {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 5px;
    font-weight: bold;
}
.step-indicator.active {
    background: #0d6efd;
    color: white;
}
.itinerary-day {
    border: 1px solid #dee2e6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Step navigation
    const steps = document.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.step-indicator');
    const progressBar = document.getElementById('formProgress');
    let currentStep = 1;

    // Navigation functions
    function showStep(stepNumber) {
        steps.forEach(step => step.classList.remove('active'));
        stepIndicators.forEach(indicator => indicator.classList.remove('active'));
        
        document.getElementById(`step${stepNumber}`).classList.add('active');
        document.querySelector(`[data-step="${stepNumber}"]`).classList.add('active');
        
        // Update progress bar
        const progress = ((stepNumber - 1) / (steps.length - 1)) * 100;
        progressBar.style.width = `${progress}%`;
        
        currentStep = stepNumber;
    }

    // Next/Prev buttons
    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
            const nextStep = parseInt(this.dataset.next);
            if (validateStep(currentStep)) {
                showStep(nextStep);
            }
        });
    });

    document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function() {
            const prevStep = parseInt(this.dataset.prev);
            showStep(prevStep);
        });
    });

    // Step validation
    function validateStep(step) {
        switch(step) {
            case 1:
                const destination = document.getElementById('destinationSelect');
                const tourName = document.getElementById('tourName');
                const departureDate = document.getElementById('departureDate');
                if (!destination.value) {
                    alert('Vui lòng chọn điểm đến');
                    destination.focus();
                    return false;
                }
                if (!tourName.value.trim()) {
                    alert('Vui lòng nhập tên tour');
                    tourName.focus();
                    return false;
                }
                if (!departureDate.value) {
                    alert('Vui lòng chọn ngày khởi hành');
                    departureDate.focus();
                    return false;
                }
                return true;
            case 3:
                const featuredImage = document.querySelector('input[name="featured_image"]');
                if (featuredImage.files.length === 0) {
                    alert('Vui lòng chọn ảnh đại diện cho tour');
                    return false;
                }
                return true;
            default:
                return true;
        }
    }

    // Itinerary management
    let dayCount = 0;
    const itineraryDays = document.getElementById('itineraryDays');
    const dayTemplate = document.getElementById('dayTemplate');
    const timeSlotTemplate = document.getElementById('timeSlotTemplate');

    // Add new day
    document.getElementById('addDayBtn').addEventListener('click', function() {
        addNewDay();
    });

    function addNewDay() {
        dayCount++;
        const dayClone = dayTemplate.content.cloneNode(true);
        const dayElement = dayClone.querySelector('.itinerary-day');
        
        // Update day number
        dayElement.querySelector('.day-number').textContent = dayCount;
        dayElement.querySelector('.day-title-input').placeholder = `VD: Ngày ${dayCount} - Khám phá...`;
        
        // Add remove functionality
        dayElement.querySelector('.remove-day').addEventListener('click', function() {
            dayElement.remove();
            updateDayNumbers();
        });
        
        // Add time slot functionality
        dayElement.querySelector('.add-time-slot').addEventListener('click', function() {
            addTimeSlot(dayElement.querySelector('.time-slots'));
        });
        
        itineraryDays.appendChild(dayElement);
    }

    function addTimeSlot(container) {
        const slotClone = timeSlotTemplate.content.cloneNode(true);
        container.appendChild(slotClone);
    }

    function updateDayNumbers() {
        const days = itineraryDays.querySelectorAll('.itinerary-day');
        dayCount = 0;
        days.forEach((day, index) => {
            dayCount++;
            day.querySelector('.day-number').textContent = dayCount;
        });
    }

    // Auto-fill from destination selection
    const destinationSelect = document.getElementById('destinationSelect');
    const tourCode = document.getElementById('tourCode');
    const tourName = document.getElementById('tourName');
    const descriptionTextarea = document.getElementById('descriptionTextarea');
    const routeTextarea = document.getElementById('routeTextarea');
    const departureDate = document.getElementById('departureDate');
    const endDate = document.getElementById('endDate');
    const durationText = document.getElementById('durationText');
    const adultCount = document.getElementById('adultCount');
    const childCount = document.getElementById('childCount');
    const priceAdult = document.getElementById('priceAdult');
    const priceChild = document.getElementById('priceChild');
    const totalPrice = document.getElementById('totalPrice');
    const departureLocation = document.getElementById('departureLocation');
    const endLocation = document.getElementById('endLocation');

    destinationSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            // Update basic info
            const tourCodeValue = selectedOption.getAttribute('data-code');
            const tourNameValue = selectedOption.text.split(' [Mã:')[0];
            
            tourCode.value = tourCodeValue + '_' + generateRandomSuffix();
            tourName.value = tourNameValue;
            
            // Update prices
            const adultPrice = selectedOption.getAttribute('data-price-adult');
            const childPrice = selectedOption.getAttribute('data-price-child');
            priceAdult.value = adultPrice;
            priceChild.value = childPrice;
            
            // Update locations
            const departureLoc = selectedOption.getAttribute('data-departure-location');
            const endLoc = selectedOption.getAttribute('data-end-location');
            departureLocation.value = departureLoc;
            endLocation.value = endLoc;
            
            // Update descriptions
            descriptionTextarea.value = selectedOption.getAttribute('data-description');
            routeTextarea.value = selectedOption.getAttribute('data-route');
            
            // Update duration
            const duration = selectedOption.getAttribute('data-duration');
            const durationDays = selectedOption.getAttribute('data-duration-days');
            durationText.textContent = 'Thời lượng: ' + duration + ' (' + durationDays + ' ngày)';
            
            // Calculate total price
            calculateTotalPrice();
            
            // Update end date if departure date is set
            if (departureDate.value) {
                updateEndDate(durationDays);
            }
            
            // Auto-create itinerary days
            createItineraryDays(parseInt(durationDays));
        }
    });

    // Create itinerary days based on duration
    function createItineraryDays(duration) {
        itineraryDays.innerHTML = '';
        dayCount = 0;
        
        for (let i = 1; i <= duration; i++) {
            addNewDay();
        }
    }

    // Calculate total price
    function calculateTotalPrice() {
        const adults = parseInt(adultCount.value) || 0;
        const children = parseInt(childCount.value) || 0;
        const adultPrice = parseInt(priceAdult.value) || 0;
        const childPrice = parseInt(priceChild.value) || 0;
        
        const total = (adults * adultPrice) + (children * childPrice);
        totalPrice.textContent = total.toLocaleString('vi-VN');
    }

    // Update end date
    function updateEndDate(durationDays) {
        if (departureDate.value) {
            const startDate = new Date(departureDate.value);
            const endDateValue = new Date(startDate);
            endDateValue.setDate(startDate.getDate() + parseInt(durationDays) - 1);
            
            endDate.value = endDateValue.toISOString().split('T')[0];
        }
    }

    // Generate random suffix
    function generateRandomSuffix() {
        return Math.floor(1000 + Math.random() * 9000);
    }

    // Form submission handling
    document.getElementById('tourForm').addEventListener('submit', function(e) {
        // Validate final step before submit
        if (!validateFinalStep()) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Đang xử lý...';
        
        // Form will submit normally
        return true;
    });

    function validateFinalStep() {
        // Validate required fields
        const requiredFields = [
            { field: 'tour_code', message: 'Mã tour là bắt buộc' },
            { field: 'tour_name', message: 'Tên tour là bắt buộc' },
            { field: 'destination', message: 'Điểm đến là bắt buộc' },
            { field: 'departure_date', message: 'Ngày khởi hành là bắt buộc' },
            { field: 'featured_image', message: 'Ảnh đại diện là bắt buộc' }
        ];
        
        for (let req of requiredFields) {
            const field = document.querySelector(`[name="${req.field}"]`);
            if (!field || !field.value.trim()) {
                alert(req.message);
                if (field) field.focus();
                return false;
            }
        }
        
        // Validate featured image
        const featuredImage = document.querySelector('input[name="featured_image"]');
        if (featuredImage.files.length === 0) {
            alert('Vui lòng chọn ảnh đại diện cho tour');
            featuredImage.focus();
            return false;
        }
        
        return true;
    }

    // Event listeners
    adultCount.addEventListener('input', calculateTotalPrice);
    childCount.addEventListener('input', calculateTotalPrice);
    departureDate.addEventListener('change', function() {
        if (destinationSelect.value) {
            const selectedOption = destinationSelect.options[destinationSelect.selectedIndex];
            const durationDays = selectedOption.getAttribute('data-duration-days');
            updateEndDate(durationDays);
        }
    });

    // Initialize first step
    showStep(1);
});
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>