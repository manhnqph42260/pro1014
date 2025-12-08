<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Đoàn - <?php echo htmlspecialchars($departure['tour_name']); ?></title>
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
        .info-box {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #f9f9f9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .stt-col {
            width: 30px;
            text-align: center;
        }
        .name-col {
            width: 150px;
        }
        .dob-col {
            width: 80px;
        }
        .gender-col {
            width: 50px;
        }
        .id-col {
            width: 100px;
        }
        .booking-col {
            width: 80px;
        }
        .status-col {
            width: 70px;
        }
        .notes-col {
            width: 120px;
        }
        .stats {
            margin-top: 20px;
            padding: 10px;
            background: #f0f8ff;
            border: 1px solid #b0c4de;
            border-radius: 4px;
        }
        .stats strong {
            color: #0066cc;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px dashed #999;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DANH SÁCH ĐOÀN DU LỊCH</h1>
        <h2><?php echo htmlspecialchars($departure['tour_name']); ?></h2>
        <p>Mã tour: <?php echo htmlspecialchars($departure['tour_code']); ?> | 
           Ngày khởi hành: <?php echo date('d/m/Y', strtotime($departure['departure_date'])); ?> | 
           Thời gian: <?php echo date('H:i', strtotime($departure['departure_time'])); ?></p>
        <p>Điểm hẹn: <?php echo htmlspecialchars($departure['meeting_point']); ?></p>
    </div>
    
    <div class="info-box">
        <strong>THÔNG TIN ĐOÀN:</strong><br>
        Tổng số khách: <strong><?php echo $stats['total_guests']; ?> người</strong> 
        (<?php echo $stats['adults']; ?> NL, <?php echo $stats['children']; ?> TE, <?php echo $stats['checked_in']; ?> đã check-in)<br>
        Hướng dẫn viên: ___________________________________<br>
        Tài xế: ___________________________________<br>
        Phương tiện: ___________________________________
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="stt-col">STT</th>
                <th class="name-col">HỌ VÀ TÊN</th>
                <th class="dob-col">NGÀY SINH</th>
                <th class="gender-col">GT</th>
                <th class="id-col">SỐ CMND/CCCD</th>
                <th>QUỐC TỊCH</th>
                <th class="booking-col">MÃ BOOKING</th>
                <th class="status-col">TRẠNG THÁI</th>
                <th class="notes-col">GHI CHÚ ĐẶC BIỆT</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($guest_list as $index => $guest): ?>
            <tr>
                <td class="stt-col"><?php echo $index + 1; ?></td>
                <td class="name-col">
                    <strong><?php echo htmlspecialchars($guest['full_name']); ?></strong>
                    <br><small><?php echo $guest['booker_name']; ?></small>
                </td>
                <td class="dob-col"><?php echo $guest['date_of_birth'] ? date('d/m/Y', strtotime($guest['date_of_birth'])) : '---'; ?></td>
                <td class="gender-col"><?php echo $guest['gender'] == 'male' ? 'Nam' : 'Nữ'; ?></td>
                <td class="id-col"><?php echo htmlspecialchars($guest['id_number'] ?? '---'); ?></td>
                <td><?php echo htmlspecialchars($guest['nationality'] ?? 'VN'); ?></td>
                <td class="booking-col"><?php echo htmlspecialchars($guest['booking_code']); ?></td>
                <td class="status-col">
                    <?php if ($guest['check_status'] == 'checked_in'): ?>
                        <span style="color: green;">✓ Check-in</span>
                    <?php elseif ($guest['check_status'] == 'no_show'): ?>
                        <span style="color: red;">Không đến</span>
                    <?php else: ?>
                        <span style="color: orange;">Chưa CI</span>
                    <?php endif; ?>
                </td>
                <td class="notes-col">
                    <?php 
                    $notes = [];
                    if (!empty($guest['dietary_restrictions'])) $notes[] = "Ăn: " . $guest['dietary_restrictions'];
                    if (!empty($guest['medical_notes'])) $notes[] = "SK: " . $guest['medical_notes'];
                    if (!empty($guest['special_requests'])) $notes[] = "YC: " . $guest['special_requests'];
                    echo implode('<br>', array_slice($notes, 0, 2));
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="stats">
        <strong>TỔNG HỢP:</strong><br>
        - Tổng số khách: <?php echo $stats['total_guests']; ?> người<br>
        - Trong đó: Người lớn: <?php echo $stats['adults']; ?> | Trẻ em: <?php echo $stats['children']; ?> | Em bé: <?php echo ($stats['total_guests'] - $stats['adults'] - $stats['children']); ?><br>
        - Đã check-in: <?php echo $stats['checked_in']; ?> người | Chưa check-in: <?php echo ($stats['total_guests'] - $stats['checked_in']); ?> người
    </div>
    
    <div class="footer">
        <p>Danh sách được in vào lúc: <?php echo date('H:i d/m/Y'); ?></p>
        <p>Quản lý Tour - Hệ thống Quản lý Du lịch</p>
        <p>Địa chỉ: ___________________________________ | Điện thoại: _________________</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>