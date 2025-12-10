<?php
$page_title = "Chỉnh sửa Lịch khởi hành";
require_once './views/admin/header.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
        }
        .sidebar .logo {
            padding: 1rem;
            border-bottom: 1px solid #34495e;
        }
        .sidebar-nav {
            padding: 1rem 0;
        }
        .nav-item {
            display: block;
            padding: 0.75rem 1rem;
            color: #bdc3c7;
            text-decoration: none;
            border-left: 3px solid transparent;
        }
        .nav-item:hover, .nav-item.active {
            background: #34495e;
            color: white;
            border-left-color: #3498db;
        }
        .main-content {
            flex: 1;
            background: #ecf0f1;
        }
        .top-header {
            background: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .content-area {
            padding: 2rem;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .alert {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .current-info {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->


        <!-- Main Content -->
        <div class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <h1>Chỉnh sửa Lịch khởi hành</h1>
                </div>
                <div class="header-right">
                    <span>Xin chào, <?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="content-area">
<div class="content-area">
    <div class="form-container">
        <!-- Thông tin hiện tại -->
        <div class="current-info">
            <h4>📋 Thông tin hiện tại</h4>
            <p><strong>Tour:</strong> <?php echo htmlspecialchars($departure['tour_name'] ?? ''); ?> (<?php echo $departure['tour_code'] ?? ''; ?>)</p>
            <p><strong>Ngày khởi hành:</strong> <?php echo date('d/m/Y', strtotime($departure['departure_date'] ?? '')); ?></p>
            <p><strong>Trạng thái:</strong> 
                <span style="background: <?php 
                    echo ($departure['status'] ?? '') == 'scheduled' ? '#fff3cd' : 
                         (($departure['status'] ?? '') == 'confirmed' ? '#d4edda' : '#d1ecf1'); 
                ?>; padding: 4px 8px; border-radius: 4px;">
                    <?php echo $departure['status'] ?? ''; ?>
                </span>
            </p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="tour_id">Chọn Tour *</label>
                    <select id="tour_id" name="tour_id" required>
                        <option value="">-- Chọn tour --</option>
                        <?php foreach ($tours as $tour): ?>
                            <option value="<?php echo $tour['tour_id']; ?>" 
                                <?php echo $tour['tour_id'] == ($departure['tour_id'] ?? '') ? 'selected' : ''; ?>>
                                <?php echo $tour['tour_code']; ?> - <?php echo htmlspecialchars($tour['tour_name'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="departure_date">Ngày khởi hành *</label>
                    <input type="date" id="departure_date" name="departure_date" 
                           value="<?php echo $departure['departure_date'] ?? ''; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="departure_time">Giờ khởi hành</label>
                    <input type="time" id="departure_time" name="departure_time" 
                           value="<?php echo $departure['departure_time'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label for="expected_slots">Số chỗ dự kiến *</label>
                    <input type="number" id="expected_slots" name="expected_slots" 
                           min="1" max="100" value="<?php echo $departure['expected_slots'] ?? 20; ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price_adult">Giá người lớn (VNĐ) *</label>
                    <input type="number" id="price_adult" name="price_adult" 
                           min="0" value="<?php echo $departure['price_adult'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="price_child">Giá trẻ em (VNĐ) *</label>
                    <input type="number" id="price_child" name="price_child" 
                           min="0" value="<?php echo $departure['price_child'] ?? ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="meeting_point">Điểm tập trung</label>
                <textarea id="meeting_point" name="meeting_point" 
                          placeholder="Địa điểm và thông tin tập trung..."><?php echo htmlspecialchars($departure['meeting_point'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="operational_notes">Ghi chú vận hành</label>
                <textarea id="operational_notes" name="operational_notes" 
                          placeholder="Ghi chú đặc biệt cho đội vận hành..."><?php echo htmlspecialchars($departure['operational_notes'] ?? ''); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Cập nhật Lịch trình</button>
                <a href="?act=admin_departures" class="btn btn-secondary">↩️ Quay lại</a>
            </div>
        </form>
    </div>
</div>
                    <!-- Thông tin hiện tại -->
                    <div class="current-info">
                        <h4>📋 Thông tin hiện tại</h4>
                        <p><strong>Tour:</strong> <?php echo htmlspecialchars($departure['tour_name']); ?> (<?php echo $departure['tour_code']; ?>)</p>
                        <p><strong>Ngày khởi hành:</strong> <?php echo date('d/m/Y', strtotime($departure['departure_date'])); ?></p>
                        <p><strong>Trạng thái:</strong> 
                            <span style="background: <?php 
                                echo $departure['status'] == 'scheduled' ? '#fff3cd' : 
                                     ($departure['status'] == 'confirmed' ? '#d4edda' : '#d1ecf1'); 
                            ?>; padding: 4px 8px; border-radius: 4px;">
                                <?php echo $departure['status']; ?>
                            </span>
                        </p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tour_id">Chọn Tour *</label>
                                <select id="tour_id" name="tour_id" required>
                                    <option value="">-- Chọn tour --</option>
                                    <?php foreach ($tours as $tour): ?>
                                        <option value="<?php echo $tour['tour_id']; ?>" 
                                            <?php echo $tour['tour_id'] == $departure['tour_id'] ? 'selected' : ''; ?>>
                                            <?php echo $tour['tour_code']; ?> - <?php echo htmlspecialchars($tour['tour_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="departure_date">Ngày khởi hành *</label>
                                <input type="date" id="departure_date" name="departure_date" 
                                       value="<?php echo $departure['departure_date']; ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="departure_time">Giờ khởi hành</label>
                                <input type="time" id="departure_time" name="departure_time" 
                                       value="<?php echo $departure['departure_time']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="expected_slots">Số chỗ dự kiến *</label>
                                <input type="number" id="expected_slots" name="expected_slots" 
                                       min="1" max="100" value="<?php echo $departure['expected_slots']; ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="price_adult">Giá người lớn (VNĐ) *</label>
                                <input type="number" id="price_adult" name="price_adult" 
                                       min="0" value="<?php echo $departure['price_adult']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="price_child">Giá trẻ em (VNĐ) *</label>
                                <input type="number" id="price_child" name="price_child" 
                                       min="0" value="<?php echo $departure['price_child']; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="meeting_point">Điểm tập trung</label>
                           <textarea id="meeting_point" name="meeting_point" 
          placeholder="Địa điểm và thông tin tập trung..."><?php echo htmlspecialchars($departure['meeting_point'] ?? ''); ?></textarea>

<textarea id="operational_notes" name="operational_notes" 
          placeholder="Ghi chú đặc biệt cho đội vận hành..."><?php echo htmlspecialchars($departure['operational_notes'] ?? ''); ?></textarea>

<!-- Các trường input khác cũng cần sửa -->
<input type="time" id="departure_time" name="departure_time" 
       value="<?php echo $departure['departure_time'] ?? ''; ?>">

<input type="number" id="expected_slots" name="expected_slots" 
       min="1" max="100" value="<?php echo $departure['expected_slots'] ?? 20; ?>" required>

<input type="number" id="price_adult" name="price_adult" 
       min="0" value="<?php echo $departure['price_adult'] ?? ''; ?>" required>

<input type="number" id="price_child" name="price_child" 
       min="0" value="<?php echo $departure['price_child'] ?? ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="operational_notes">Ghi chú vận hành</label>
                            <textarea id="operational_notes" name="operational_notes" 
                                      placeholder="Ghi chú đặc biệt cho đội vận hành..."><?php echo htmlspecialchars($departure['operational_notes']); ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">💾 Cập nhật Lịch trình</button>
                            <a href="?act=admin_departures" class="btn btn-secondary">↩️ Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set min date to today
        document.getElementById('departure_date').min = new Date().toISOString().split('T')[0];
        
        // Real-time validation
        document.getElementById('expected_slots').addEventListener('input', function() {
            if (this.value < 1) {
                this.value = 1;
            }
            if (this.value > 100) {
                this.value = 100;
            }
        });
    </script>
</body>
</html>