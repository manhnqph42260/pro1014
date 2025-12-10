<?php require_once './views/admin/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Chi tiết Hướng dẫn viên</h1>
    
    <div class="row">
        <!-- Left Column: Profile -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Hồ sơ HDV</h6>
                    <div>
                        <a href="?act=admin_guides_edit&id=<?= $guide['guide_id'] ?>" 
                           class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                    </div>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($guide['avatar_url'])): ?>
                        <img src="<?= BASE_URL . '/' . $guide['avatar_url'] ?>" 
                             class="img-fluid rounded-circle mb-3" 
                             style="width: 150px; height: 150px; object-fit: cover;" 
                             alt="Avatar">
                    <?php else: ?>
                        <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 150px; height: 150px; font-size: 48px;">
                            <?= substr($guide['full_name'], 0, 1) ?>
                        </div>
                    <?php endif; ?>
                    
                    <h4><?= htmlspecialchars($guide['full_name']) ?></h4>
                    <p class="text-muted"><?= $guide['guide_code'] ?></p>
                    
                    <div class="mb-3">
                        <?php
                        $status_badges = [
                            'active' => 'success',
                            'inactive' => 'secondary',
                            'on_leave' => 'warning'
                        ];
                        ?>
                        <span class="badge badge-<?= $status_badges[$guide['status']] ?? 'secondary' ?>">
                            <?= $guide['status'] == 'active' ? 'Đang hoạt động' : 
                               ($guide['status'] == 'inactive' ? 'Không hoạt động' : 'Nghỉ phép') ?>
                        </span>
                    </div>
                    
                    <div class="rating mb-3">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="fa fa-star <?= $i <= $guide['rating'] ? 'text-warning' : 'text-muted' ?>"></span>
                        <?php endfor; ?>
                        <small class="ml-1">(<?= number_format($guide['rating'], 1) ?>)</small>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
    <li class="list-group-item">
        <strong><i class="fas fa-envelope mr-2"></i>Email:</strong>
        <?= !empty($guide['email']) ? htmlspecialchars($guide['email']) : 'N/A' ?>
    </li>
    <li class="list-group-item">
        <strong><i class="fas fa-phone mr-2"></i>Điện thoại:</strong>
        <?= !empty($guide['phone']) ? htmlspecialchars($guide['phone']) : 'N/A' ?>
    </li>
    <li class="list-group-item">
        <strong><i class="fas fa-id-card mr-2"></i>CMND/CCCD:</strong>
        <?= !empty($guide['id_number']) ? htmlspecialchars($guide['id_number']) : 'N/A' ?>
    </li>
    <li class="list-group-item">
        <strong><i class="fas fa-birthday-cake mr-2"></i>Ngày sinh:</strong>
        <?= !empty($guide['date_of_birth']) ? date('d/m/Y', strtotime($guide['date_of_birth'])) : 'N/A' ?>
    </li>
    <li class="list-group-item">
        <strong><i class="fas fa-history mr-2"></i>Kinh nghiệm:</strong>
        <?= !empty($guide['experience_years']) ? $guide['experience_years'] . ' năm' : 'N/A' ?>
    </li>
</ul>
            </div>
            
            <!-- Contact Information -->
            <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Thông tin liên hệ</h6>
    </div>
    <div class="card-body">
        <p><strong>Địa chỉ:</strong><br>
        <?= !empty($guide['address']) ? nl2br(htmlspecialchars($guide['address'])) : 'Chưa cập nhật' ?></p>
        
        <p><strong>Liên hệ khẩn cấp:</strong><br>
        <?= !empty($guide['emergency_contact']) ? htmlspecialchars($guide['emergency_contact']) : 'Chưa cập nhật' ?></p>
    </div>
</div>
        </div>
        
        <!-- Right Column: Details & History -->
        <div class="col-lg-8">
            <!-- Languages & Skills -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Ngôn ngữ</h6>
                <span class="badge badge-info"><?= count($guide['languages_array'] ?? []) ?></span>
            </div>
            <div class="card-body">
                <?php if (!empty($guide['languages_array']) && is_array($guide['languages_array'])): 
                    foreach ($guide['languages_array'] as $language): 
                        if (!empty(trim($language))): ?>
                            <span class="badge badge-info mr-1 mb-1 p-2">
                                <i class="fas fa-language mr-1"></i>
                                <?= htmlspecialchars(trim($language)) ?>
                            </span>
                        <?php endif;
                    endforeach;
                else: ?>
                    <div class="alert alert-warning p-2 mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        Chưa cập nhật ngôn ngữ
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Kỹ năng</h6>
                <span class="badge badge-success"><?= count($guide['skills_array'] ?? []) ?></span>
            </div>
            <div class="card-body">
                <?php if (!empty($guide['skills_array']) && is_array($guide['skills_array'])): 
                    // Map skill key to display name
                    $skill_names = [
                        'first_aid' => '🩹 Sơ cứu',
                        'photography' => '📸 Chụp ảnh',
                        'cooking' => '👨‍🍳 Nấu ăn',
                        'history' => '📜 Lịch sử',
                        'storytelling' => '📖 Kể chuyện',
                        'team_management' => '👥 Quản lý nhóm'
                    ];
                    
                    foreach ($guide['skills_array'] as $skill): 
                        if (!empty(trim($skill))): 
                            $display_name = $skill_names[$skill] ?? $skill; ?>
                            <span class="badge badge-success mr-1 mb-1 p-2">
                                <i class="fas fa-star mr-1"></i>
                                <?= htmlspecialchars($display_name) ?>
                            </span>
                        <?php endif;
                    endforeach;
                else: ?>
                    <div class="alert alert-warning p-2 mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        Chưa cập nhật kỹ năng
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
            
            <!-- Certifications -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Chứng chỉ</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($guide['certifications']) && is_array($guide['certifications'])): ?>
                        <ul class="list-unstyled">
                            <?php foreach ($guide['certifications'] as $cert): ?>
                                <li class="mb-2">
                                    <i class="fas fa-certificate text-warning mr-2"></i>
                                    <?= htmlspecialchars($cert) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Chưa có chứng chỉ nào</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Assignment History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử phân công tour</h6>
                    <span class="badge badge-primary"><?= count($assignments) ?> tour</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tour</th>
                                    <th>Ngày khởi hành</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày phân công</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($assignment['tour_name']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($assignment['departure_date'])) ?></td>
                                    <td>
                                        <span class="badge <?= $assignment['assignment_type'] == 'main_guide' ? 'badge-primary' : 'badge-secondary' ?>">
                                            <?= $assignment['assignment_type'] == 'main_guide' ? 'HDV chính' : 'HDV phụ' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $status_colors = [
                                            'assigned' => 'warning',
                                            'confirmed' => 'info',
                                            'completed' => 'success'
                                        ];
                                        ?>
                                        <span class="badge badge-<?= $status_colors[$assignment['status']] ?? 'secondary' ?>">
                                            <?= $assignment['status'] == 'assigned' ? 'Đã phân công' : 
                                               ($assignment['status'] == 'confirmed' ? 'Đã xác nhận' : 'Hoàn thành') ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($assignment['assigned_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($assignments)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Chưa có phân công nào</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Incident Reports -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Báo cáo sự cố</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($incidents)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tour</th>
                                        <th>Ngày sự cố</th>
                                        <th>Loại sự cố</th>
                                        <th>Mức độ</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($incidents as $incident): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($incident['tour_name']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($incident['incident_date'])) ?></td>
                                        <td><?= $incident['incident_type'] ?></td>
                                        <td>
                                            <?php
                                            $severity_colors = [
                                                'low' => 'success',
                                                'medium' => 'warning',
                                                'high' => 'danger',
                                                'critical' => 'dark'
                                            ];
                                            ?>
                                            <span class="badge badge-<?= $severity_colors[$incident['severity_level']] ?? 'secondary' ?>">
                                                <?= $incident['severity_level'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $resolution_colors = [
                                                'pending' => 'warning',
                                                'resolved' => 'success',
                                                'escalated' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge badge-<?= $resolution_colors[$incident['resolution_status']] ?? 'secondary' ?>">
                                                <?= $incident['resolution_status'] == 'pending' ? 'Đang xử lý' : 
                                                   ($incident['resolution_status'] == 'resolved' ? 'Đã giải quyết' : 'Đã chuyển') ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Không có báo cáo sự cố nào</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once './views/admin/footer.php'; ?>