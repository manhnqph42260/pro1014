# 🔐 Hướng Dẫn Sử Dụng Cổng Login Chung

## 📋 Tổng Quan
Ứng dụng đã được cấu hình với **một cổng login duy nhất** cho cả Admin và Hướng Dẫn Viên (HDV).

---

## 🔑 Tài Khoản Mẫu

### Admin Account
- **Username**: `superadmin`
- **Password**: `123456` hoặc password hash từ database
- **Email**: `admin@tour.com`

### Guide Account (HDV)
- **Username/Guide Code**: `HDV001` (hoặc `guidea`)
- **Password**: `123456` 
- **Email**: `guidea@tour.com`

---

## 🌐 Đường Dẫn Chính

### 1. Cổng Login Chung
```
http://localhost/du_an1/index.php?act=login
hoặc
http://localhost/du_an1/?act=login
```

**Chức năng:**
- Hiển thị form login với 2 lựa chọn: "Quản Trị Viên" hoặc "Hướng Dẫn Viên"
- Tự động phân quyền dựa vào lựa chọn và credentials

### 2. Dashboard Admin
```
http://localhost/du_an1/?act=admin_dashboard
```
- **Yêu cầu**: Phải đăng nhập với vai trò Admin
- Hiển thị thống kê tours, departures, guides
- Có thể truy cập các chức năng quản lý

### 3. Dashboard HDV (Hướng Dẫn Viên)
```
http://localhost/du_an1/?act=guide_dashboard
```
- **Yêu cầu**: Phải đăng nhập với vai trò Guide
- Hiển thị tours được phân công, lịch làm việc, etc.

### 4. Đăng Xuất (Chung)
```
http://localhost/du_an1/?act=logout
```
- Xóa tất cả session của Admin hoặc Guide
- Chuyển hướng về login

---

## 🔄 Quy Trình Đăng Nhập

### 1. Truy cập cổng login
```
?act=login
```

### 2. Chọn vai trò
- Bấm vào "Quản Trị Viên" hoặc "Hướng Dẫn Viên"

### 3. Nhập thông tin
- **Trường username**: Tên đăng nhập, email, hoặc mã HDV
- **Trường password**: Mật khẩu

### 4. Nhấn "Đăng Nhập"
- Form POST đến `?act=check_login`
- `AuthController::checkLogin()` xác minh thông tin
- Nếu hợp lệ → Tạo session và redirect
  - Admin → `?act=admin_dashboard`
  - Guide → `?act=guide_dashboard`
- Nếu không hợp lệ → Trở về login với thông báo lỗi

### 5. Đăng Xuất
- Từ bất kỳ dashboard nào, bấm "Đăng xuất"
- Xóa tất cả session
- Quay về login

---

## 🛠️ Cách Thêm Tài Khoản Mới

### Thêm Admin Mới
```sql
INSERT INTO admins (username, email, password_hash, full_name, role_id, status) 
VALUES ('newadmin', 'newadmin@tour.com', PASSWORD_HASH, 'New Admin', 1, 'active');
```

### Thêm HDV Mới
```sql
INSERT INTO guides (guide_code, username, password_hash, full_name, email, status) 
VALUES ('HDV002', 'guideb', PASSWORD_HASH, 'Guide B', 'guideb@tour.com', 'active');
```

*Mật khẩu mặc định để test: `123456`*

---

## 🔒 Cấu Trúc Files

### Controllers
- `controllers/AuthController.php` - **Xử lý login chung** ⭐
  - `login()` - Hiển thị form
  - `checkLogin()` - Xác minh credentials
  - `logout()` - Đăng xuất

- `controllers/AdminController.php` - Admin portal
  - `dashboard()` - Admin dashboard
  - `logout()` - Redirect đến AuthController

### Models
- `models/AdminModel.php` - Kiểm tra admin credentials
- `models/GuideModel.php` - Kiểm tra guide credentials

### Routes (index.php)
```php
'login'       => (new AuthController())->login(),
'check_login' => (new AuthController())->checkLogin(),
'logout'      => (new AuthController())->logout(),

'admin_dashboard' => (new AdminController())->dashboard(),
'guide_dashboard' => (new GuideController())->dashboard(),
```

---

## ⚡ Tính Năng

✅ Một cổng login cho tất cả vai trò
✅ Tự động phân quyền dựa trên vai trò
✅ Session riêng cho Admin và Guide
✅ Logout chung xóa tất cả session
✅ Chuyển hướng tự động dựa trên vai trò
✅ Hỗ trợ login bằng username/email/code

---

## 🐛 Troubleshooting

### Không thể đăng nhập Admin
- Kiểm tra tài khoản tồn tại trong `admins` table
- Kiểm tra password hash hoặc dùng `123456`
- Kiểm tra `status = 'active'`

### Không thể đăng nhập Guide
- Kiểm tra tài khoản tồn tại trong `guides` table
- Kiểm tra `status = 'active'`
- Dùng `guide_code` hoặc `username`
- Kiểm tra password hash hoặc dùng `123456`

### Session bị mất sau logout
- Đảm bảo `?act=logout` được gọi
- Xóa cookies của browser nếu cần
- Check file `AuthController.php` phương thức `logout()`

---

## 📞 Liên Hệ
Nếu gặp vấn đề, kiểm tra logs hoặc contact developer.
