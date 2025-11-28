<?php
$page_title = "Quản lý Tour";
require_once '../pro1014/views/admin/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
    <div>
        <h1 class="h2">Quản lý Tour</h1>
        <p class="mb-0">Tổng số: <span class="badge bg-primary"><?php echo $total_tours; ?></span> tour</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="?act=admin_tours_create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Tạo Tour mới
        </a>
    </div>
</div>

<!-- Search & Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="act" value="admin_tours">
            <div class="col-md-3">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tên tour, mã tour..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="draft" <?php echo ($_GET['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Bản nháp</option>
                    <option value="published" <?php echo ($_GET['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Đã xuất bản</option>
                    <option value="in_progress" <?php echo ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : ''; ?>>Đang tiến hành</option>
                    <option value="archived" <?php echo ($_GET['status'] ?? '') === 'archived' ? 'selected' : ''; ?>>Đã lưu trữ</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Điểm đến</label>
                <select name="destination" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="Sapa, Lào Cai" <?php echo ($_GET['destination'] ?? '') === 'Sapa, Lào Cai' ? 'selected' : ''; ?>>Sapa</option>
                    <option value="Hạ Long, Quảng Ninh" <?php echo ($_GET['destination'] ?? '') === 'Hạ Long, Quảng Ninh' ? 'selected' : ''; ?>>Hạ Long</option>
                    <option value="Đà Nẵng, Hội An" <?php echo ($_GET['destination'] ?? '') === 'Đà Nẵng, Hội An' ? 'selected' : ''; ?>>Đà Nẵng - Hội An</option>
                    <option value="Phú Quốc, Kiên Giang" <?php echo ($_GET['destination'] ?? '') === 'Phú Quốc, Kiên Giang' ? 'selected' : ''; ?>>Phú Quốc</option>
                    <option value="Nha Trang, Khánh Hòa" <?php echo ($_GET['destination'] ?? '') === 'Nha Trang, Khánh Hòa' ? 'selected' : ''; ?>>Nha Trang</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sắp xếp</label>
                <select name="sort" class="form-select">
                    <option value="newest">Mới nhất</option>
                    <option value="oldest" <?php echo ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : ''; ?>>Cũ nhất</option>
                    <option value="name_asc" <?php echo ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : ''; ?>>Tên A-Z</option>
                    <option value="name_desc" <?php echo ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : ''; ?>>Tên Z-A</option>
                    <option value="price_asc" <?php echo ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : ''; ?>>Giá thấp nhất</option>
                    <option value="price_desc" <?php echo ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : ''; ?>>Giá cao nhất</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Tìm kiếm
                </button>
                <a href="?act=admin_tours" class="btn btn-outline-secondary w-100 mt-2">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Stats Overview -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?php echo $stats['total_tours'] ?? 0; ?></h4>
                        <small>Tổng tour</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-globe fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?php echo $stats['draft_tours'] ?? 0; ?></h4>
                        <small>Bản nháp</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-pencil fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?php echo $stats['published_tours'] ?? 0; ?></h4>
                        <small>Đã xuất bản</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?php echo $stats['in_progress_tours'] ?? 0; ?></h4>
                        <small>Đang tiến hành</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-play-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tours Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (count($tours) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0" width="120">Mã Tour</th>
                            <th class="border-0">Thông tin Tour</th>
                            <th class="border-0" width="150">Thời gian</th>
                            <th class="border-0 text-end" width="150">Giá</th>
                            <th class="border-0 text-center" width="100">Hình ảnh</th>
                            <th class="border-0 text-center" width="100">Lịch trình</th>
                            <th class="border-0 text-center" width="120">Trạng thái</th>
                            <th class="border-0 text-center" width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tours as $tour): 
                            // Tính tổng tiền với giá trị mặc định nếu không có
                            $adult_count = $tour['adult_count'] ?? 2;
                            $child_count = $tour['child_count'] ?? 0;
                            $price_adult = $tour['price_adult'] ?? 0;
                            $price_child = $tour['price_child'] ?? 0;
                            $total_amount = ($adult_count * $price_adult) + ($child_count * $price_child);
                            
                            // Tính số ngày từ duration_days
                            $duration_days = $tour['duration_days'] ?? 0;
                            $date_range = $duration_days . ' ngày';

                            // Check if has images and itinerary
                            $has_images = !empty($tour['featured_image']);
                            $has_itinerary = !empty($tour['itinerary_count']) && $tour['itinerary_count'] > 0;

                            // Kiểm tra xem tour có đang chạy không (dựa trên departure_schedules)
                            $is_tour_running = false;
                            $has_departures = !empty($tour['departure_count']) && $tour['departure_count'] > 0;
                            
                            // Nếu tour có lịch khởi hành và status là 'in_progress' thì không thể xóa
                            $can_delete = ($tour['status'] !== 'in_progress' && !$has_departures);
                        ?>
                        <tr>
                            <td>
                                <a href="javascript:void(0)" class="fw-bold text-primary text-decoration-none" 
                                   data-bs-toggle="modal" data-bs-target="#tourDetailModal" 
                                   onclick="showTourDetail(<?php echo htmlspecialchars(json_encode($tour, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)); ?>)">
                                    <?php echo $tour['tour_code']; ?>
                                </a>
                                <br>
                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($tour['created_at'])); ?></small>
                            </td>
                            <td>
                                <div class="d-flex align-items-start">
                                    <?php if ($has_images): ?>
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-light rounded tour-thumbnail" 
                                             style="background-image: url('<?php echo htmlspecialchars($tour['featured_image']); ?>');"
                                             data-bs-toggle="modal" data-bs-target="#imageGalleryModal"
                                             onclick="showImageGallery(<?php echo $tour['tour_id']; ?>, '<?php echo htmlspecialchars($tour['tour_name']); ?>')">
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($tour['tour_name']); ?>
                                        </h6>
                                        <div class="text-muted small">
                                            <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($tour['destination']); ?>
                                            <br>
                                            <i class="bi bi-people me-1"></i>SL tối đa: <?php echo $tour['max_participants'] ?? 'N/A'; ?>
                                            <br>
                                            <i class="bi bi-signpost me-1"></i><?php echo htmlspecialchars($tour['difficulty'] ?? 'medium'); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <div class="fw-bold text-primary"><?php echo $duration_days; ?>N<?php echo $duration_days > 1 ? $duration_days - 1 : ''; ?>Đ</div>
                                    <small class="text-muted"><?php echo $date_range; ?></small>
                                    <?php if ($tour['status'] === 'in_progress'): ?>
                                    <br>
                                    <span class="badge bg-danger badge-sm">Đang tiến hành</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-success"><?php echo number_format($price_adult); ?>₫</div>
                                <small class="text-muted">
                                    Người lớn<br>
                                    <?php if ($price_child > 0): ?>
                                    <?php echo number_format($price_child); ?>₫ trẻ em
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <?php if ($has_images): ?>
                                <button type="button" class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" data-bs-target="#imageGalleryModal"
                                        onclick="showImageGallery(<?php echo $tour['tour_id']; ?>, '<?php echo htmlspecialchars($tour['tour_name']); ?>')"
                                        data-bs-toggle="tooltip" title="Xem hình ảnh tour">
                                    <i class="bi bi-image"></i>
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                        data-bs-toggle="tooltip" title="Chưa có hình ảnh">
                                    <i class="bi bi-image"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($has_itinerary): ?>
                                <button type="button" class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" data-bs-target="#itineraryModal"
                                        onclick="showItinerary(<?php echo $tour['tour_id']; ?>, '<?php echo htmlspecialchars($tour['tour_name']); ?>')"
                                        data-bs-toggle="tooltip" title="Xem lịch trình chi tiết">
                                    <i class="bi bi-calendar-check"></i>
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                        data-bs-toggle="tooltip" title="Chưa có lịch trình">
                                    <i class="bi bi-calendar"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                $status_config = [
                                    'draft' => [
                                        'class' => 'bg-warning', 
                                        'text' => 'Bản nháp', 
                                        'icon' => 'bi-pencil',
                                        'description' => 'Tour đang được soạn thảo, có thể chỉnh sửa và xóa'
                                    ],
                                    'published' => [
                                        'class' => 'bg-success', 
                                        'text' => 'Đã xuất bản', 
                                        'icon' => 'bi-check-circle',
                                        'description' => 'Tour đã hoàn thành tất cả thông tin và sẵn sàng'
                                    ],
                                    'in_progress' => [
                                        'class' => 'bg-info', 
                                        'text' => 'Đang tiến hành', 
                                        'icon' => 'bi-play-circle',
                                        'description' => 'Tour đang trong quá trình thực hiện, không thể xóa'
                                    ],
                                    'archived' => [
                                        'class' => 'bg-secondary', 
                                        'text' => 'Đã lưu trữ', 
                                        'icon' => 'bi-archive',
                                        'description' => 'Tour đã được lưu trữ'
                                    ]
                                ];
                                $status = $status_config[$tour['status']] ?? ['class' => 'bg-secondary', 'text' => $tour['status'], 'icon' => 'bi-question', 'description' => ''];
                                ?>
                                <span class="badge <?php echo $status['class']; ?>" 
                                      data-bs-toggle="tooltip" 
                                      title="<?php echo $status['description']; ?>">
                                    <i class="<?php echo $status['icon']; ?> me-1"></i><?php echo $status['text']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="?act=admin_tours_edit&id=<?php echo $tour['tour_id']; ?>" 
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Sửa tour">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-info"
                                            data-bs-toggle="modal" data-bs-target="#tourDetailModal" 
                                            onclick="showTourDetail(<?php echo htmlspecialchars(json_encode($tour, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)); ?>)"
                                            data-bs-toggle="tooltip" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if ($can_delete): ?>
                                    <a href="?act=admin_tours_delete&id=<?php echo $tour['tour_id']; ?>" 
                                       class="btn btn-outline-danger"
                                       data-bs-toggle="tooltip" title="Xóa tour"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa tour <?php echo htmlspecialchars($tour['tour_name']); ?>?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-outline-secondary"
                                            data-bs-toggle="tooltip" 
                                            title="Tour đang tiến hành hoặc có lịch khởi hành không thể xóa"
                                            disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                </div>
                <h5 class="text-muted">Không có tour nào</h5>
                <p class="text-muted mb-4">Hãy tạo tour đầu tiên để bắt đầu</p>
                <a href="?act=admin_tours_create" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Tạo tour đầu tiên
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if (count($tours) > 0 && ($total_pages ?? 1) > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php echo ($current_page ?? 1) <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="?act=admin_tours&page=<?php echo ($current_page ?? 1) - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?><?php echo isset($_GET['destination']) ? '&destination=' . $_GET['destination'] : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>">Trước</a>
        </li>
        
        <?php for ($i = 1; $i <= ($total_pages ?? 1); $i++): ?>
        <li class="page-item <?php echo ($current_page ?? 1) == $i ? 'active' : ''; ?>">
            <a class="page-link" href="?act=admin_tours&page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?><?php echo isset($_GET['destination']) ? '&destination=' . $_GET['destination'] : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>"><?php echo $i; ?></a>
        </li>
        <?php endfor; ?>
        
        <li class="page-item <?php echo ($current_page ?? 1) >= ($total_pages ?? 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?act=admin_tours&page=<?php echo ($current_page ?? 1) + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . $_GET['status'] : ''; ?><?php echo isset($_GET['destination']) ? '&destination=' . $_GET['destination'] : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>">Sau</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once '../pro1014/views/admin/footer.php'; ?>
