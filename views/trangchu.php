<h2>Danh sách Tour</h2>
<a href="index.php?act=add">+ Thêm tour</a>
<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Tên Tour</th>
        <th>Giá</th>
        <th>Mô tả</th>
        <th>Ngày bắt đầu</th>
        <th>Số ngày tour</th>
        <th>Điểm đến</th>
        <th>Chỗ trống</th>
        <th>Ảnh</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>

    <?php foreach ($tours as $tour): ?>
    <tr>
        <td><?= $tour['tour_id'] ?></td>
        <td><?= $tour['tour_name'] ?></td>
        <td><?= number_format($tour['price']) ?> Đ</td>
        <td><?= $tour['description'] ?></td>
        <td><?= $tour['start_date'] ?></td>
        <td><?= $tour['duration'] ?></td>
        <td><?= $tour['destination'] ?></td>
        <td><?= $tour['available_seats'] ?></td>
        <td><img src="uploads/<?= $tour["image"] ?>" width="80px" alt=""></td>
        <td><?= $tour['status'] ?></td>

        <td>
            <a href="index.php?act=edit&id=<?= $tour['tour_id'] ?>">Sửa</a> |
            <a onclick="return confirm('Bạn muốn xóa không?')" 
               href="index.php?act=delete&id=<?= $tour['tour_id'] ?>">Xóa</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
