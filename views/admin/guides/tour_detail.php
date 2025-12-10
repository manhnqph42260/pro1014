<?php
$page_title = "Chi tiết Tour";
$breadcrumb = [
    ['title' => 'Lịch làm việc', 'link' => 'schedule_list.php'],
    ['title' => 'Chi tiết Tour', 'active' => true]
];
require_once __DIR__ . '/header.php';

// Giả lập dữ liệu tour (trong thực tế lấy từ database)
$tour = [
    'tour_id' => 1,
    'tour_code' => 'T001',
    'tour_name' => 'Tour Sapa 3 ngày 2 đêm',
    'destination' => 'Sapa, Lào Cai',
    'duration_days' => 3,
    'departure_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+3 days')),
    'meeting_point' => 'Khách sạn ABC, 123 Đường XYZ, Hà Nội',
    'meeting_time' => '06:00',
    'total_participants' => 18,
    'checked_in' => 15,
    'status' => 'in_progress',
    'description' => 'Khám phá vùng đất Sapa với những thửa ruộng bậc thang tuyệt đẹp, trải nghiệm văn hóa dân tộc thiểu số và thưởng thức ẩm thực địa phương.',
    'difficulty' => 'medium',
    'price_adult' => 2500000,
    'price_child' => 1800000,
    'created_at' => '2024-02-15',
    'itinerary' => [
        [
            'day' => 1,
            'title' => 'Khởi hành Hà Nội - Sapa',
            'description' => 'Di chuyển từ Hà Nội lên Sapa',
            'activities' => 'Đón khách, Di chuyển bằng xe, Ăn trưa, Tham quan bản Cát Cát',
            'meals' => 'Trưa, Tối',
            'accommodation' => 'Khách sạn 3 sao tại Sapa',
            'guide_notes' => 'Chuẩn bị sẵn bảng tên, kiểm tra sức khỏe khách trước khi khởi hành'
        ],
        [
            'day' => 2,
            'title' => 'Khám phá Fansipan',
            'description' => 'Chinh phục nóc nhà Đông Dương',
            'activities' => 'Cáp treo Fansipan, Tham quan chùa Trình, Đi bộ tham quan',
            'meals' => 'Sáng, Trưa, Tối',
            'accommodation' => 'Khách sạn 3 sao tại Sapa',
            'guide_notes' => 'Chú ý an toàn khi đi cáp treo, mang theo áo ấm'
        ],
        [
            'day' => 3,
            'title' => 'Tham quan và trở về Hà Nội',
            'description' => 'Tham quan chợ Sapa và trở về Hà Nội',
            'activities' => 'Tham quan chợ Sapa, Mua sắm, Ăn trưa, Di chuyển về Hà Nội',
            'meals' => 'Sáng, Trưa',
            'accommodation' => 'Không',
            'guide_notes' => 'Nhắc khách check-out, thu hồi phòng, kiểm tra hành lý'
        ]
    ],
    'services' => [
        ['name' => 'Xe 45 chỗ', 'provider' => 'Công ty ABC', 'contact' => '0912345678'],
        ['name' => 'Khách sạn 3 sao', 'provider' => 'Khách sạn XYZ', 'contact' => '0987654321'],
        ['name' => 'Vé cáp treo', 'provider' => 'Sun World', 'contact' => '0901234567'],
    ],
    'policies' => [
        ['type' => 'cancellation', 'title' => 'Chính sách hủy tour', 'content' => 'Hủy trước 7 ngày: hoàn 100%, trước 3 ngày: 50%, sau đó: không hoàn'],
        ['type' => 'health', 'title' => 'Lưu ý sức khỏe', 'content' => 'Khách có vấn đề sức khỏe cần thông báo trước, mang theo thuốc cá nhân'],
    ],
    'guide_assignment' => [
        'main_guide' => 'Nguyễn Văn A (Bạn)',
        'assistant_guide' => 'Trần Thị B',
        'assigned_by' => 'Admin',
        'assigned_date' => '2024-02-15',
        'notes' => 'HDV chính phụ trách toàn bộ tour, HDV phụ hỗ trợ điểm danh và chụp ảnh'
    ]
];
?>

<!-- Tour Header -->
<div class="card guide-card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4><?php echo $tour['tour_name']; ?></h4>
                <div class="d-flex align-items-center gap-3 mt-2">
                    <span class="badge bg-primary"><?php echo $tour['tour_code']; ?></span>
                    <span class="badge bg-success">Đang tiến hành</span>
                    <span class="badge bg-info"><?php echo $tour['difficulty'] == 'easy' ? 'Dễ' : ($tour['difficulty'] == 'medium' ? 'Trung bình' : 'Khó'); ?></span>
                </div>
            </div>
            <div class="btn-group">
                <a href="tour_participants.php?tour_id=<?php echo $tour['tour_id']; ?>" 
                   class="btn btn-outline-primary">
                    <i class="bi bi-people me-1"></i>Danh sách khách
                </a>
                <a href="attendance.php?tour_id=<?php echo $tour['tour_id']; ?>" 
                   class="btn btn-success">
                    <i class="bi bi-clipboard-check me-1"></i>Điểm danh
                </a>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <td width="140"><strong><i class="bi bi-geo-alt"></i> Điểm đến:</strong></td>
                        <td><?php echo $tour['destination']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><i class="bi bi-calendar"></i> Thời gian:</strong></td>
                        <td><?php echo $tour['duration_days']; ?> ngày (<?php echo date('d/m', strtotime($tour['departure_date'])); ?> - <?php echo date('d/m', strtotime($tour['end_date'])); ?>)</td>
                    </tr>
                    <tr>
                        <td><strong><i class="bi bi-clock"></i> Giờ tập trung:</strong></td>
                        <td><?php echo $tour['meeting_time']; ?> tại <?php echo $tour['meeting_point']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><i class="bi bi-people"></i> Số khách:</strong></td>
                        <td><?php echo $tour['checked_in']; ?>/<?php echo $tour['total_participants']; ?> đã check-in</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <td width="140"><strong><i class="bi bi-person-badge"></i> HDV chính:</strong></td>
                        <td><?php echo $tour['guide_assignment']['main_guide']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><i class="bi bi-person-plus"></i> HDV phụ:</strong></td>
                        <td><?php echo $tour['guide_assignment']['assistant_guide']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><i class="bi bi-cash"></i> Giá người lớn:</strong></td>
                        <td class="text-success fw-bold"><?php echo number_format($tour['price_adult']); ?>₫</td>
                    </tr>
                    <tr>
                        <td><strong><i class="bi bi-cash"></i> Giá trẻ em:</strong></td>
                        <td class="text-success"><?php echo number_format($tour['price_child']); ?>₫</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <?php if (!empty($tour['description'])): ?>
        <div class="mt-3">
            <h6><i class="bi bi-card-text me-2"></i>Mô tả tour</h6>
            <p class="text-muted"><?php echo $tour['description']; ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tab Navigation -->
<ul class="nav nav-tabs mb-4" id="tourDetailTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="itinerary-tab" data-bs-toggle="tab" data-bs-target="#itinerary" type="button">
            <i class="bi bi-calendar-week me-1"></i>Lịch trình chi tiết
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button">
            <i class="bi bi-truck me-1"></i>Dịch vụ
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="policies-tab" data-bs-toggle="tab" data-bs-target="#policies" type="button">
            <i class="bi bi-shield-check me-1"></i>Chính sách
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="guide-tab" data-bs-toggle="tab" data-bs-target="#guide" type="button">
            <i class="bi bi-person-video3 me-1"></i>Phân công HDV
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="tourDetailTabContent">
    
    <!-- Itinerary Tab -->
    <div class="tab-pane fade show active" id="itinerary" role="tabpanel">
        <div class="card guide-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-signpost-split me-2"></i>Lịch trình theo ngày</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="printItinerary()">
                    <i class="bi bi-printer me-1"></i>In lịch trình
                </button>
            </div>
            <div class="card-body">
                <?php foreach ($tour['itinerary'] as $day): ?>
                <div class="card border mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Ngày <?php echo $day['day']; ?>: <?php echo $day['title']; ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6><i class="bi bi-info-circle me-2"></i>Mô tả</h6>
                                <p><?php echo $day['description']; ?></p>
                                
                                <h6 class="mt-3"><i class="bi bi-activity me-2"></i>Hoạt động</h6>
                                <p><?php echo $day['activities']; ?></p>
                                
                                <h6 class="mt-3"><i class="bi bi-journal-text me-2"></i>Ghi chú HDV</h6>
                                <div class="alert alert-info">
                                    <i class="bi bi-lightbulb"></i> <?php echo $day['guide_notes']; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6><i class="bi bi-info-square me-2"></i>Thông tin chi tiết</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Bữa ăn:</strong></td>
                                                <td><?php echo $day['meals']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Chỗ ở:</strong></td>
                                                <td><?php echo $day['accommodation']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Thời tiết dự báo:</strong></td>
                                                <td><span class="badge bg-info">Nắng nhẹ, 20-25°C</span></td>
                                            </tr>
                                        </table>
                                        
                                        <button class="btn btn-sm btn-outline-primary w-100 mt-2" 
                                                onclick="showDayJournal(<?php echo $day['day']; ?>)">
                                            <i class="bi bi-pencil-square me-1"></i>Ghi nhật ký ngày <?php echo $day['day']; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Services Tab -->
    <div class="tab-pane fade" id="services" role="tabpanel">
        <div class="card guide-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Dịch vụ tour</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($tour['services'] as $service): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card border h-100">
                            <div class="card-body">
                                <h6><?php echo $service['name']; ?></h6>
                                <p class="text-muted small mb-2">Nhà cung cấp: <?php echo $service['provider']; ?></p>
                                <p><i class="bi bi-telephone"></i> <?php echo $service['contact']; ?></p>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="serviceCheck<?php echo $service['name']; ?>">
                                    <label class="form-check-label" for="serviceCheck<?php echo $service['name']; ?>">
                                        Đã xác nhận
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pre-trip Checklist -->
                <div class="card border mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>Checklist trước tour</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check1">
                                    <label class="form-check-label" for="check1">Đã nhận danh sách khách</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check2">
                                    <label class="form-check-label" for="check2">Đã xác nhận xe đón</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check3">
                                    <label class="form-check-label" for="check3">Đã kiểm tra phòng khách sạn</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check4">
                                    <label class="form-check-label" for="check4">Đã chuẩn bị bảng tên</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check5">
                                    <label class="form-check-label" for="check5">Đã kiểm tra y tế cơ bản</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check6">
                                    <label class="form-check-label" for="check6">Đã xác nhận vé tham quan</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-success mt-3" onclick="saveChecklist()">
                            <i class="bi bi-check-circle me-1"></i>Lưu checklist
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Policies Tab -->
    <div class="tab-pane fade" id="policies" role="tabpanel">
        <div class="card guide-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Chính sách và quy định</h5>
            </div>
            <div class="card-body">
                <?php foreach ($tour['policies'] as $policy): ?>
                <div class="card border mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><?php echo $policy['title']; ?></h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo $policy['content']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Emergency Contacts -->
                <div class="card border border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="bi bi-telephone-forward me-2"></i>Liên hệ khẩn cấp</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Công ty du lịch:</strong></p>
                                <p class="mb-1">0243 123 4567 (24/7)</p>
                                <p class="mb-1">hotro@tour.com</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Bệnh viện gần nhất:</strong></p>
                                <p class="mb-1">Bệnh viện Đa khoa Sapa: 0214 387 1234</p>
                                <p class="mb-1">Cấp cứu: 115</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Guide Assignment Tab -->
    <div class="tab-pane fade" id="guide" role="tabpanel">
        <div class="card guide-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Phân công hướng dẫn viên</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6><i class="bi bi-person-badge me-2"></i>HDV chính</h6>
                                <div class="d-flex align-items-center mt-3">
                                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                                        <i class="bi bi-person fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Nguyễn Văn A</h5>
                                        <p class="text-muted mb-1">Mã: HDV001</p>
                                        <p class="mb-1"><i class="bi bi-telephone"></i> 0901111111</p>
                                        <span class="badge bg-info">5 năm kinh nghiệm</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6><i class="bi bi-person-plus me-2"></i>HDV phụ</h6>
                                <div class="d-flex align-items-center mt-3">
                                    <div class="bg-secondary text-white rounded-circle p-3 me-3">
                                        <i class="bi bi-person fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Trần Thị B</h5>
                                        <p class="text-muted mb-1">Mã: HDV002</p>
                                        <p class="mb-1"><i class="bi bi-telephone"></i> 0902222222</p>
                                        <span class="badge bg-warning">3 năm kinh nghiệm</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Assignment Details -->
                <div class="card border mt-4">
                    <div class="card-body">
                        <h6><i class="bi bi-journal-text me-2"></i>Thông tin phân công</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="200"><strong>Người phân công:</strong></td>
                                <td><?php echo $tour['guide_assignment']['assigned_by']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày phân công:</strong></td>
                                <td><?php echo date('d/m/Y', strtotime($tour['guide_assignment']['assigned_date'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ghi chú:</strong></td>
                                <td><?php echo $tour['guide_assignment']['notes']; ?></td>
                            </tr>
                        </table>
                        
                        <!-- Skills and Languages -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6><i class="bi bi-star me-2"></i>Kỹ năng HDV chính</h6>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-primary">Sơ cứu</span>
                                    <span class="badge bg-primary">Nhiếp ảnh</span>
                                    <span class="badge bg-primary">Tiếng Anh</span>
                                    <span class="badge bg-primary">Tổ chức team building</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="bi bi-translate me-2"></i>Ngôn ngữ</h6>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">Tiếng Việt</span>
                                    <span class="badge bg-success">Tiếng Anh</span>
                                    <span class="badge bg-success">Tiếng Pháp cơ bản</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="fixed-bottom bg-white border-top p-3 d-print-none">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div>
                <a href="schedule.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại lịch làm việc
                </a>
            </div>
            <div class="btn-group">
                <a href="journal.php?tour_id=<?php echo $tour['tour_id']; ?>" class="btn btn-info">
                    <i class="bi bi-journal-text me-1"></i>Nhật ký tour
                </a>
                <a href="attendance.php?tour_id=<?php echo $tour['tour_id']; ?>" class="btn btn-success">
                    <i class="bi bi-clipboard-check me-1"></i>Điểm danh
                </a>
                <a href="special_requests.php?tour_id=<?php echo $tour['tour_id']; ?>" class="btn btn-warning">
                    <i class="bi bi-heart me-1"></i>Yêu cầu đặc biệt
                </a>
                <button class="btn btn-primary" onclick="downloadTourDetails()">
                    <i class="bi bi-download me-1"></i>Tải chi tiết tour
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Day Journal -->
<div class="modal fade" id="dayJournalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ghi nhật ký ngày <span id="journalDay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="dayJournalForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Thời tiết</label>
                            <select class="form-select" required>
                                <option value="">Chọn thời tiết</option>
                                <option value="sunny">Nắng đẹp</option>
                                <option value="cloudy">Nhiều mây</option>
                                <option value="rain">Mưa</option>
                                <option value="storm">Mưa bão</option>
                                <option value="foggy">Sương mù</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nhiệt độ (°C)</label>
                            <input type="number" class="form-control" min="-10" max="50" step="0.1" value="25">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Điểm nhấn trong ngày</label>
                        <textarea class="form-control" rows="3" placeholder="Những hoạt động, sự kiện đáng nhớ..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hoạt động đã hoàn thành</label>
                        <textarea class="form-control" rows="3" placeholder="Liệt kê các hoạt động đã thực hiện..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sự cố gặp phải (nếu có)</label>
                        <textarea class="form-control" rows="2" placeholder="Mô tả sự cố, cách xử lý..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phản hồi của khách hàng</label>
                        <textarea class="form-control" rows="2" placeholder="Ý kiến, đánh giá của khách..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tải lên hình ảnh</label>
                        <input type="file" class="form-control" multiple accept="image/*">
                        <small class="text-muted">Có thể chọn nhiều ảnh (tối đa 10MB mỗi ảnh)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveDayJournal()">Lưu nhật ký</button>
            </div>
        </div>
    </div>
</div>

<script>
function printItinerary() {
    window.print();
}

function showDayJournal(day) {
    document.getElementById('journalDay').textContent = day;
    $('#dayJournalModal').modal('show');
}

function saveDayJournal() {
    const form = document.getElementById('dayJournalForm');
    if (form.checkValidity()) {
        alert('Đã lưu nhật ký ngày ' + document.getElementById('journalDay').textContent);
        $('#dayJournalModal').modal('hide');
    } else {
        form.reportValidity();
    }
}

function saveChecklist() {
    alert('Đã lưu checklist');
}

function downloadTourDetails() {
    const tourName = "<?php echo $tour['tour_name']; ?>";
    const fileName = tourName.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '_details.pdf';
    alert('Đang tải file: ' + fileName);
    // In real app, generate and download PDF
}

// Initialize tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const triggerTabList = [].slice.call(document.querySelectorAll('#tourDetailTab button'));
    triggerTabList.forEach(function (triggerEl) {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
    
    // Auto-refresh every 5 minutes for real-time updates
    setInterval(function() {
        if (!localStorage.getItem('guide_offline_mode')) {
            console.log('Checking for tour updates...');
            // In real app, call API to check for updates
        }
    }, 300000); // 5 minutes
});
</script>

<style>
@media print {
    .d-print-none, .fixed-bottom, .guide-sidebar, .breadcrumb, .btn-group {
        display: none !important;
    }
    .guide-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>