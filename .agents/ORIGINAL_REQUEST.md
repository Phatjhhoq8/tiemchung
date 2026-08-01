# Original User Request

## Initial Request — 2026-07-31T15:29:18Z

<USER_REQUEST>
Dự án thực hiện tái cấu trúc (refactor) hệ thống tiêm chủng Medicare từ mã nguồn Laravel hiện tại, tập trung vào Giai đoạn 1: Chuẩn hóa tài khoản quản trị, thiết lập phân quyền đa chi nhánh (RBAC), tách biệt yêu cầu tư vấn (CRM Leads), phòng chống XSS/SVG upload và làm sạch database schema.

Working directory: /home/hongphuoc/Desktop/thue
Integrity mode: development

## Requirements

### R1. Chuẩn hóa Tài khoản và Bảo mật Admin (Phần 1)
- Xóa bỏ hoàn toàn cơ chế tự động tạo tài khoản super admin mặc định trong controller. Thay vào đó, tạo một Artisan command `php artisan admin:create` cho phép tạo tài khoản admin thủ công với các tham số nhập vào an toàn.
- Cập nhật cấu trúc bảng `users` (được phép sửa đổi trực tiếp migration hiện tại hoặc thêm mới): bổ sung các trường `status`, `must_change_password`, `password_changed_at`, `last_login_at`, `locked_until`, `failed_login_count` để quản lý vòng đời tài khoản.
- Tích hợp ghi log khi đăng nhập thành công, đăng nhập thất bại và tự động khóa tài khoản tạm thời nếu đăng nhập sai quá số lần quy định.

### R2. Phân quyền và Dữ liệu Đa chi nhánh (Phần 2)
- Tách biệt rõ ràng dữ liệu hệ thống (Vaccine Master) và dữ liệu chi nhánh địa phương (giá, trạng thái kho, lịch hẹn của chi nhánh đó trong `center_vaccines`).
- Triển khai Laravel Policies để kiểm soát truy cập (Access Control) ở tầng server:
  - Chỉ tài khoản `super_admin` mới có quyền tạo mới, chỉnh sửa thông tin gốc hoặc xóa vắc xin khỏi hệ thống.
  - Tài khoản `branch_admin` chỉ được phép quản lý dữ liệu vắc xin địa phương thuộc chi nhánh của mình (`price`, `sale_price`, `stock_status`, `is_featured`, `sort_order` trong bảng `center_vaccines`).
  - Chặn đứng mọi hành vi truy cập chéo dữ liệu giữa các chi nhánh (chống IDOR). Nếu `branch_admin` của chi nhánh A cố tình gửi request xem/sửa dữ liệu chi nhánh B, hệ thống phải trả về lỗi `403 Forbidden`.

### R3. Tách biệt Yêu cầu Tư vấn & Chuẩn hóa Đăng ký (Phần 3 & Phần 4)
- Tạo bảng `consultation_leads` riêng biệt để lưu trữ các yêu cầu tư vấn gửi từ người dùng. Không lưu chung dữ liệu tư vấn vào bảng đăng ký tiêm (`registrations`) và không sử dụng dữ liệu giả (như ngày sinh giả) trong cơ sở dữ liệu.
- Chuẩn hóa cấu trúc bảng trung gian `registration_vaccines` để đảm bảo cột `quantity` hoạt động ổn định và được ánh xạ chính xác qua Eloquent relationship pivot của Model `Registration` và `Vaccine`.

### R4. Bảo mật Nội dung và Chặn tệp tin nguy hại (Phần 10)
- Xây dựng lớp tiện ích làm sạch HTML bài viết để loại bỏ các đoạn mã script độc hại (Stored XSS) trước khi lưu bài viết vào CSDL.
- Chặn tải lên định dạng `.svg` ở tất cả các form tải ảnh lên trong hệ thống quản trị (chỉ cho phép các định dạng ảnh raster an toàn như JPG, PNG, WEBP).
- Lọc bỏ các scheme URL nguy hiểm (`javascript:`, `data:`) đối với các trường liên kết banner và địa chỉ nhúng bản đồ chi nhánh.
- Áp dụng làm sạch dữ liệu để tránh lỗi CSV/Formula Injection khi xuất dữ liệu đăng ký tiêm chủng ra tệp CSV.

## Acceptance Criteria

### Security & Access Control
- [ ] Không tồn tại tài khoản mặc định `admin/admin123` được tự động tạo trong mã nguồn khi chạy ứng dụng.
- [ ] Khi đăng nhập sai quá 5 lần liên tiếp, tài khoản admin sẽ bị khóa tạm thời và ghi nhận nhật ký bảo mật.
- [ ] Admin chi nhánh A gửi request AJAX hoặc tải trang chỉnh sửa dữ liệu vắc xin/đăng ký của chi nhánh B sẽ nhận lỗi `403 Forbidden`.
- [ ] Admin chi nhánh không thể chỉnh sửa thông tin Tên, Xuất xứ, Phân loại của vắc xin trong danh mục gốc.
- [ ] Các tệp SVG bị từ chối khi thực hiện tải lên hình ảnh vắc xin, bài viết hoặc banner.

### Data Integrity & REST APIs
- [ ] Yêu cầu tư vấn mới từ khách hàng được lưu vào bảng `consultation_leads` mà không tạo thêm bản ghi giả nào trong bảng `registrations`.
- [ ] Mọi trường hợp in dữ liệu bài viết ra giao diện khách hàng thông qua `{!! $article->content !!}` đều được làm sạch trước đó, loại bỏ toàn bộ các thẻ `<script>`, `onload`, `onerror` hoặc liên kết `javascript:`.
- [ ] Dữ liệu xuất bản ghi đăng ký tiêm chủng ra CSV được làm sạch để vô hiệu hóa các ký tự bắt đầu bằng `=`, `+`, `-`, `@`.

### Database & Migrations
- [ ] Chạy thành công lệnh `php artisan migrate:fresh --seed` mà không phát sinh bất kỳ lỗi cú pháp hoặc ràng buộc nào.
</USER_REQUEST>

## Follow-up — 2026-07-31T16:35:44Z

<USER_REQUEST>
Tái cấu trúc hệ thống tiêm chủng Medicare từ Giai đoạn 1 đến Giai đoạn 6 (RBAC, Leads, Slots, Kho lô, Webhook, Quy trình tiêm) sử dụng phong cách Ponytail (tối giản, hiệu quả, không over-engineer).

Working directory: /home/hongphuoc/Desktop/thue
Integrity mode: development

## Requirements (Ponytail Style - Minimal & Efficient)

### R1. Phân quyền Admin & Ghi Nhật ký Bảo mật (Phase 1 & 13)
- **Soft Delete / Ngừng hoạt động**: Ngăn chặn hard delete đối với các tài nguyên chính (`vaccines`, `centers`, `users`, `banners`, `articles`). Sử dụng trạng thái `is_active = false` hoặc `status = 'inactive'` (YAGNI soft deletes nếu có thể chuyển trạng thái đơn giản).
- **Audit Logs**: Tạo bảng `audit_logs` tối giản lưu lại các thay đổi dữ liệu nhạy cảm: actor_id, center_id, action, resource_type, resource_id, old_values, new_values, ip_address, user_agent. Tích hợp ghi log tự động khi thay đổi giá vắc xin, nhập xuất kho, thay đổi trạng thái đơn tiêm, hoàn tiền.

### R2. Tách Yêu cầu Tư vấn & Chuẩn hóa Giao dịch (Phase 2, 3, 5)
- **CRM Leads**: Tạo bảng `consultation_leads` để lưu thông tin tư vấn độc lập (name, phone, source, status, note, center_id). Không tạo bệnh nhân giả hay registration giả khi khách hàng chỉ gửi yêu cầu tư vấn.
- **Chuẩn hóa Đăng ký**: Cấu trúc bảng `registrations` đại diện cho một giao dịch. Liên kết qua bảng pivot `registration_vaccines` có đầy đủ cột `quantity` và `price`.
- **Chống gửi trùng (Idempotency)**: Backend kiểm tra mã `idempotency_key` gửi từ frontend để ngăn tạo đơn lặp khi người dùng bấm đúp.

### R3. Lịch hẹn, Khung giờ và Công suất (Phase 6)
- **Slots & Appointments**: Thiết lập bảng `schedules` và `slots` (start_at, end_at, capacity, reserved_count).
- **Concurrency Control**: Sử dụng `DB::transaction` và `lockForUpdate()` trên Slot khi khách hàng chọn giờ để đảm bảo không đặt quá công suất (`capacity`) trong các request đồng thời.

### R4. Quản lý Kho theo Lô (Phase 8)
- **Inventory Lots & Movements**: Quản lý tồn kho theo lô qua bảng `inventory_lots` (lot_number, expires_at, available_quantity, status). Lưu mọi biến động kho qua `stock_movements`.
- **FEFO (First Expired First Out)**: Logic xuất kho tự động chọn lô gần hết hạn trước. Ngăn chặn xuất các lô hết hạn hoặc bị thu hồi/cách ly.
- **Stock Reservation**: Khi đơn tiêm ở trạng thái `pending`, thực hiện giữ tồn kho tạm thời (reservation), giải phóng (release) nếu đơn bị hủy hoặc hết hạn thanh toán.

### R5. Hồ sơ Bệnh nhân & Quy trình Tiêm chủng (Phase 9)
- **Patient History**: Tạo bảng `patients` quản lý hồ sơ tập trung (không sao chép thông tin bệnh nhân vào từng đơn đăng ký).
- **Quy trình 3 bước**: Check-in (`checked_in`), Sàng lọc chuyên môn (`eligible` / `deferred` / `contraindicated`), và Thực hiện tiêm (lưu `administered_doses` ghi rõ vắc xin, số lô, người tiêm, thời gian quan sát sau tiêm).

### R6. Webhook Thanh toán & Tác vụ Nền (Phase 7 & 14)
- **Payment Webhook**: Chỉ xác nhận đơn hàng thành công (`paid`) khi nhận được server-to-server webhook hợp lệ (xác thực chữ ký, số tiền), không tin tưởng return URL từ trình duyệt.
- **Queue Jobs**: Di chuyển luồng gửi Email/SMS thông báo sang hàng đợi (background queue jobs) để không làm nghẽn luồng xử lý transaction chính.

## Acceptance Criteria

### Security & Compliance
- [ ] Mọi hoạt động thay đổi cấu hình gốc (Vaccine master) hoặc phân quyền đều được ghi nhận vào `audit_logs`.
- [ ] Admin chi nhánh không thể truy cập hoặc thao tác trên dữ liệu (đơn hàng, tồn kho) của chi nhánh khác (chặn chéo IDOR, trả về `403 Forbidden`).
- [ ] Không cho phép upload tệp ảnh định dạng `.svg` trên tất cả các form admin.

### Concurrency & Data Integrity
- [ ] Đặt lịch đồng thời cho slot cuối cùng không bị vượt quá công suất (capacity).
- [ ] Thực hiện trừ/cộng tồn kho chính xác và an toàn thông qua database lock, không xảy ra race condition.
- [ ] Gửi trùng request đăng ký với cùng `idempotency_key` chỉ tạo duy nhất 1 đơn.
- [ ] Tồn kho tự động ưu tiên xuất lô hết hạn trước (FEFO) và từ chối các lô không khả dụng.

### Tests & Migrations
- [ ] Chạy thành công `/opt/lampp/bin/php artisan migrate:fresh --seed` trên database trắng mà không phát sinh lỗi.
- [ ] Toàn bộ các test suite mới viết cho phân quyền, kho theo lô, slots và webhook chạy thành công.
</USER_REQUEST>

## Follow-up — 2026-08-01T03:27:59Z

<USER_REQUEST>
# Teamwork Project Prompt — Draft

> Status: Launched
> Goal: Tái cấu trúc hệ thống tiêm chủng Medicare từ Giai đoạn 1 đến Giai đoạn 6 (RBAC, Leads, Slots, Kho lô, Webhook, Quy trình tiêm) sử dụng phong cách Ponytail (tối giản, hiệu quả, không over-engineer).

Working directory: /home/hongphuoc/Desktop/thue
Integrity mode: development

## Requirements (Ponytail Style - Minimal & Efficient)

### R1. Phân quyền Admin & Ghi Nhật ký Bảo mật (Phase 1 & 13)
- **Soft Delete / Ngừng hoạt động**: Ngăn chặn hard delete đối với các tài nguyên chính (`vaccines`, `centers`, `users`, `banners`, `articles`). Sử dụng trạng thái `is_active = false` hoặc `status = 'inactive'` (YAGNI soft deletes nếu có thể chuyển trạng thái đơn giản).
- **Audit Logs**: Tạo bảng `audit_logs` tối giản lưu lại các thay đổi dữ liệu nhạy cảm: actor_id, center_id, action, resource_type, resource_id, old_values, new_values, ip_address, user_agent. Tích hợp ghi log tự động khi thay đổi giá vắc xin, nhập xuất kho, thay đổi trạng thái đơn tiêm, hoàn tiền.

### R2. Tách Yêu cầu Tư vấn & Chuẩn hóa Giao dịch (Phase 2, 3, 5)
- **CRM Leads**: Tạo bảng `consultation_leads` để lưu thông tin tư vấn độc lập (name, phone, source, status, note, center_id). Không tạo bệnh nhân giả hay registration giả khi khách hàng chỉ gửi yêu cầu tư vấn.
- **Chuẩn hóa Đăng ký**: Cấu trúc bảng `registrations` đại diện cho một giao dịch. Liên kết qua bảng pivot `registration_vaccines` có đầy đủ cột `quantity` và `price`.
- **Chống gửi trùng (Idempotency)**: Backend kiểm tra mã `idempotency_key` gửi từ frontend để ngăn tạo đơn lặp khi người dùng bấm đúp.

### R3. Lịch hẹn, Khung giờ và Công suất (Phase 6)
- **Slots & Appointments**: Thiết lập bảng `schedules` và `slots` (start_at, end_at, capacity, reserved_count).
- **Concurrency Control**: Sử dụng `DB::transaction` và `lockForUpdate()` trên Slot khi khách hàng chọn giờ để đảm bảo không đặt quá công suất (`capacity`) trong các request đồng thời.

### R4. Quản lý Kho theo Lô (Phase 8)
- **Inventory Lots & Movements**: Quản lý tồn kho theo lô qua bảng `inventory_lots` (lot_number, expires_at, available_quantity, status). Lưu mọi biến động kho qua `stock_movements`.
- **FEFO (First Expired First Out)**: Logic xuất kho tự động chọn lô gần hết hạn trước. Ngăn chặn xuất các lô hết hạn hoặc bị thu hồi/cách ly.
- **Stock Reservation**: Khi đơn tiêm ở trạng thái `pending`, thực hiện giữ tồn kho tạm thời (reservation), giải phóng (release) nếu đơn bị hủy hoặc hết hạn thanh toán.

### R5. Hồ sơ Bệnh nhân & Quy trình Tiêm chủng (Phase 9)
- **Patient History**: Tạo bảng `patients` quản lý hồ sơ tập trung (không sao chép thông tin bệnh nhân vào từng đơn đăng ký).
- **Quy trình 3 bước**: Check-in (`checked_in`), Sàng lọc chuyên môn (`eligible` / `deferred` / `contraindicated`), và Thực hiện tiêm (lưu `administered_doses` ghi rõ vắc xin, số lô, người tiêm, thời gian quan sát sau tiêm).

### R6. Webhook Thanh toán & Tác vụ Nền (Phase 7 & 14)
- **Payment Webhook**: Chỉ xác nhận đơn hàng thành công (`paid`) khi nhận được server-to-server webhook hợp lệ (xác thực chữ ký, số tiền), không tin tưởng return URL từ trình duyệt.
- **Queue Jobs**: Di chuyển luồng gửi Email/SMS thông báo sang hàng đợi (background queue jobs) để không làm nghẽn luồng xử lý transaction chính.

## Acceptance Criteria

### Security & Compliance
- [ ] Mọi hoạt động thay đổi cấu hình gốc (Vaccine master) hoặc phân quyền đều được ghi nhận vào `audit_logs`.
- [ ] Admin chi nhánh không thể truy cập hoặc thao tác trên dữ liệu (đơn hàng, tồn kho) của chi nhánh khác (chặn chéo IDOR, trả về `403 Forbidden`).
- [ ] Không cho phép upload tệp ảnh định dạng `.svg` trên tất cả các form admin.

### Concurrency & Data Integrity
- [ ] Đặt lịch đồng thời cho slot cuối cùng không bị vượt quá công suất (capacity).
- [ ] Thực hiện trừ/cộng tồn kho chính xác và an toàn thông qua database lock, không xảy ra race condition.
- [ ] Gửi trùng request đăng ký với cùng `idempotency_key` chỉ tạo duy nhất 1 đơn.
- [ ] Tồn kho tự động ưu tiên xuất lô hết hạn trước (FEFO) và từ chối các lô không khả dụng.

### Tests & Migrations
- [ ] Chạy thành công `/opt/lampp/bin/php artisan migrate:fresh --seed` trên database trắng mà không phát sinh lỗi.
- [ ] Toàn bộ các test suite mới viết cho phân quyền, kho theo lô, slots và webhook chạy thành công.
</USER_REQUEST>
