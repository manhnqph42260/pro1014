<?php
$title = "Tạo Booking mới";
require_once './views/admin/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tạo Booking mới</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="?act=admin_bookings" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST" class="row g-3">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông tin lịch trình</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Chọn lịch khởi hành *</label>
<select name="departure_id" class="form-select" required id="departureSelect">
    <option value="">-- Chọn lịch trình --</option>
    <?php foreach ($departures as $departure): ?>
        <option value="<?= $departure['departure_id'] ?>" 
                data-price-adult="<?= $departure['price_adult'] ?>"
                data-price-child="<?= $departure['price_child'] ?>"
                data-available-slots="<?= $departure['available_slots'] ?>">
            <?= $departure['tour_name'] ?> - <?= date('d/m/Y', strtotime($departure['departure_date'])) ?> 
            (Còn <?= $departure['available_slots'] ?> chỗ)
        </option>
    <?php endforeach; ?>
</select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Loại booking</label>
                    <select name="booking_type" class="form-select" id="bookingType">
                        <option value="individual">Khách lẻ</option>
                        <option value="group">Đoàn/Group</option>
                    </select>
                </div>
                
                <div id="groupFields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Tên đoàn/group</label>
                        <input type="text" name="group_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên công ty</label>
                        <input type="text" name="company_name" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông tin khách hàng</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Họ tên *</label>
                    <input type="text" name="customer_name" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại *</label>
                            <input type="text" name="customer_phone" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <textarea name="customer_address" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Thông tin khách</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Số người lớn</label>
                        <input type="number" name="adult_count" class="form-control" value="1" min="0" id="adultCount">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số trẻ em</label>
                        <input type="number" name="child_count" class="form-control" value="0" min="0" id="childCount">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số em bé</label>
                        <input type="number" name="infant_count" class="form-control" value="0" min="0" id="infantCount">
                    </div>
                </div>
                
                <div id="guestDetails" class="mt-3">
                    <!-- Guest details will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Yêu cầu đặc biệt</h5>
            </div>
            <div class="card-body">
                <textarea name="special_requests" class="form-control" rows="3" placeholder="Yêu cầu về ăn uống, dị ứng, y tế..."></textarea>
            </div>
        </div>
    </div>
    
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Tạo Booking</button>
        <a href="?act=admin_bookings" class="btn btn-secondary">Hủy</a>
    </div>
</form>

<script>
// JavaScript for dynamic form handling
document.getElementById('bookingType').addEventListener('change', function() {
    document.getElementById('groupFields').style.display = this.value === 'group' ? 'block' : 'none';
});

// Function to update guest details
function updateGuestDetails() {
    const adultCount = parseInt(document.getElementById('adultCount').value) || 0;
    const childCount = parseInt(document.getElementById('childCount').value) || 0;
    const infantCount = parseInt(document.getElementById('infantCount').value) || 0;
    const totalGuests = adultCount + childCount + infantCount;
    
    let guestHtml = '<h6>Thông tin chi tiết khách:</h6>';
    
    // Add adult guests
    for (let i = 1; i <= adultCount; i++) {
        guestHtml += `
            <div class="row mb-2">
                <div class="col-md-4">
                    <input type="text" name="guest_names[]" class="form-control" placeholder="Họ tên người lớn ${i}" required>
                </div>
                <div class="col-md-3">
                    <input type="date" name="guest_dobs[]" class="form-control" placeholder="Ngày sinh">
                </div>
                <div class="col-md-3">
                    <select name="guest_genders[]" class="form-select">
                        <option value="">Giới tính</option>
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="hidden" name="guest_types[]" value="adult">
                    <span class="badge bg-primary">Người lớn</span>
                </div>
            </div>
        `;
    }
    
    // Add child guests
    for (let i = 1; i <= childCount; i++) {
        guestHtml += `
            <div class="row mb-2">
                <div class="col-md-4">
                    <input type="text" name="guest_names[]" class="form-control" placeholder="Họ tên trẻ em ${i}" required>
                </div>
                <div class="col-md-3">
                    <input type="date" name="guest_dobs[]" class="form-control" placeholder="Ngày sinh">
                </div>
                <div class="col-md-3">
                    <select name="guest_genders[]" class="form-select">
                        <option value="">Giới tính</option>
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="hidden" name="guest_types[]" value="child">
                    <span class="badge bg-warning">Trẻ em</span>
                </div>
            </div>
        `;
    }
    
    // Add infant guests
    for (let i = 1; i <= infantCount; i++) {
        guestHtml += `
            <div class="row mb-2">
                <div class="col-md-4">
                    <input type="text" name="guest_names[]" class="form-control" placeholder="Họ tên em bé ${i}" required>
                </div>
                <div class="col-md-3">
                    <input type="date" name="guest_dobs[]" class="form-control" placeholder="Ngày sinh">
                </div>
                <div class="col-md-3">
                    <select name="guest_genders[]" class="form-select">
                        <option value="">Giới tính</option>
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="hidden" name="guest_types[]" value="infant">
                    <span class="badge bg-info">Em bé</span>
                </div>
            </div>
        `;
    }
    
    document.getElementById('guestDetails').innerHTML = totalGuests > 0 ? guestHtml : '<p class="text-muted">Chưa có thông tin khách</p>';
}

// Add event listeners to count inputs
['adultCount', 'childCount', 'infantCount'].forEach(id => {
    document.getElementById(id).addEventListener('change', updateGuestDetails);
    document.getElementById(id).addEventListener('input', updateGuestDetails);
});

// Initialize guest details
updateGuestDetails();
// Real-time validation for guest count
function validateGuestCount() {
    const departureSelect = document.getElementById('departureSelect');
    const adultCount = parseInt(document.getElementById('adultCount').value) || 0;
    const childCount = parseInt(document.getElementById('childCount').value) || 0;
    const infantCount = parseInt(document.getElementById('infantCount').value) || 0;
    const totalGuests = adultCount + childCount + infantCount;
    
    if (departureSelect.value) {
        const selectedOption = departureSelect.options[departureSelect.selectedIndex];
        const availableSlots = parseInt(selectedOption.getAttribute('data-available-slots'));
        
        if (totalGuests > availableSlots) {
            alert(`Cảnh báo: Bạn đang đặt ${totalGuests} khách nhưng chỉ còn ${availableSlots} chỗ trống!`);
            return false;
        }
        
        if (totalGuests === 0) {
            alert('Vui lòng nhập số khách!');
            return false;
        }
    }
    
    return true;
}

// Add event listeners
document.getElementById('departureSelect').addEventListener('change', validateGuestCount);
document.getElementById('adultCount').addEventListener('change', validateGuestCount);
document.getElementById('childCount').addEventListener('change', validateGuestCount);
document.getElementById('infantCount').addEventListener('change', validateGuestCount);

// Form submission validation
document.querySelector('form').addEventListener('submit', function(e) {
    if (!validateGuestCount()) {
        e.preventDefault();
        return false;
    }
});
</script>

<?php require_once './views/admin/footer.php'; ?>
