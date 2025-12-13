<?php
$page_title = "Nhật ký Tour";
$breadcrumb = [
    ['title' => 'Chi tiết Tour', 'link' => 'tour_detail.php'],
    ['title' => 'Nhật ký Tour', 'active' => true]
];
require_once __DIR__ . '/header.php';

// Giả lập dữ liệu nhật ký
$tour_info = [
    'tour_id' => 1,
    'tour_name' => 'Tour Sapa 3 ngày 2 đêm',
    'tour_code' => 'T001',
    'current_day' => 2,
    'total_days' => 3
];

$journals = [
    [
        'journal_id' => 1,
        'day_number' => 1,
        'journal_date' => date('Y-m-d'),
        'weather' => 'Nắng đẹp',
        'temperature' => 28,
        'humidity' => 65,
        'highlights' => 'Khởi hành đúng giờ, cả đoàn vui vẻ. Tham quan bản Cát Cát rất thú vị, khách hàng thích thú với văn hóa dân tộc.',
        'activities_completed' => 'Đón khách, di chuyển Hà Nội - Sapa, ăn trưa, tham quan bản Cát Cát, check-in khách sạn, ăn tối',
        'issues_encountered' => 'Một khách bị say xe nhẹ, đã xử lý bằng thuốc say xe và nghỉ ngơi.',
        'customer_feedback' => 'Khách hàng hài lòng với dịch vụ, đặc biệt thích thú với ẩm thực địa phương.',
        'created_at' => date('Y-m-d H:i:s'),
        'images' => [
            ['url' => 'https://via.placeholder.com/300x200/007bff/ffffff?text=Ảnh+1', 'caption' => 'Cả đoàn tại bản Cát Cát'],
            ['url' => 'https://via.placeholder.com/300x200/28a745/ffffff?text=Ảnh+2', 'caption' => 'Ăn trưa nhà hàng địa phương']
        ]
    ],
    [
        'journal_id' => 2,
        'day_number' => 2,
        'journal_date' => date('Y-m-d', strtotime('+1 day')),
        'weather' => 'Nắng nhẹ, sương mù buổi sáng',
        'temperature' => 24,
        'humidity' => 75,
        'highlights' => 'Chinh phục Fansipan thành công, cả đoàn rất phấn khích. Ngắm cảnh tuyệt đẹp trên đỉnh Fansipan.',
        'activities_completed' => 'Ăn sáng, đi cáp treo Fansipan, tham quan chùa Trình, ăn trưa, nghỉ ngơi, ăn tối',
        'issues_encountered' => 'Sương mù dày buổi sáng nên cáp treo chậm hơn dự kiến 30 phút.',
        'customer_feedback' => 'Khách hàng rất thích thú với trải nghiệm cáp treo và cảnh quan trên đỉnh Fansipan.',
        'created_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
        'images' => [
            ['url' => 'https://via.placeholder.com/300x200/ffc107/ffffff?text=Ảnh+3', 'caption' => 'Trên đỉnh Fansipan'],
            ['url' => 'https://via.placeholder.com/300x200/dc3545/ffffff?text=Ảnh+4', 'caption' => 'Cả đoàn tại chùa Trình']
        ]
    ]
];

$current_weather = [
    'condition' => 'Nắng nhẹ',
    'temperature' => 25,
    'humidity' => '70%',
    'wind' => '10 km/h'
];
?>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card guide-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4><?php echo $tour_info['tour_name']; ?></h4>
                        <p class="text-muted mb-2">Mã tour: <?php echo $tour_info['tour_code']; ?></p>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-0">Ngày <?php echo $tour_info['current_day']; ?>/<?php echo $tour_info['total_days']; ?></h5>
                        <small class="text-muted"><?php echo date('d/m/Y'); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card guide-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6><i class="bi bi-cloud-sun me-2"></i>Thời tiết hiện tại</h6>
                        <div class="display-4"><?php echo $current_weather['temperature']; ?>°C</div>
                        <small><?php echo $current_weather['condition']; ?></small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            Độ ẩm: <?php echo $current_weather['humidity']; ?><br>
                            Gió: <?php echo $current_weather['wind']; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Journal Entry -->
<div class="card guide-card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Ghi nhật ký nhanh - Ngày <?php echo $tour_info['current_day']; ?></h5>
    </div>
    <div class="card-body">
        <form id="quickJournalForm">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Thời tiết *</label>
                    <select class="form-select" required>
                        <option value="">Chọn thời tiết</option>
                        <option value="sunny">Nắng đẹp</option>
                        <option value="partly_cloudy">Nắng nhiều mây</option>
                        <option value="cloudy">Nhiều mây</option>
                        <option value="rain">Mưa</option>
                        <option value="storm">Mưa bão</option>
                        <option value="foggy">Sương mù</option>
                        <option value="cold">Lạnh</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Nhiệt độ (°C)</label>
                    <input type="number" class="form-control" value="<?php echo $current_weather['temperature']; ?>" min="-10" max="50">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Độ ẩm (%)</label>
                    <input type="number" class="form-control" value="70" min="0" max="100">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Điểm nhấn trong ngày *</label>
                    <input type="text" class="form-control" placeholder="VD: Cả đoàn chinh phục Fansipan thành công" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Hoạt động đã hoàn thành</label>
                <textarea class="form-control" rows="2" placeholder="Liệt kê các hoạt động đã thực hiện..."></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Sự cố / Vấn đề gặp phải</label>
                <textarea class="form-control" rows="2" placeholder="Mô tả sự cố, cách xử lý..."></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Phản hồi của khách hàng</label>
                <textarea class="form-control" rows="2" placeholder="Ý kiến, đánh giá của khách..."></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Tải lên hình ảnh</label>
                <div class="border rounded p-3 text-center">
                    <i class="bi bi-cloud-arrow-up display-4 text-muted"></i>
                    <p class="mt-2">Kéo thả ảnh vào đây hoặc click để chọn</p>
                    <input type="file" class="form-control d-none" id="imageUpload" multiple accept="image/*">
                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('imageUpload').click()">
                        <i class="bi bi-camera me-1"></i>Chọn ảnh
                    </button>
                    <small class="d-block text-muted mt-2">Tối đa 10 ảnh, mỗi ảnh ≤ 5MB</small>
                </div>
                <div id="imagePreview" class="mt-2"></div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                    <i class="bi bi-save me-1"></i>Lưu nháp
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Lưu nhật ký
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Previous Journal Entries -->
<div class="card guide-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Nhật ký các ngày trước</h5>
        <button class="btn btn-sm btn-outline-primary" onclick="exportJournals()">
            <i class="bi bi-download me-1"></i>Xuất tất cả
        </button>
    </div>
    <div class="card-body">
        <?php if (count($journals) > 0): ?>
            <?php foreach ($journals as $journal): ?>
            <div class="card border mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ngày <?php echo $journal['day_number']; ?> - <?php echo date('d/m/Y', strtotime($journal['journal_date'])); ?></h5>
                    <small class="text-muted"><?php echo date('H:i', strtotime($journal['created_at'])); ?></small>
                </div>
                <div class="card-body">
                    <!-- Weather Info -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <i class="bi bi-thermometer-half fs-1 text-primary"></i>
                                    <div class="mt-2">
                                        <h5 class="mb-0"><?php echo $journal['temperature']; ?>°C</h5>
                                        <small><?php echo $journal['weather']; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="bi bi-stars me-2"></i>Điểm nhấn</h6>
                                    <p><?php echo $journal['highlights']; ?></p>
                                    
                                    <h6 class="mt-3"><i class="bi bi-list-task me-2"></i>Hoạt động hoàn thành</h6>
                                    <p><?php echo $journal['activities_completed']; ?></p>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6><i class="bi bi-exclamation-triangle me-2"></i>Sự cố gặp phải</h6>
                                    <p><?php echo $journal['issues_encountered'] ?: 'Không có sự cố'; ?></p>
                                    
                                    <h6 class="mt-3"><i class="bi bi-chat-left-text me-2"></i>Phản hồi khách hàng</h6>
                                    <p><?php echo $journal['customer_feedback']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Images -->
                    <?php if (!empty($journal['images'])): ?>
                    <h6><i class="bi bi-images me-2"></i>Hình ảnh ngày <?php echo $journal['day_number']; ?></h6>
                    <div class="row g-2 mt-2">
                        <?php foreach ($journal['images'] as $image): ?>
                        <div class="col-md-3">
                            <div class="position-relative">
                                <img src="<?php echo $image['url']; ?>" 
                                     class="img-fluid rounded journal-image"
                                     onclick="showImageModal('<?php echo $image['url']; ?>', '<?php echo $image['caption']; ?>')">
                                <div class="position-absolute bottom-0 start-0 p-2 text-white w-100" 
                                     style="background: rgba(0,0,0,0.5); border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                                    <small><?php echo $image['caption']; ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Actions -->
                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-sm btn-outline-primary me-2" 
                                onclick="editJournal(<?php echo $journal['journal_id']; ?>)">
                            <i class="bi bi-pencil me-1"></i>Sửa
                        </button>
                        <button class="btn btn-sm btn-outline-danger" 
                                onclick="deleteJournal(<?php echo $journal['journal_id']; ?>)">
                            <i class="bi bi-trash me-1"></i>Xóa
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-journal-x display-1 text-muted"></i>
                <h5 class="text-muted mt-3">Chưa có nhật ký nào</h5>
                <p class="text-muted">Hãy bắt đầu ghi nhật ký ngày đầu tiên của tour</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="">
                <p id="modalCaption" class="mt-2"></p>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="fixed-bottom bg-white border-top p-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <div>
                <a href="tour_detail.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại chi tiết tour
                </a>
            </div>
            <div class="btn-group">
                <button class="btn btn-info" onclick="syncJournals()">
                    <i class="bi bi-cloud-arrow-up me-1"></i>Đồng bộ lên server
                </button>
                <button class="btn btn-primary" onclick="printJournal()">
                    <i class="bi bi-printer me-1"></i>In nhật ký
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPhotoModal">
                    <i class="bi bi-camera me-1"></i>Chụp ảnh ngay
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Photo Modal -->
<div class="modal fade" id="addPhotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chụp ảnh / Thêm ảnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div id="cameraPreview" class="border rounded p-3 mb-3" style="height: 300px; background: #f8f9fa;">
                        <i class="bi bi-camera display-4 text-muted"></i>
                        <p class="mt-2">Camera sẽ hiển thị ở đây</p>
                    </div>
                    
                    <div class="btn-group w-100 mb-3">
                        <button class="btn btn-primary" onclick="startCamera()">
                            <i class="bi bi-camera-video me-1"></i>Bật camera
                        </button>
                        <button class="btn btn-success" onclick="capturePhoto()">
                            <i class="bi bi-camera-fill me-1"></i>Chụp ảnh
                        </button>
                        <button class="btn btn-outline-secondary" onclick="stopCamera()">
                            <i class="bi bi-camera-video-off me-1"></i>Tắt
                        </button>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Hoặc tải ảnh từ thiết bị</label>
                        <input type="file" class="form-control" accept="image/*" onchange="previewUploadedImage(this)">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Chú thích ảnh</label>
                        <input type="text" class="form-control" placeholder="Nhập chú thích cho ảnh...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="savePhoto()">Lưu ảnh</button>
            </div>
        </div>
    </div>
</div>

<script>
let stream = null;

// Handle quick journal form
document.getElementById('quickJournalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (this.checkValidity()) {
        const formData = new FormData(this);
        
        // Add images from file input
        const imageInput = document.getElementById('imageUpload');
        for (let i = 0; i < imageInput.files.length; i++) {
            formData.append('images[]', imageInput.files[i]);
        }
        
        // Simulate saving
        alert('Đã lưu nhật ký ngày <?php echo $tour_info["current_day"]; ?>');
        this.reset();
        document.getElementById('imagePreview').innerHTML = '';
        
        // In real app, send to server
        // fetch('/api/journal/save', { method: 'POST', body: formData })
    } else {
        this.reportValidity();
    }
});

// Handle image upload preview
document.getElementById('imageUpload').addEventListener('change', function() {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    for (let file of this.files) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail me-2 mb-2';
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }
});

function saveDraft() {
    localStorage.setItem('journal_draft', document.getElementById('quickJournalForm').querySelector('textarea').value);
    alert('Đã lưu bản nháp vào bộ nhớ cục bộ');
}

function exportJournals() {
    const format = prompt('Chọn định dạng xuất (pdf/excel):', 'pdf');
    if (format) {
        const fileName = `nhat_ky_tour_<?php echo $tour_info["tour_code"]; ?>_<?php echo date('Y-m-d'); ?>.${format}`;
        alert(`Đang xuất file: ${fileName}`);
        // In real app, generate and download file
    }
}

function editJournal(journalId) {
    if (confirm('Bạn muốn sửa nhật ký này?')) {
        // Load journal data into form
        alert('Đang tải dữ liệu nhật ký...');
        // In real app, fetch journal data and populate form
    }
}

function deleteJournal(journalId) {
    if (confirm('Bạn có chắc muốn xóa nhật ký này?')) {
        alert('Đã xóa nhật ký');
        // In real app, send delete request to server
    }
}

function showImageModal(src, caption) {
    document.getElementById('modalImage').src = src;
    document.getElementById('modalCaption').textContent = caption;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function syncJournals() {
    if (confirm('Đồng bộ tất cả nhật ký lên server?')) {
        alert('Đang đồng bộ...');
        // In real app, sync local data with server
    }
}

function printJournal() {
    const printContent = document.querySelector('.guide-content').innerHTML;
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Nhật ký Tour - <?php echo $tour_info["tour_name"]; ?></title>
            <style>
                body { font-family: Arial, sans-serif; }
                .journal-day { border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; }
                .weather-info { background: #f8f9fa; padding: 15px; border-radius: 5px; }
                .header { text-align: center; margin-bottom: 30px; }
                .timestamp { text-align: right; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>NHẬT KÝ TOUR</h2>
                <h4><?php echo $tour_info["tour_name"]; ?> (<?php echo $tour_info["tour_code"]; ?>)</h4>
                <p>HDV: <?php echo $_SESSION['guide_name'] ?? 'Hướng dẫn viên'; ?></p>
            </div>
            ${printContent}
        </body>
        </html>
    `;
    
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}

// Camera functions
function startCamera() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(mediaStream) {
                stream = mediaStream;
                const video = document.createElement('video');
                video.srcObject = mediaStream;
                video.autoplay = true;
                video.style.width = '100%';
                video.style.height = '100%';
                document.getElementById('cameraPreview').innerHTML = '';
                document.getElementById('cameraPreview').appendChild(video);
            })
            .catch(function(err) {
                alert('Không thể truy cập camera: ' + err.message);
            });
    } else {
        alert('Trình duyệt không hỗ trợ camera');
    }
}

function capturePhoto() {
    const preview = document.getElementById('cameraPreview');
    const video = preview.querySelector('video');
    
    if (video) {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        const img = document.createElement('img');
        img.src = canvas.toDataURL('image/png');
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        
        preview.innerHTML = '';
        preview.appendChild(img);
        
        // Store image data for saving
        preview.dataset.imageData = canvas.toDataURL('image/png');
    } else {
        alert('Vui lòng bật camera trước');
    }
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    document.getElementById('cameraPreview').innerHTML = 
        '<i class="bi bi-camera display-4 text-muted"></i><p class="mt-2">Camera đã tắt</p>';
}

function previewUploadedImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            document.getElementById('cameraPreview').innerHTML = '';
            document.getElementById('cameraPreview').appendChild(img);
            
            // Store image data
            document.getElementById('cameraPreview').dataset.imageData = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function savePhoto() {
    const imageData = document.getElementById('cameraPreview').dataset.imageData;
    const caption = document.querySelector('#addPhotoModal input[type="text"]').value;
    
    if (imageData) {
        alert('Đã lưu ảnh: ' + (caption || 'Không có chú thích'));
        $('#addPhotoModal').modal('hide');
        
        // In real app, save to server or local storage
    } else {
        alert('Vui lòng chụp hoặc tải ảnh trước');
    }
}

// Auto-save draft every 2 minutes
setInterval(function() {
    const textarea = document.getElementById('quickJournalForm')?.querySelector('textarea');
    if (textarea && textarea.value.trim()) {
        localStorage.setItem('journal_autosave', textarea.value);
        console.log('Auto-saved journal draft');
    }
}, 120000);

// Load draft on page load
document.addEventListener('DOMContentLoaded', function() {
    const draft = localStorage.getItem('journal_draft');
    if (draft) {
        const textarea = document.getElementById('quickJournalForm')?.querySelector('textarea');
        if (textarea) {
            textarea.value = draft;
        }
    }
});
</script>

<style>
.journal-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s;
}
.journal-image:hover {
    transform: scale(1.05);
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>