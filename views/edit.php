<h2>Sửa Tour</h2>

<form action="index.php?act=update" method="post"  enctype="multipart/form-data">

    <input type="hidden" name="tour_id" value="<?= $tour['tour_id'] ?>">

    Tên tour: <input type="text" name="tour_name" required value="<?= $tour['tour_name'] ?>"><br><br>

    Giá: <input type="number" name="price" required value="<?= $tour['price'] ?>"><br><br>

    Mô tả: <textarea name="description" value="<?= $tour['description'] ?>"></textarea><br><br>

    Ngày khởi hành: <input type="date" name="start_date" required value="<?= $tour['start_date'] ?>"><br><br>

    Số ngày tour: <input type="text" name="duration" required value="<?= $tour['duration'] ?>"> <br><br>

    Điểm đến: <input type="text" name="destination" required value="<?= $tour['destination'] ?>"> <br><br>

    Số chỗ trống: <input type="text" name="available_seats" value="<?= $tour['available_seats'] ?>"><br><br>

    Ảnh: <input type="file" name="image" value="<?= $tour['image'] ?>"><br><br>

    Trạng thái hoạt động <select name="status">
    <option value="Hoạt động" <?= $tour['status'] == "Hoạt động" ? 'selected' : '' ?>>Hoạt động</option>
    <option value="Ngưng hoạt động" <?= $tour['status'] == "Ngưng hoạt động" ? 'selected' : '' ?>>Ngưng hoạt động</option>
</select>

    <button type="submit">Cập nhật</button>
</form>
