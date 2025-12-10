<?php
$page_title = "Bảng điều khiển HDV";
require_once 'header.php';

// --- 1. DỮ LIỆU GIẢ LẬP (MOCK DATA) ---
// Giả sử HDV đang có 1 tour đang chạy (Ongoing)
$current_tour = [
    'id' => 1,
    'code' => 'T-HN-SP-01',
    'name' => 'Hà Nội - Sapa - Fansipan (3N2Đ)',
    'status' => 'ongoing', // ongoing, upcoming, none
    'current_day' => 2,
    'total_days' => 3,
    'guest_count' => 15,
    'meeting_point' => 'Sảnh Khách sạn Bamboo Sapa',
    'leader_phone' => '0988.123.456 (Điều hành)',
];

// Nhiệm vụ CỦA HDV trong ngày hôm nay
$today_tasks = [
    ['time' => '06:30', 'task' => 'Báo thức đoàn, kiểm tra số lượng khách ăn sáng', 'status' => 'done'],
    ['time' => '07:30', 'task' => 'Điểm danh lên xe di chuyển ra ga cáp treo', 'status' => 'pending'],
    ['time' => '08:00', 'task' => 'Phát vé cáp treo & hướng dẫn quy định an toàn', 'status' => 'pending'],
    ['time' => '11:30', 'task' => 'Liên hệ nhà hàng Vân Sam đặt bàn ăn trưa', 'status' => 'pending'],
    ['time' => '19:00', 'task' => 'Tổ chức Gala Dinner mini tại khách sạn', 'status' => 'pending'],
];

// Lịch trình tóm tắt hôm nay
$today_schedule = [
    'title' => 'Ngày 2: Chinh phục đỉnh Fansipan - Bản Cát Cát',
    'morning' => 'Ăn sáng, xe đưa đoàn đi Fansipan.',
    'afternoon' => 'Thăm quan bản Cát Cát, tìm hiểu văn hóa H\'Mông.',
    'evening' => 'Ăn tối, tự do dạo chơi chợ đêm.'
];
?>

<div class="pb-5">
    
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 py-2 border-start border-primary border-4">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-primary m-0">02</h2>
                    <small class="text-muted text-uppercase fw-bold">Tour tháng này</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100 py-2 border-start border-success border-4">
                <div class="card-body text-center">
                    <h2 class="fw-bold text-success m-0">45</h2>
                    <small class="text-muted text-uppercase fw-bold">Khách phụ trách</small>
                </div>
            </div>
        </div>
        </div>

    <h5 class="fw-bold text-gray-800 mb-3"><i class="bi bi-geo-fill text-danger me-2"></i>Tour đang phụ trách</h5>
    
    <?php if ($current_tour['status'] == 'ongoing'): ?>
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-warning text-dark mb-1">ĐANG DIỄN RA - NGÀY <?= $current_tour['current_day'] ?>/<?= $current_tour['total_days'] ?></span>
                    <h5 class="m-0 fw-bold"><?= $current_tour['name'] ?></h5>
                </div>
                <a href="?act=guide-tour-detail&id=<?= $current_tour['id'] ?>" class="btn btn-sm btn-light text-primary fw-bold shadow-sm">
                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="card-body bg-white">
            <div class="row g-4">
                <div class="col-md-5 border-end-md">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">MÃ TOUR</label>
                        <div class="fw-bold text-dark"><?= $current_tour['code'] ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">SỐ LƯỢNG KHÁCH</label>
                        <div class="fw-bold text-dark fs-5"><?= $current_tour['guest_count'] ?> khách</div>
                        <a href="?act=guide-attendance&id=<?= $current_tour['id'] ?>" class="btn btn-outline-primary btn-sm mt-1">
                            <i class="bi bi-clipboard-check"></i> Điểm danh ngay
                        </a>
                    </div>
                    <div>
                        <label class="small text-muted fw-bold">ĐIỂM TẬP TRUNG HIỆN TẠI</label>
                        <div class="alert alert-light border border-primary text-primary mb-0 d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill fs-4 me-2"></i>
                            <strong><?= $current_tour['meeting_point'] ?></strong>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active btn-sm" data-bs-toggle="pill" data-bs-target="#pills-task">
                                <i class="bi bi-list-check"></i> Nhiệm vụ hôm nay
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link btn-sm" data-bs-toggle="pill" data-bs-target="#pills-schedule">
                                <i class="bi bi-calendar-day"></i> Lịch trình ngày <?= $current_tour['current_day'] ?>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-task">
                            <div class="list-group list-group-flush">
                                <?php foreach($today_tasks as $task): ?>
                                    <label class="list-group-item d-flex gap-3 align-items-center bg-transparent px-0 py-2">
                                        <input class="form-check-input flex-shrink-0" type="checkbox" <?= $task['status']=='done' ? 'checked disabled' : '' ?>>
                                        <span class="pt-1 form-checked-content">
                                            <span class="badge bg-secondary"><?= $task['time'] ?></span>
                                            <span class="<?= $task['status']=='done' ? 'text-decoration-line-through text-muted' : 'fw-bold' ?>">
                                                <?= $task['task'] ?>
                                            </span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-schedule">
                            <h6 class="fw-bold text-primary"><?= $today_schedule['title'] ?></h6>
                            <ul class="timeline-simple list-unstyled">
                                <li class="mb-2 ms-3 position-relative">
                                    <span class="position-absolute start-0 top-0 translate-middle p-1 bg-warning border border-light rounded-circle" style="left: -1rem !important;"></span>
                                    <strong>Sáng:</strong> <?= $today_schedule['morning'] ?>
                                </li>
                                <li class="mb-2 ms-3 position-relative">
                                    <span class="position-absolute start-0 top-0 translate-middle p-1 bg-success border border-light rounded-circle" style="left: -1rem !important;"></span>
                                    <strong>Chiều:</strong> <?= $today_schedule['afternoon'] ?>
                                </li>
                                <li class="mb-2 ms-3 position-relative">
                                    <span class="position-absolute start-0 top-0 translate-middle p-1 bg-dark border border-light rounded-circle" style="left: -1rem !important;"></span>
                                    <strong>Tối:</strong> <?= $today_schedule['evening'] ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-emoji-smile display-4"></i>
            <h5 class="mt-3">Hiện tại bạn chưa có tour nào đang diễn ra.</h5>
            <p>Kiểm tra lịch trình sắp tới của bạn.</p>
            <a href="?act=guide-schedule" class="btn btn-primary">Xem lịch trình</a>
        </div>
    <?php endif; ?>

    <h5 class="fw-bold text-gray-800 mb-3 mt-4">Lịch trình sắp tới</h5>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover mb-0 align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Tên Tour</th>
                    <th>Thời gian</th>
                    <th>Địa điểm đón</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="ps-3 fw-bold">Hạ Long - Ngủ đêm du thuyền</td>
                    <td>20/11 - 21/11</td>
                    <td>Nhà hát lớn HN</td>
                    <td><span class="badge bg-info text-dark">Sắp tới</span></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<?php require_once 'footer.php'; ?>