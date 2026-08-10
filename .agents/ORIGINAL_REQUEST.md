# Original User Request

## 2026-08-10T16:04:54Z

Cải tiến trang bảng điều khiển quản trị (Dashboard) cho hệ thống đăng ký tiêm chủng Medicare. Dashboard sẽ tải động toàn bộ số liệu thống kê thực tế từ cơ sở dữ liệu MySQL, tích hợp biểu đồ SVG trực quan và hiển thị số lượng ca tiêm trong ngày.

Working directory: /home/hongphuoc/Desktop/thue
Integrity mode: development

## Requirements

### R1. Tích hợp dữ liệu động thực tế
- Thay thế các giá trị cứng `$consultCount`, `$importedQuantity`, `$soldQuantity` trong [AdminDashboardController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminDashboardController.php).
- `$consultCount`: Tổng số ca yêu cầu tư vấn chưa xử lý (trạng thái `pending` hoặc `new`) từ bảng `consultation_leads`, có lọc theo `center_id`.
- `$importedQuantity`: Tổng số lượng vắc xin hiện có (tổng `available_quantity` + `reserved_quantity` từ bảng `inventory_lots`), có lọc theo `center_id`.
- `$soldQuantity`: Tổng số vắc xin đã bán/tiêm hoàn tất (các registration có `booking_status` = 'completed'), có lọc theo `center_id`.

### R2. Thống kê ca tiêm hôm nay
- Tính toán số ca tiêm dự kiến trong ngày hôm nay (`injection_date` bằng ngày hiện tại) và hiển thị một widget nổi bật trên Dashboard để nhân viên y tế theo dõi, có lọc theo `center_id`.

### R3. Biểu đồ trực quan doanh thu & lượt đăng ký bằng SVG
- Hiển thị biểu đồ thống kê xu hướng doanh thu và số lượng đơn tiêm chủng trong 7 ngày gần nhất hoặc 6 tháng gần nhất.
- Sử dụng SVG thuần kết hợp CSS Tailwind/Vanilla có sẵn để đảm bảo tốc độ tải trang nhanh, không phụ thuộc thư viện JS bên ngoài.
- Tuân thủ nghiêm ngặt bảng màu thương hiệu: Medicare Red (`#c8102e`), Medicare Gold (`#eaaa00`), Medicare Navy (`#004b8f`).

## Acceptance Criteria

### Thống kê chính xác và đồng bộ
- [ ] Số liệu tư vấn, tồn kho, và đã bán được truy vấn và hiển thị động, khớp chính xác với dữ liệu trong database khi lọc theo từng chi nhánh hoặc tất cả chi nhánh.
- [ ] Số ca tiêm dự kiến hôm nay hiển thị đúng theo thời gian thực của máy chủ.

### Giao diện và Biểu đồ
- [ ] Biểu đồ SVG hiển thị đúng tỷ lệ, trực quan, có chú thích đầy đủ cho các cột hoặc đường dữ liệu.
- [ ] Toàn bộ Dashboard đáp ứng chuẩn responsive trên di động và PC, không làm vỡ giao diện.
- [ ] Sử dụng đúng hệ màu thương hiệu được định nghĩa trong [AGENTS.md](file:///home/hongphuoc/Desktop/thue/.agents/AGENTS.md).
