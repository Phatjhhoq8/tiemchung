# Báo Cáo Thay Đổi Hệ Thống So Với Lần Push Trước

Báo cáo này chi tiết các thay đổi trong mã nguồn hệ thống **Medicare** kể từ commit/push cuối cùng vào ngày **28/07/2026** (Commit ID: `b9a7cb95d977da8423e9b5b9ee2b9d45a9440f6d`) đến ngày hôm nay **31/07/2026**.

Các thay đổi tập trung vào việc chuyển đổi hệ thống sang mô hình **Đa Chi Nhánh (Branch Context)**, bổ sung tính năng quản lý tồn kho chi nhánh, phân quyền tài khoản quản trị và tinh chỉnh trải nghiệm giao diện người dùng.

---

## 1. Cơ Sở Dữ Liệu (Database Migrations & Seeders)

Hệ thống đã bổ sung 5 migrations mới để hỗ trợ dữ liệu đa chi nhánh:
- **`2026_07_31_000001_extend_centers_for_branch_context.php`**: Mở rộng bảng `centers` thêm các trường: `slug`, `zalo_phone`, `map_url` (bản đồ nhúng), `working_hours`, và `sort_order`.
- **`2026_07_31_000002_create_center_vaccines_table.php`**: Tạo bảng trung gian `center_vaccines` liên kết giữa vắc xin và chi nhánh, cho phép cấu hình giá riêng biệt, trạng thái tồn kho riêng biệt cho từng chi nhánh.
- **`2026_07_31_000003_add_center_id_to_registrations_table.php`**: Thêm cột `center_id` vào bảng `registrations` để theo dõi chính xác đơn đăng ký thuộc chi nhánh nào.
- **`2026_07_31_000004_add_admin_fields_to_users_table.php`**: Thêm các trường phân quyền cho bảng `users` (`role`, `center_id`).
- **`2026_07_31_000005_create_vaccine_stock_movements_table.php`**: Tạo bảng lưu lịch sử nhập/xuất và điều chỉnh kho vắc xin theo chi nhánh.

**Seeder cập nhật**:
- [CenterSeeder.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Database/Seeders/CenterSeeder.php) đã được cập nhật thêm đầy đủ link bản đồ nhúng Google Maps thực tế cho 4 chi nhánh: Medicare Cờ Đỏ, Medicare Thới Lai, Medicare Phong Điền, và Medicare Trà Nóc.

---

## 2. Xử Lý Logic Backend (Controllers & Models)

- **`CenterContext.php` (Support Helper)**: Được xây dựng mới để quản lý chi nhánh hiện hành của khách hàng trong Session và tự động điều chỉnh giỏ hàng tương ứng khi chuyển đổi chi nhánh.
- **`HomeController.php` & `VaccineController.php`**: 
  - Cập nhật logic tải danh sách vắc xin lẻ và gói vắc xin theo ngữ cảnh chi nhánh hiện tại (chỉ tải vắc xin hoạt động và có sẵn tại chi nhánh đó).
  - Tích hợp lọc sản phẩm theo chi nhánh trong trang danh mục vắc xin.
- **`AdminDashboardController.php` & `AdminRegistrationController.php`**:
  - Hỗ trợ lọc đơn đăng ký tiêm chủng theo chi nhánh mà quản trị viên quản lý.
  - Phân quyền dữ liệu: Nhân viên chi nhánh chỉ xem được đơn đăng ký của chi nhánh mình, trong khi Super Admin có quyền xem toàn bộ hệ thống.
- **`AdminStockController.php` & `AdminUserController.php` (Mới)**:
  - Thêm tính năng quản lý nhập/xuất kho vắc xin tại từng chi nhánh.
  - Thêm tính năng tạo và quản lý tài khoản nhân viên cho từng chi nhánh.

---

## 3. Cải Tiến Giao Diện & Trải Nghiệm (Client & Admin Frontend)

### Trang Chọn Chi Nhánh (`contact.blade.php`)
- Tích hợp khung bản đồ Google Maps tương ứng vào từng thẻ thông tin chi nhánh.
- **Chỉnh sửa nút "Chọn chi nhánh này"**: Thiết kế lại với viền đỏ `#c8102e` chữ đỏ trên nền trắng (`#ffffff`), khi di chuột (hover) sẽ tự động đảo ngược sang nền đỏ chữ trắng mang lại độ tương phản tốt và cảm giác chuyên nghiệp.

### Header Dropdown (`layouts/app.blade.php`)
- Thiết kế lại danh sách chọn chi nhánh ở header:
  - Loại bỏ các inline styles tĩnh cũ.
  - Thêm hiệu ứng hover đổi màu nền mượt mà cho các mục.
  - Làm nổi bật chi nhánh đang được chọn (Active) bằng màu nền đỏ/hồng nhạt (`rgba(200, 16, 46, 0.08)`) và màu chữ đỏ thương hiệu.

### Hệ Thống Toast Thông Báo Mới
- Loại bỏ các khối Alert tĩnh chiếm dụng không gian ở đầu trang.
- Thay thế hoàn toàn bằng **Toast thông báo nổi** ở góc trên bên phải màn hình. Toast hỗ trợ hiệu ứng mờ kính (glassmorphism), hiệu ứng trượt vào mượt mà (`slideIn`), tự động đóng sau 4 giây (`fadeOut`) và có nút đóng thủ công.

### Giao Diện Quản Trị (Admin Layouts)
- Cập nhật sidebar của trang admin để tích hợp các tính năng mới: Quản lý kho vắc xin (`Stock`), Quản lý tài khoản nhân viên chi nhánh (`Users`).
- Bảo vệ các tuyến đường cấu hình bằng middleware `SuperAdminOnly`.

---

## 4. Thống Kê File Thay Đổi
Tổng cộng có **27 file đã thay đổi** trong thư mục module và gốc dự án, với **967 dòng code được thêm mới** và **291 dòng code được lược bỏ/tối ưu**. 

*Để xem toàn bộ mã nguồn thay đổi chi tiết, bạn có thể thực hiện lệnh `git diff b9a7cb95d977da8423e9b5b9ee2b9d45a9440f6d` tại terminal.*
