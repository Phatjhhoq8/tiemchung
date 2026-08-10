# Original User Request

## 2026-08-10T05:26:57Z

Thiết kế và triển khai giao diện Quản lý lịch & Khung giờ tiêm chủng mới dạng Bảng Lịch Tuần (Weekly Calendar Grid) chia làm 7 cột tương ứng từ Thứ 2 đến Chủ nhật, hỗ trợ sao chép nhanh (Copy) toàn bộ khung giờ của một ngày sang các ngày khác, và chỉnh sửa/xóa trực tiếp các khung giờ/lịch hẹn.

Working directory: /home/hongphuoc/Desktop/thue
Integrity mode: development

## Requirements

### R1. Giao diện Bảng Lịch Tuần (Weekly Calendar Grid)
* Thay đổi màn hình quản lý lịch tiêm hiện tại index.blade.php (file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/schedules/index.blade.php) thành dạng bảng chia làm 7 cột song song đại diện cho 7 ngày trong tuần (Thứ 2 đến Chủ nhật) của tuần được chọn.
* Mỗi cột của ngày phải hiển thị rõ ràng:
  * Ngày tháng (định dạng dd/m/yyyy).
  * Trạng thái Đóng/Mở lịch của ngày đó kèm nút chuyển đổi trạng thái nhanh.
  * Danh sách các khung giờ hoạt động. Mỗi khung giờ hiển thị khoảng thời gian, công suất thực tế (ví dụ: 0/12) và một nút bút chì nhỏ để mở modal chỉnh sửa/xóa khung giờ đó.
  * Nút "Xóa lịch ngày" để gỡ bỏ toàn bộ lịch của ngày đó.
  * Form hoặc nút nhanh cho phép "Thêm khung giờ" trực tiếp cho ngày tương ứng.
* Bổ sung thanh điều hướng tuần ở phía trên (Tuần trước, Tuần hiện tại, Tuần sau, hoặc chọn tuần bất kỳ) để lọc lịch.

### R2. Tính năng Sao chép lịch (Copy Schedule)
* Cho phép người dùng sao chép toàn bộ danh sách khung giờ và công suất của một ngày sang một hoặc nhiều ngày khác trong tuần/tháng.
* Giao diện cung cấp nút "Sao chép lịch" ở mỗi cột ngày. Khi click, hiển thị form lựa chọn các ngày đích (ví dụ: checklist các thứ trong tuần hoặc chọn khoảng ngày) để áp dụng cấu hình khung giờ của ngày gốc sang các ngày đích đó.
* Có thông báo xác nhận và kiểm tra nếu ngày đích đã có khách hàng đặt lịch tiêm để tránh ghi đè làm mất lịch hẹn của khách.

### R3. Đảm bảo nghiệp vụ và bảo mật
* Giữ nguyên và tương thích tốt với cơ chế tự động sinh lịch từ khung giờ mặc định đã phát triển.
* Tuân thủ nghiêm ngặt phân quyền chi nhánh (Branch Admin chỉ quản lý được chi nhánh của mình; Super Admin có dropdown chọn chi nhánh).
* Đảm bảo các hoạt động AJAX / Form Submit diễn ra mượt mà, không tải lại trang không cần thiết (SPA experience).

---

## Acceptance Criteria

### Giao diện Bảng Lịch Tuần
- [ ] Truy cập /admin/schedules hiển thị giao diện 7 cột song song của tuần hiện tại thay vì danh sách cuộn dọc.
- [ ] Các nút điều hướng tuần hoạt động chính xác, cập nhật đúng danh sách ngày của tuần đó.
- [ ] Bấm chỉnh sửa khung giờ (icon bút chì) hiển thị đúng modal với thông tin của khung giờ đó, lưu thay đổi và cập nhật lại giao diện không cần tải lại trang.

### Tính năng Sao chép lịch
- [ ] Bấm nút "Sao chép lịch" hiển thị tùy chọn các ngày đích.
- [ ] Xác nhận sao chép thành công sao chép toàn bộ khung giờ sang ngày đích trong cơ sở dữ liệu.
- [ ] Ngăn chặn việc ghi đè nếu ngày đích đang có khách hàng đặt lịch tiêm (đã có lượt đặt reserved_count > 0).

### Kiểm thử tự động
- [ ] Viết test tự động trong tests/Feature/WeeklyCalendarDashboardTest.php kiểm tra việc hiển thị lịch tuần và sao chép lịch thành công.
- [ ] Toàn bộ test suite vượt qua (Pass 100%) không có lỗi.
