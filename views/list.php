<h2>Lịch trình Tour</h2>
<a href="index.php?act=detail-add&tour_id=<?= $_GET['tour_id'] ?>">+ Thêm ngày</a>

<table border="1" cellpadding="10">
<tr>
    <th>Ngày</th>
    <th>Tiêu đề</th>
    <th>Nội dung</th>
    <th>Ảnh</th>
    <th>Hành động</th>
</tr>

<?php foreach ($details as $d): ?>
<tr>
    <td><?= $d['day_number'] ?></td>
    <td><?= $d['title'] ?></td>
    <td><?= $d['content'] ?></td>
    <td><img src="uploads/details/<?= $d['image'] ?>" width="80"></td>
    <td>
        <a href="index.php?act=detail-edit&id=<?= $d['detail_id'] ?>">Sửa</a> |
        <a onclick="return confirm('Xóa?')" 
           href="index.php?act=detail-delete&id=<?= $d['detail_id'] ?>&tour_id=<?= $_GET['tour_id'] ?>">Xóa</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
