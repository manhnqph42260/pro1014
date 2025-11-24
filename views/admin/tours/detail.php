<h3>Chính sách Tour</h3>

<!-- Form thêm chính sách -->
<form method="post" action="index.php?act=save_policy">
    <input type="hidden" name="tour_id" value="<?= $tour_id ?>">

    <label>Loại chính sách:</label>
    <select name="policy_type">
        <option value="cancellation">Hủy tour</option>
        <option value="change">Đổi lịch</option>
        <option value="health">Sức khỏe</option>
        <option value="luggage">Hành lý</option>
    </select><br>

    <label>Tiêu đề:</label>
    <input type="text" name="title"><br>

    <label>Nội dung:</label>
    <textarea name="content"></textarea><br>

    <button>Thêm chính sách</button>
</form>

<hr>

<!-- Danh sách chính sách -->
<table border="1" cellpadding="10">
    <tr>
        <th>Loại</th>
        <th>Tiêu đề</th>
        <th>Nội dung</th>
        <th>Hành động</th>
    </tr>

    <?php foreach ($policies as $p): ?>
    <tr>
        <td><?= $p["policy_type"] ?></td>
        <td><?= $p["title"] ?></td>
        <td><?= nl2br($p["content"]) ?></td>
        <td>
            <a href="index.php?act=delete_policy&policy_id=<?= $p['policy_id'] ?>&tour_id=<?= $tour_id ?>">Xóa</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
