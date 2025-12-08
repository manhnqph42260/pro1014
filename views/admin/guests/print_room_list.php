<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Phòng - <?php echo htmlspecialchars($departure['tour_name']); ?></title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 16px;
            margin: 0 0 3px 0;
            color: #333;
        }
        .header h2 {
            font-size: 14px;
            margin: 0;
            color: #666;
        }
        .hotel-section {
            margin: 15px 0;
            page-break-inside: avoid;
        }
        .hotel-header {
            background-color: #e3f2fd;
            padding: 6px 10px;
            border-left: 4px solid #2196f3;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .room-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .room-table th, .room-table td {
            border: 1px solid #999;
            padding: 4px 6px;
            vertical-align: top;
        }
        .room-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .room-number {
            font-weight: bold;
            color: #d32f2f;
        }
        .guest-info {
            margin: 2px 0;
        }
        .guest-name {
            font-weight: bold;
        }
        .guest-details {
            font-size: 10px;
            color: #666;
        }
        .room-type {
            text-align: center;
        }
        .dates {
            text-align: center;
            white-space: nowrap;
        }
        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px dashed #999;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DANH SÁCH PHÂN PHÒNG KHÁCH SẠN</h1>
        <h2><?php echo htmlspecialchars($departure['tour_name']); ?></h2>
        <p>Mã tour: <?php echo htmlspecialchars($departure['tour_code']); ?> | 
           Ngày: <?php echo date('d/m/Y', strtotime($departure['departure_date'])); ?> | 
           Số ngày: <?php echo $departure['duration_days'] ?? '--'; ?> ngày</p>
    </div>
    
    <div class="summary">
        <strong>TỔNG HỢP:</strong> <?php echo count($room_list); ?> khách đã được phân phòng tại 
        <?php echo count($hotels); ?> phòng thuộc <?php echo count(array_unique(array_column($room_list, 'hotel_name'))); ?> khách sạn
    </div>
    
    <?php foreach ($hotels as $hotel_key => $hotel): ?>
    <div class="hotel-section">
        <div class="hotel-header">
            KHÁCH SẠN: <?php echo htmlspecialchars($hotel['hotel_name']); ?> | 
            PHÒNG: <span class="room-number"><?php echo htmlspecialchars($hotel['room_number']); ?></span> | 
            LOẠI: <?php 
                $room_types = ['single' => 'Đơn', 'double' => 'Đôi', 'triple' => 'Ba', 'family' => 'GĐ', 'suite' => 'Suite'];
                echo $room_types[$hotel['room_type']] ?? $hotel['room_type'];
            ?> |
            NGÀY Ở: <?php echo $hotel['check_in_date'] ? date('d/m', strtotime($hotel['check_in_date'])) : '--'; ?> 
            đến <?php echo $hotel['check_out_date'] ? date('d/m', strtotime($hotel['check_out_date'])) : '--'; ?>
        </div>
        
        <table class="room-table">
            <thead>
                <tr>
                    <th style="width: 40px;">STT</th>
                    <th>KHÁCH HÀNG</th>
                    <th style="width: 60px;">GIỚI TÍNH</th>
                    <th style="width: 80px;">LOẠI KHÁCH</th>
                    <th style="width: 80px;">MÃ BOOKING</th>
                    <th style="width: 120px;">GHI CHÚ ĐẶC BIỆT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hotel['guests'] as $guest_index => $guest): ?>
                <tr>
                    <td style="text-align: center;"><?php echo $guest_index + 1; ?></td>
                    <td>
                        <div class="guest-info">
                            <div class="guest-name"><?php echo htmlspecialchars($guest['full_name']); ?></div>
                            <div class="guest-details">
                                <?php if ($guest['date_of_birth']): ?>
                                    NS: <?php echo date('d/m/Y', strtotime($guest['date_of_birth'])); ?> |
                                <?php endif; ?>
                                <?php if ($guest['id_number']): ?>
                                    CMND: <?php echo htmlspecialchars($guest['id_number']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center;"><?php echo $guest['gender'] == 'male' ? 'Nam' : 'Nữ'; ?></td>
                    <td style="text-align: center;">
                        <?php 
                        $guest_types = ['adult' => 'NL', 'child' => 'TE', 'infant' => 'EB'];
                        echo $guest_types[$guest['guest_type']] ?? $guest['guest_type'];
                        ?>
                    </td>
                    <td style="text-align: center;"><?php echo htmlspecialchars($guest['booking_code']); ?></td>
                    <td>
                        <?php 
                        $special_notes = [];
                        if (!empty($guest['dietary_restrictions'])) $special_notes[] = "Ăn: " . substr($guest['dietary_restrictions'], 0, 20);
                        if (!empty($guest['medical_notes'])) $special_notes[] = "SK: " . substr($guest['medical_notes'], 0, 20);
                        if (!empty($guest['special_requests'])) $special_notes[] = "YC: " . substr($guest['special_requests'], 0, 20);
                        echo implode('<br>', $special_notes);
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($hotels)): ?>
    <div style="text-align: center; padding: 20px; color: #999;">
        <p>Chưa có thông tin phân phòng khách sạn cho tour này.</p>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        <p>Danh sách được in vào lúc: <?php echo date('H:i d/m/Y'); ?></p>
        <p>Quản lý Tour - Hệ thống Quản lý Du lịch</p>
        <p>Lưu ý: Danh sách này được cập nhật đến thời điểm in. Vui lòng kiểm tra lại với quản lý tour.</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>