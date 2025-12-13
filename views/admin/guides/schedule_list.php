<?php 
// Đặt tiêu đề cho header hiển thị
$page_title = "Lịch Trình Tour";
require_once 'header.php'; 

// --- DỮ LIỆU GIẢ LẬP (PHÒNG KHI CONTROLLER CHƯA TRUYỀN DỮ LIỆU) ---
// Bạn có thể xóa đoạn này nếu Controller đã truyền biến $schedules
if (!isset($schedules) || empty($schedules)) {
    $schedules = [
        [
            'departure_id' => 999,
            'tour_id' => 1,
            'tour_name' => 'Hà Nội - Sapa - Fansipan (3N2Đ)',
            'tour_code' => 'T-HN-SP-01',
            'start_date' => date('Y-m-d'), // Hôm nay
            'end_date' => date('Y-m-d', strtotime('+2 days')),
            'meeting_point' => 'Nhà Hát Lớn Hà Nội',
            'guest_count' => 15,
            'status' => 'ongoing' // upcoming, ongoing, completed, cancelled
        ],
        [
            'departure_id' => 1000,
            'tour_id' => 2,
            'tour_name' => 'Hạ Long - Ngủ Đêm Du Thuyền (2N1Đ)',
            'tour_code' => 'T-HL-05',
            'start_date' => date('Y-m-d', strtotime('+5 days')),
            'end_date' => date('Y-m-d', strtotime('+6 days')),
            'meeting_point' => 'Cảng Tuần Châu',
            'guest_count' => 20,
            'status' => 'upcoming'
        ],
        [
            'departure_id' => 980,
            'tour_id' => 3,
            'tour_name' => 'Ninh Bình - Tràng An - Bái Đính',
            'tour_code' => 'T-NB-02',
            'start_date' => date('Y-m-d', strtotime('-5 days')),
            'end_date' => date('Y-m-d', strtotime('-4 days')),
            'meeting_point' => 'Cổng Công Viên Thống Nhất',
            'guest_count' => 10,
            'status' => 'completed'
        ]
    ];
}
?>

<div class="pb-5">
    
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Tìm tên tour hoặc mã tour...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="upcoming">Sắp diễn ra</option>
                        <option value="ongoing">Đang diễn ra</option>
                        <option value="completed">Đã hoàn thành</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control">
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary fw-bold"><i class="bi bi-funnel"></i> Lọc</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i>Danh sách tour được phân công</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase small">
                    <tr>
                        <th width="30%" class="ps-4">Thông tin Tour</th>
                        <th width="20%">Thời gian</th>
                        <th width="15%">Điểm đón</th>
                        <th width="10%">Khách</th>
                        <th width="10%">Trạng thái</th>
                        <th width="15%" class="text-center">Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($schedules)): ?>
                        <?php foreach($schedules as $item): 
                            // Xử lý hiển thị trạng thái
                            $stClass = 'secondary'; $stText = 'Không rõ';
                            if($item['status'] == 'upcoming') { $stClass = 'info'; $stText = 'Sắp tới'; }
                            if($item['status'] == 'ongoing') { $stClass = 'success'; $stText = 'Đang chạy'; }
                            if($item['status'] == 'completed') { $stClass = 'secondary'; $stText = 'Hoàn tất'; }
                            if($item['status'] == 'cancelled') { $stClass = 'danger'; $stText = 'Đã hủy'; }
                        ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($item['tour_name']) ?></div>
                                <span class="badge bg-light text-primary border"><?= $item['tour_code'] ?></span>
                            </td>

                            <td>
                                <div class="d-flex flex-column small">
                                    <span><i class="bi bi-arrow-right-circle text-success me-1"></i> <?= date('d/m/Y', strtotime($item['start_date'])) ?></span>
                                    <span class="mt-1"><i class="bi bi-arrow-left-circle text-danger me-1"></i> <?= date('d/m/Y', strtotime($item['end_date'])) ?></span>
                                </div>
                            </td>

                            <td>
                                <small class="text-muted"><i class="bi bi-geo-alt-fill text-danger"></i> <?= htmlspecialchars($item['meeting_point']) ?></small>
                            </td>

                            <td>
                                <span class="fw-bold"><i class="bi bi-people-fill text-secondary"></i> <?= $item['guest_count'] ?></span>
                            </td>

                            <td>
                                <span class="badge bg-<?= $stClass ?>-subtle text-<?= $stClass ?> border border-<?= $stClass ?>">
                                    <?= $stText ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Thao tác
                                    </button>
                                    <ul class="dropdown-menu shadow">
                                        <li>
                                            <a class="dropdown-item" href="?act=guide-tour-detail&id=<?= $item['tour_id'] ?>">
                                                <i class="bi bi-info-circle me-2 text-info"></i> Xem chi tiết
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="?act=guide-attendance&id=<?= $item['departure_id'] ?>">
                                                <i class="bi bi-clipboard-check me-2 text-success"></i> Điểm danh
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="?act=guide-guest-list&id=<?= $item['departure_id'] ?>">
                                                <i class="bi bi-people me-2 text-primary"></i> Danh sách khách
                                            </a>
                                        </li>
                                        <?php if($item['status'] == 'completed'): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="?act=guide-report&id=<?= $item['departure_id'] ?>">
                                                <i class="bi bi-file-earmark-text me-2"></i> Xem báo cáo
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-calendar-x display-4"></i>
                                    <p class="mt-3">Bạn chưa có lịch trình nào được phân công.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white d-flex justify-content-end py-3">
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">Trước</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Sau</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?php require_once './views/admin/guides/header.php'; ?>

<div class="container-fluid guide-content">
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-calendar-check me-2"></i>Lịch Trình Của Bạn
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 ps-4">Thông tin Tour</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($myTours)): ?>
                            <?php
                            // Biến cờ để xác định tour tương lai gần nhất
                            $found_nearest = false;
                            $today = new DateTime();
                            $today->setTime(0, 0, 0); // Reset giờ về 0 để so sánh ngày chuẩn
                            ?>

                            <?php foreach ($myTours as $tour): ?>
                                <?php
                                $tourDate = new DateTime($tour['departure_date']);
                                $tourDate->setTime(0, 0, 0);

                                // Tính khoảng cách ngày
                                $interval = $today->diff($tourDate);
                                $days_left = $interval->days;
                                $is_past = $interval->invert; // 1 là quá khứ, 0 là tương lai

                                // --- LOGIC HIỂN THỊ TRẠNG THÁI ---
                                $stt_text = '';
                                $stt_class = '';
                                $row_opacity = '1';

                                if ($is_past) {
                                    // 1. QUÁ KHỨ -> ĐẨY XUỐNG DƯỚI (Đã xử lý ở SQL), LÀM MỜ
                                    $stt_text = 'Đã hoàn thành';
                                    $stt_class = 'bg-secondary bg-opacity-75';
                                    $row_opacity = '0.5'; // Mờ đi
                                } elseif ($days_left == 0) {
                                    // 2. HÔM NAY
                                    $stt_text = 'Đang diễn ra';
                                    $stt_class = 'bg-success'; // Màu xanh lá
                                    $found_nearest = true; // Đánh dấu đã tìm thấy tour gần nhất
                                } else {
                                    // 3. TƯƠNG LAI
                                    if (!$found_nearest) {
                                        // Đây là tour tương lai ĐẦU TIÊN tìm thấy -> Gần nhất
                                        $stt_text = 'Sắp khởi hành';
                                        $stt_class = 'bg-primary'; // Màu xanh dương đậm
                                        $found_nearest = true; // Đánh dấu xong, các tour sau sẽ nhảy vào else
                                    } else {
                                        // Các tour tương lai CÒN LẠI (Xa hơn)
                                        $stt_text = 'Còn ' . $days_left . ' ngày';
                                        $stt_class = 'bg-info text-dark'; // Màu xanh nhạt
                                    }
                                }
                                ?>

                                <tr style="opacity: <?= $row_opacity ?>;">
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($tour['tour_name']) ?></div>
                                        <div class="d-flex align-items-center mt-1">
                                            <span class="badge bg-light text-secondary border me-2">
                                                <?= htmlspecialchars($tour['tour_code']) ?>
                                            </span>
                                            <small class="text-danger">
                                                <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($tour['meeting_point']) ?>
                                            </small>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-bold fs-6">
                                            <?= date('d/m/Y', strtotime($tour['departure_date'])) ?>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> <?= date('H:i', strtotime($tour['departure_time'])) ?>
                                        </small>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge <?= $stt_class ?> rounded-pill px-3 py-2 border">
                                            <?= $stt_text ?>
                                        </span>
                                    </td>

                                    <td class="text-end pe-4">
                                        <?php if (!$is_past): ?>
                                            <div class="btn-group">
                                                <a href="?act=guide-attendance-check&id=<?= $tour['departure_id'] ?>"
                                                    class="btn btn-outline-primary btn-sm" title="Điểm danh" data-bs-toggle="tooltip">
                                                    <i class="bi bi-clipboard-check"></i>
                                                </a>
                                                <a href="?act=guide-incident-report&departure_id=<?= $tour['departure_id'] ?>"
                                                    class="btn btn-outline-danger btn-sm" title="Báo cáo" data-bs-toggle="tooltip">
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <a href="?act=guide-attendance-check&id=<?= $tour['departure_id'] ?>"
                                                class="btn btn-light btn-sm border text-muted" title="Xem lại lịch sử">
                                                <i class="bi bi-eye"></i> Xem lại
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">Chưa có lịch trình nào được phân công.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Kích hoạt tooltip bootstrap (để hiện chữ khi rê chuột vào nút)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>

<?php require_once './views/admin/guides/footer.php'; ?>

<?php require_once 'footer.php'; ?>

