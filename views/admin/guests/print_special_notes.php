<?php
// print_special_notes.php - Đặt trong views/admin/guests/
header('Content-Type: text/html; charset=utf-8');

// Lấy thông tin từ session hoặc GET
$guest = $_SESSION['print_guest_data'] ?? [];
$departure = $_SESSION['print_departure_data'] ?? [];

if (empty($guest)) {
    die('Không có dữ liệu để in!');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghi chú Đặc biệt - <?php echo htmlspecialchars($guest['full_name'] ?? ''); ?></title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            color: #333;
        }
        .header h2 {
            font-size: 16px;
            margin: 0;
            color: #666;
        }
        .section {
            margin: 15px 0;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px 12px;
            border-left: 4px solid #333;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 14px;
        }
        .section-content {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }
        .danger-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }
        .info-box {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }
        .success-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px dashed #999;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .urgent {
            color: #dc3545;
            font-weight: bold;
        }
        .checkbox-list {
            list-style-type: none;
            padding-left: 0;
        }
        .checkbox-list li {
            margin-bottom: 5px;
        }
        .checkbox-list li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>GHI CHÚ ĐẶC BIỆT KHÁCH HÀNG</h1>
        <h2><?php echo htmlspecialchars($guest['full_name'] ?? ''); ?></h2>
        <p>
            <?php if (isset($guest['booking_code'])): ?>
                Booking: <?php echo htmlspecialchars($guest['booking_code']); ?> | 
            <?php endif; ?>
            <?php if (isset($departure['tour_name'])): ?>
                Tour: <?php echo htmlspecialchars($departure['tour_name']); ?> | 
            <?php endif; ?>
            Ngày khởi hành: <?php echo isset($departure['departure_date']) ? date('d/m/Y', strtotime($departure['departure_date'])) : '---'; ?>
        </p>
    </div>
    
    <!-- Dietary Information -->
    <?php if (!empty($guest['dietary_restrictions']) || !empty($guest['food_allergies'])): ?>
    <div class="section">
        <div class="section-title">🍽️ YÊU CẦU ĂN UỐNG</div>
        <div class="section-content">
            <?php if (!empty($guest['dietary_restrictions'])): ?>
            <div class="warning-box">
                <strong>Chế độ ăn đặc biệt:</strong><br>
                <?php echo nl2br(htmlspecialchars($guest['dietary_restrictions'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($guest['food_allergies'])): ?>
            <div class="danger-box">
                <strong>Dị ứng thực phẩm:</strong><br>
                <?php echo nl2br(htmlspecialchars($guest['food_allergies'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($guest['medications'])): ?>
            <div class="info-box">
                <strong>Thuốc đang sử dụng:</strong><br>
                <?php echo nl2br(htmlspecialchars($guest['medications'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($guest['blood_type'])): ?>
            <div class="info-box">
                <strong>Nhóm máu:</strong> <?php echo htmlspecialchars($guest['blood_type']); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Medical Information -->
    <?php if (!empty($guest['medical_notes'])): ?>
    <div class="section">
        <div class="section-title">🏥 THÔNG TIN Y TẾ</div>
        <div class="section-content">
            <div class="danger-box">
                <?php echo nl2br(htmlspecialchars($guest['medical_notes'])); ?>
            </div>
            
            <?php if (!empty($guest['emergency_notes'])): ?>
            <div class="danger-box">
                <strong>Lưu ý sơ cứu/cấp cứu:</strong><br>
                <?php echo nl2br(htmlspecialchars($guest['emergency_notes'])); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Special Requests -->
    <?php if (!empty($guest['special_requests']) || !empty($guest['hobbies_interests']) || !empty($guest['travel_history'])): ?>
    <div class="section">
        <div class="section-title">⭐ YÊU CẦU ĐẶC BIỆT</div>
        <div class="section-content">
            <?php if (!empty($guest['special_requests'])): ?>
            <div class="info-box">
                <strong>Yêu cầu chung:</strong><br>
                <?php echo nl2br(htmlspecialchars($guest['special_requests'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($guest['hobbies_interests'])): ?>
            <div class="info-box">
                <strong>Sở thích:</strong><br>
                <?php echo nl2br(htmlspecialchars($guest['hobbies_interests'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($guest['travel_history'])): ?>
            <div class="info-box">
                <strong>Lịch sử du lịch:</strong><br>
                <?php echo nl2br(htmlspecialchars($guest['travel_history'])); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Room & Transport Requests -->
    <?php if (!empty($guest['room_requests']) || !empty($guest['transport_requests'])): ?>
    <div class="section">
        <div class="section-title">⚙️ YÊU CẦU TIỆN NGHI</div>
        <div class="section-content">
            <div class="row" style="display: flex;">
                <?php if (!empty($guest['room_requests'])): ?>
                <div style="flex: 1; margin-right: 10px;">
                    <div class="info-box">
                        <strong>Phòng nghỉ:</strong><br>
                        <?php 
                        $room_requests = json_decode($guest['room_requests'], true);
                        if (is_array($room_requests)): ?>
                            <ul class="checkbox-list">
                                <?php foreach ($room_requests as $request): ?>
                                    <li><?php echo htmlspecialchars($request); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?php echo htmlspecialchars($guest['room_requests']); ?>
                        <?php endif; ?>
                        <?php if (!empty($guest['room_requests_other'])): ?>
                            <br><em><?php echo htmlspecialchars($guest['room_requests_other']); ?></em>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($guest['transport_requests'])): ?>
                <div style="flex: 1;">
                    <div class="info-box">
                        <strong>Di chuyển:</strong><br>
                        <?php 
                        $transport_requests = json_decode($guest['transport_requests'], true);
                        if (is_array($transport_requests)): ?>
                            <ul class="checkbox-list">
                                <?php foreach ($transport_requests as $request): ?>
                                    <li><?php echo htmlspecialchars($request); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?php echo htmlspecialchars($guest['transport_requests']); ?>
                        <?php endif; ?>
                        <?php if (!empty($guest['transport_requests_other'])): ?>
                            <br><em><?php echo htmlspecialchars($guest['transport_requests_other']); ?></em>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Staff Notes -->
    <?php if (!empty($guest['notes_for_guide']) || !empty($guest['notes_for_hotel']) || !empty($guest['requires_special_attention'])): ?>
    <div class="section">
        <div class="section-title">📝 GHI CHÚ NỘI BỘ</div>
        <div class="section-content">
            <?php if (!empty($guest['notes_for_guide'])): ?>
            <div class="warning-box">
                <strong>Cho Hướng dẫn viên:</strong><br>
                <?php echo nl2br(htmlspecialchars($guest['notes_for_guide'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($guest['notes_for_hotel'])): ?>
            <div class="warning-box">
                <strong>Cho Khách sạn/Nhà hàng:</strong><br>
                <?php echo nl2br(htmlspecialchars($guest['notes_for_hotel'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($guest['requires_special_attention'])): ?>
            <div class="danger-box">
                <span class="urgent">⚠️ CẦN QUAN TÂM ĐẶC BIỆT</span><br>
                Khách hàng này cần được chú ý và quan tâm đặc biệt trong suốt chuyến đi.
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Emergency Contact -->
    <?php if (!empty($guest['emergency_contact_name']) || !empty($guest['emergency_contact'])): ?>
    <div class="section">
        <div class="section-title">📞 LIÊN HỆ KHẨN CẤP</div>
        <div class="section-content">
            <div class="success-box">
                <?php if (!empty($guest['emergency_contact_name'])): ?>
                    <strong>Tên:</strong> <?php echo htmlspecialchars($guest['emergency_contact_name']); ?>
                    <?php if (!empty($guest['emergency_relationship'])): ?>
                        (<?php echo htmlspecialchars($guest['emergency_relationship']); ?>)
                    <?php endif; ?>
                    <br>
                <?php endif; ?>
                
                <?php if (!empty($guest['emergency_contact_phone'])): ?>
                    <strong>Điện thoại:</strong> <?php echo htmlspecialchars($guest['emergency_contact_phone']); ?><br>
                <?php endif; ?>
                
                <?php if (!empty($guest['emergency_contact_email'])): ?>
                    <strong>Email:</strong> <?php echo htmlspecialchars($guest['emergency_contact_email']); ?><br>
                <?php endif; ?>
                
                <?php if (!empty($guest['emergency_contact_address'])): ?>
                    <strong>Địa chỉ:</strong> <?php echo nl2br(htmlspecialchars($guest['emergency_contact_address'])); ?><br>
                <?php endif; ?>
                
                <?php if (empty($guest['emergency_contact_name']) && !empty($guest['emergency_contact'])): ?>
                    <strong>Thông tin liên hệ:</strong> <?php echo htmlspecialchars($guest['emergency_contact']); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        <p>Tài liệu được in vào lúc: <?php echo date('H:i d/m/Y'); ?></p>
        <p>--- TÀI LIỆU NỘI BỘ - BẢO MẬT THÔNG TIN KHÁCH HÀNG ---</p>
        <p>Chỉ sử dụng cho mục đích phục vụ khách hàng và không được chia sẻ ra bên ngoài.</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
            // Sau khi in xong, quay lại trang trước
            setTimeout(function() {
                window.history.back();
            }, 1000);
        }
    </script>
</body>
</html>