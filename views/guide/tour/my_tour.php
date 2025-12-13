<?php
// views/admin/guides/my_tours.php
$page_title = "Tour của tôi";
require_once 'header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📋 TOUR CỦA TÔI</h4>
                </div>
                <div class="card-body">
                    <h5>Xin chào, <?php echo $_SESSION['full_name'] ?? 'HDV'; ?>!</h5>
                    <p>Đây là trang quản lý tour của bạn.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h1 class="text-success">3</h1>
                                    <p>Tour đang phụ trách</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h1 class="text-warning">2</h1>
                                    <p>Tour sắp tới</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h1 class="text-info">5</h1>
                                    <p>Tour đã hoàn thành</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6>Danh sách tour:</h6>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Mã Tour</th>
                                    <th>Tên Tour</th>
                                    <th>Ngày khởi hành</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>T-HN-SP-01</td>
                                    <td>Hà Nội - Sapa - Fansipan</td>
                                    <td>15/12/2024</td>
                                    <td><span class="badge bg-success">Đang diễn ra</span></td>
                                    <td><a href="?act=guide_tour_detail&id=1" class="btn btn-sm btn-info">Chi tiết</a></td>
                                </tr>
                                <tr>
                                    <td>T-HN-HL-02</td>
                                    <td>Hạ Long - Ngủ đêm du thuyền</td>
                                    <td>20/12/2024</td>
                                    <td><span class="badge bg-warning">Sắp tới</span></td>
                                    <td><a href="#" class="btn btn-sm btn-info">Chi tiết</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>