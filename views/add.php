<h2>Thêm Tour</h2>

<form action="index.php?act=store" method="post"  enctype="multipart/form-data">

    Tên tour: <input type="text" name="tour_name" required><br><br>

    Giá: <input type="number" name="price" required><br><br>

    Mô tả: <textarea name="description"></textarea><br><br>

    Ngày khởi hành: <input type="date" name="start_date" required><br><br>

    Số ngày tour: <input type="text" name="duration" required> <br><br>

    Điểm đến: <input type="text" name="destination" required> <br><br>

    Số chỗ trống: <input type="text" name="available_seats"><br><br>

    Ảnh: <input type="file" name="image"><br><br>

    Trạng thái hoạt động <select name="status">
    <option value="Hoạt động">Hoạt động</option>
    <option value="Ngưng hoạt động">Ngưng hoạt động</option>
</select>
    <button type="submit">Lưu</button>
</form>
