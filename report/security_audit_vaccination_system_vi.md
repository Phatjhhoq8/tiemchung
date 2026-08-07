# BÁO CÁO KIỂM TOÁN BẢO MẬT VÀ LUỒNG NGHIỆP VỤ ĐẦU CUỐI

## Hệ thống đặt lịch tiêm chủng

**Ngày đánh giá:** 07/08/2026  
**Phạm vi:** Mã nguồn trong workspace `C:\Users\Admin\Desktop\tiemchung`  
**Phương pháp:** Phân tích tĩnh mã nguồn, định tuyến, controller, model, migration, cấu hình, kiểm thử hiện có và kiểm tra dependency.  
**Giới hạn:** Chưa thực hiện kiểm thử xâm nhập trên môi trường triển khai thật; không kiểm chứng được WAF/CDN, quyền tài khoản MySQL, mã hóa ổ đĩa/sao lưu, cấu hình TLS bên ngoài repository hoặc dữ liệu đang tồn tại trong cơ sở dữ liệu.

---

# A. Tóm tắt điều hành

1. Hệ thống là ứng dụng Laravel 11 dùng Blade, MySQL và session phía máy chủ; không có API token/JWT hoặc cổng thanh toán trực tuyến.
2. Điểm yếu nghiêm trọng nhất đang truy cập được là tra cứu lịch hẹn chỉ bằng số điện thoại, làm lộ thông tin bệnh nhân, lịch tiêm, vắc xin, số tiền và trạng thái thanh toán mà không có OTP hoặc bằng chứng sở hữu.
3. `POST /register` không có rate limit, CAPTCHA hoặc giới hạn số bệnh nhân trong một yêu cầu, cho phép tự động chiếm toàn bộ khung giờ và tạo lượng lớn dữ liệu rác.
4. Cơ chế idempotency của đặt lịch bị sai: hệ thống tìm khóa gốc nhưng lưu khóa có hậu tố theo bệnh nhân, nên gửi lại yêu cầu có thể tạo lịch trùng hoặc trả lỗi thay vì trả kết quả cũ.
5. Đặt lịch và thực hiện tiêm không dự trữ hoặc trừ tồn kho vắc xin; hai hệ thống tồn kho độc lập có thể sai lệch và cho phép nhận lịch vượt số liều thực tế.
6. Luồng trạng thái lịch hẹn cho phép các chuyển trạng thái không hợp lệ; có thể hoàn tất lịch chỉ dựa trên thanh toán mà không cần check-in, sàng lọc hoặc bản ghi liều tiêm.
7. Vai trò `branch_admin` có phạm vi quyền quá rộng và có một lỗi rò rỉ lịch sử điểm liên chi nhánh; các policy đã đăng ký nhưng không được gọi trực tiếp.
8. Controller bệnh nhân và quy trình tiêm hiện chưa được định tuyến. Nếu được bật nguyên trạng, chúng tạo rủi ro nghiêm trọng về truy cập hồ sơ y tế liên chi nhánh, chọn sai lô/vắc xin và ghi liều trùng.
9. Cấu hình workspace chứa thông tin xác thực có vẻ đang hoạt động và bật debug; báo cáo không ghi lại giá trị bí mật. Cần luân chuyển bí mật và kiểm chứng cấu hình production.
10. Dependency đang khóa có advisory mức cao/trung bình đối với Laravel, Guzzle, CommonMark và PostCSS; cần nâng cấp và đưa kiểm tra advisory vào CI.

---

# B. Kiến trúc hệ thống

## B.1 Sơ đồ kiến trúc

```text
Khách / Bệnh nhân
       |
       | Form, số điện thoại, PII, vaccine_id, slot_id, center_id
       v
Trình duyệt + Laravel Blade + JavaScript
       |
       | Cookie session + CSRF
       v
Laravel Web Routes
       |
       +--> Public Controllers
       |      +--> Đặt lịch / tra cứu / giỏ hàng / tư vấn
       |
       +--> admin.auth --> Branch Admin / Super Admin
                              |
                              +--> Lịch hẹn / khách hàng / điểm
                              +--> Lịch / khung giờ
                              +--> Tồn kho / lô vắc xin
                              +--> CMS / người dùng / cấu hình
       |
       v
Controller / Support / Service
       |
       +--> Eloquent Models --> MySQL từ xa
       |                         +--> users / sessions / cache / jobs
       |                         +--> registrations / patients
       |                         +--> schedules / slots
       |                         +--> center_vaccines
       |                         +--> inventory_lots
       |                         +--> point_transactions / audit_logs
       |
       +--> Public image storage dưới webroot
       |
       +--> CDN trình duyệt / Google Maps / Zalo
```

## B.2 Thành phần đã xác định

| Thành phần | Triển khai | Bằng chứng chính |
|---|---|---|
| Frontend | Blade, JavaScript, Vite | `modules/VaccineRegistration/resources/views`, `public/js/app.js` |
| Backend | Laravel 11, PHP 8.2 | `composer.json`, `public/index.php` |
| Xác thực | Session admin tùy biến | `AdminAuthController.php`, `AdminAuth.php` |
| Phân quyền | `admin.auth`, `super.admin`, `AdminContext` | `routes/web.php:78-147` |
| Database | MySQL, Eloquent | `config/database.php:17-48` |
| Session/cache/queue | Database | `config/session.php`, `config/cache.php`, `config/queue.php` |
| Đặt lịch | `registrations`, `schedules`, `slots` | Các migration trong module |
| Tồn kho tổng hợp | `center_vaccines`, `vaccine_stock_movements` | Migration ngày 31/07/2026 |
| Tồn kho theo lô | `inventory_lots`, `stock_movements` | Migration ngày 01/08/2026 |
| Thanh toán | Xác nhận thủ công, hoàn tiền, điểm thưởng | `app/Services/RegistrationPaymentService.php` |
| Hồ sơ y tế | `patients`, `administered_doses` | Migration quy trình tiêm ngày 01/08/2026 |
| Upload | Ghi ảnh trực tiếp dưới `public/images` | Các admin controller upload ảnh |
| Email/SMS | Không tìm thấy triển khai đang dùng | Không có job/notification/service tương ứng |
| Cổng thanh toán | Không tồn tại | Không có webhook/callback/API gateway |

## B.3 Biên tin cậy

| Biên tin cậy | Dữ liệu do người dùng kiểm soát |
|---|---|
| Trình duyệt → route công khai | Số điện thoại, PII bệnh nhân, vaccine ID, slot ID, center session, ghi chú, khóa idempotency |
| Trình duyệt admin → route quản trị | Resource ID, center ID, trạng thái, số lượng, điểm, nội dung CMS, file upload |
| Controller → service/model | Trạng thái lịch, thanh toán, giá trị tồn kho, patient/registration linkage |
| Ứng dụng → MySQL từ xa | PII, dữ liệu y tế, mật khẩu băm, session, audit, queue và lỗi job |
| Ứng dụng → public webroot | Ảnh do quản trị viên tải lên |
| Trang web → CDN bên thứ ba | Script, font và tài nguyên có quyền chạy trong origin của ứng dụng |

---

# C. Vai trò và ma trận quyền

Hệ thống chỉ triển khai hai vai trò nhân sự. Không có vai trò bác sĩ, y tá, lễ tân, dược sĩ, thu ngân hoặc kiểm toán viên.

| Tài nguyên/chức năng | Khách/Bệnh nhân | Branch admin | Super admin |
|---|---:|---:|---:|
| Xem vắc xin, bài viết, trung tâm | Có | Có | Có |
| Tạo lịch hẹn | Có, không cần tài khoản | Có | Có |
| Tra cứu lịch bằng số điện thoại | Có | Có | Có |
| Quản lý lịch hẹn | Không | Chi nhánh của mình | Tất cả |
| Xác nhận thanh toán/hoàn tiền | Không | Chi nhánh của mình | Tất cả |
| Xem khách hàng/điểm | Không | Có đăng ký tại chi nhánh | Tất cả |
| Điều chỉnh điểm | Không | Theo phạm vi controller | Tất cả |
| Quản lý lịch/khung giờ | Không | Chi nhánh của mình | Tất cả |
| Quản lý tồn kho/lô | Không | Chi nhánh của mình | Tất cả |
| Quản lý lead | Không | Theo chi nhánh | Tất cả |
| Quản lý trung tâm/người dùng/CMS/settings | Không | Không | Có |
| Check-in/sàng lọc/thực hiện tiêm | Không có route | Không có route | Không có route |

**Đánh giá:** Phân quyền chức năng quá thô. Một `branch_admin` đồng thời có thể xem PII, sửa lịch, xác nhận tiền, hoàn tiền, điều chỉnh điểm, sửa sức chứa và sửa tồn kho. Điều này làm tăng tác động nếu tài khoản bị chiếm đoạt hoặc nhân viên lạm dụng quyền.

---

# D. Bề mặt tấn công

## D.1 Điểm vào công khai quan trọng

| Endpoint | Chức năng | Kiểm soát chính |
|---|---|---|
| `POST /centers/select` | Chọn chi nhánh trong session | CSRF, validation |
| `POST /cart/add`, `/remove`, `/clear` | Thay đổi giỏ hàng | CSRF, không rate limit |
| `POST /consultations`, `/leads` | Tạo lead | CSRF, 10/phút |
| `POST /register` | Tạo một hoặc nhiều lịch hẹn | CSRF, không rate limit |
| `POST /tra-cuu-lich-hen` | Tra cứu lịch bằng số điện thoại | 10/phút, không xác thực sở hữu |
| `POST /admin/login` | Đăng nhập quản trị | 5/phút và khóa tài khoản |

## D.2 Điểm vào đặc quyền

- Quản lý lịch hẹn, trạng thái, thanh toán và hoàn tiền.
- Xem/xuất dữ liệu bệnh nhân và lịch hẹn ra CSV.
- Quản lý điểm khách hàng.
- Quản lý schedule, slot và `reserved_count`.
- Quản lý tồn kho tổng hợp và lô vắc xin.
- Quản lý lead tư vấn.
- Super admin quản lý tài khoản, trung tâm, banner, bài viết và settings.
- Upload ảnh bài viết, banner và vắc xin.

## D.3 Mã nhạy cảm chưa được định tuyến

- `AdminPatientController`: đọc và sửa hồ sơ bệnh nhân tập trung.
- `VaccinationWorkflowController`: check-in, sàng lọc và ghi nhận tiêm.

Hai controller trên được import trong `routes/web.php:27-28` nhưng không có route tương ứng trong `routes/web.php:31-149`.

---

# E. Bảng tổng hợp lỗ hổng

| ID | Mức độ | Lỗ hổng | Thành phần | Tác động | Độ tin cậy |
|---|---|---|---|---|---|
| VAC-001 | HIGH | Tra cứu lịch hẹn chỉ bằng số điện thoại | Public booking lookup | Lộ PII và thông tin y tế/thanh toán | Đã xác nhận |
| VAC-002 | HIGH | Không kiểm soát lạm dụng đặt lịch công khai | Public registration | Chiếm slot, spam dữ liệu, DoS nghiệp vụ | Đã xác nhận |
| VAC-003 | HIGH | Idempotency đặt lịch bị hỏng | Public registration | Lịch trùng, giữ slot trùng | Đã xác nhận |
| VAC-004 | HIGH | Không dự trữ hoặc trừ tồn kho theo vòng đời | Booking/inventory/clinical | Nhận lịch vượt tồn, sai lệch y tế | Đã xác nhận |
| VAC-005 | HIGH | State machine lịch hẹn và lâm sàng không nhất quán | Registration workflow | Hoàn tất giả, bỏ qua quy trình y tế | Đã xác nhận |
| VAC-006 | HIGH | Có thể thanh toán lịch đã hủy và giải phóng slot sai | Payment/refund | Sai trạng thái tiền, sai sức chứa | Đã xác nhận |
| VAC-007 | MEDIUM | Lộ giao dịch điểm liên chi nhánh | Customer admin | Rò rỉ dữ liệu giữa chi nhánh | Đã xác nhận |
| VAC-008 | MEDIUM | Khóa tài khoản gây DoS và lộ trạng thái tài khoản | Admin login | Vô hiệu hóa tài khoản admin | Đã xác nhận |
| VAC-009 | MEDIUM | Đổi mật khẩu không thu hồi session cũ | Admin session | Session bị đánh cắp tiếp tục hoạt động | Đã xác nhận |
| VAC-010 | MEDIUM | Admin sửa trực tiếp `reserved_count` | Slot management | Mở lại slot đầy hoặc tạo sức chứa giả | Đã xác nhận |
| VAC-011 | MEDIUM | Sửa tồn lô không có movement/audit/invariant | Inventory lots | Sai lệch tồn kho không truy vết | Đã xác nhận |
| VAC-012 | MEDIUM | Nhập kho không nguyên tử và hai sổ kho độc lập | Inventory | Lost update, số liệu không khớp | Đã xác nhận |
| VAC-013 | MEDIUM | Policy mật khẩu không nhất quán và không ép đổi mật khẩu | Admin accounts | Tăng rủi ro chiếm tài khoản | Đã xác nhận |
| VAC-014 | MEDIUM | Stored XSS qua chuẩn hóa URL chưa đầy đủ | CMS/banner | Thực thi script trong origin | Có khả năng |
| VAC-015 | HIGH | Bí mật/cấu hình nhạy cảm trong workspace | Deployment config | Truy cập DB/FTP, lộ khóa ứng dụng | Đã xác nhận trong workspace |
| VAC-016 | MEDIUM | Dependency có advisory bảo mật | Supply chain | CRLF, DoS, URL/filter bypass | Đã xác nhận |
| VAC-017 | LOW | Thiếu security headers trong repository | Web configuration | Clickjacking, giảm phòng thủ XSS | Đã xác nhận ở tầng ứng dụng |
| VAC-018 | MEDIUM | Dữ liệu y tế lưu plaintext, TLS DB chưa chứng minh | Data protection | Tăng tác động khi DB bị lộ | Có khả năng |
| VAC-019 | CRITICAL* | Patient controller không có branch scope | Dormant patient workflow | Đọc/sửa hồ sơ y tế toàn hệ thống | Có điều kiện nếu được route |
| VAC-020 | CRITICAL* | Quy trình tiêm thiếu ràng buộc lot/vaccine/center | Dormant clinical workflow | Ghi sai liều, lô, chi nhánh, tồn kho | Có điều kiện nếu được route |

`*` Không phải endpoint khai thác được qua route hiện tại. Mức độ phản ánh tác động nếu controller được bật nguyên trạng.

---

# F. Chi tiết phát hiện

## VAC-001 - Tra cứu lịch hẹn chỉ bằng số điện thoại

**Mức độ:** HIGH  
**Trạng thái:** Đã xác nhận  
**Endpoint:** `POST /tra-cuu-lich-hen`  
**Tệp/hàm:** `VaccineController.php:436-463`, `booking_lookup.blade.php:30-51`  

**Nguyên nhân gốc:** Endpoint công khai chỉ yêu cầu số điện thoại. Không có OTP, mã đăng ký, ngày sinh, session khách hàng hoặc cơ chế chứng minh quyền sở hữu.

**Kịch bản tấn công:** Kẻ tấn công biết hoặc đoán số điện thoại của bệnh nhân, gửi yêu cầu tra cứu và nhận toàn bộ lịch khớp số đó.

**Tác động bảo mật:** Lộ tên bệnh nhân, mã đăng ký, chi nhánh, ngày/giờ, vắc xin, tổng tiền, trạng thái lịch và thanh toán.

**Tác động nghiệp vụ:** Vi phạm riêng tư dữ liệu sức khỏe; tạo rủi ro tuân thủ và mất lòng tin. Rate limit 10/phút chỉ giảm tốc độ, không chứng minh quyền sở hữu và có thể bị phân tán theo IP.

**Bằng chứng:** Route công khai tại `routes/web.php:61-64`; truy vấn theo phone tại `VaccineController.php:449-457`.

**Khuyến nghị:** Dùng OTP có thời hạn, một lần sử dụng và giới hạn thử; hoặc yêu cầu cặp `registration_code + phone` và chỉ trả về đúng một lịch. Không trả dữ liệu thanh toán/y tế trước khi xác minh sở hữu.

## VAC-002 - Không kiểm soát lạm dụng đặt lịch công khai

**Mức độ:** HIGH  
**Trạng thái:** Đã xác nhận  
**Endpoint:** `POST /register`  
**Tệp/hàm:** `routes/web.php:57-60`, `VaccineController.php:469-640`  

**Nguyên nhân gốc:** Không có `throttle`, CAPTCHA, quota theo số điện thoại/thiết bị hoặc giới hạn tối đa cho mảng `patients`.

**Kịch bản tấn công:** Bot gửi một yêu cầu chứa nhiều bệnh nhân hoặc nhiều yêu cầu nối tiếp để giữ toàn bộ sức chứa của các slot.

**Tác động:** Từ chối dịch vụ nghiệp vụ, lịch giả, customer/registration spam, tăng tải transaction và chi phí vận hành.

**Khuyến nghị:** Giới hạn số bệnh nhân mỗi yêu cầu, rate limit theo IP + số điện thoại + session, CAPTCHA sau ngưỡng rủi ro, xác minh OTP trước khi giữ slot và tự động hết hạn lịch chưa xác nhận.

## VAC-003 - Idempotency đặt lịch bị hỏng

**Mức độ:** HIGH  
**Trạng thái:** Đã xác nhận  
**Endpoint:** `POST /register`  
**Tệp/hàm:** `VaccineController.php:511-617`  

**Nguyên nhân gốc:** Hệ thống tìm khóa idempotency gốc nhưng lưu `key_0`, `key_1` theo chỉ số bệnh nhân. Nhánh bắt lỗi unique cũng tiếp tục tìm khóa gốc.

**Kịch bản tấn công/lỗi:** Trình duyệt retry do mất kết nối. Yêu cầu đầu lưu `K_0`; yêu cầu sau tìm `K`, không thấy, rồi cố tạo lại. Với khóa khác hoặc luồng không chạm unique đúng cách, lịch có thể bị nhân đôi; với cùng khóa, hệ thống có thể phát sinh lỗi thay vì trả kết quả cũ.

**Tác động:** Đặt lịch trùng, tăng `reserved_count`, trải nghiệm lỗi và khó đối soát.

**Khuyến nghị:** Tạo bảng request-level idempotency có unique key và lưu toàn bộ response/result set; khóa key trong cùng transaction; ràng buộc key với hash payload; trả lại đúng danh sách registration đã tạo. Middleware idempotency hiện có phải được đăng ký hoặc loại bỏ để tránh hiểu nhầm.

## VAC-004 - Không dự trữ hoặc trừ tồn kho theo vòng đời

**Mức độ:** HIGH  
**Trạng thái:** Đã xác nhận  
**Thành phần:** Public booking, counter booking, inventory, administration  
**Tệp/hàm:** `VaccineController.php:537-610`, `AdminRegistrationController.php:120-178`, `Registration.php:179-215`  

**Nguyên nhân gốc:** Public booking chỉ kiểm tra `stock_status != out_of_stock`; counter booking so sánh `stock_quantity` nhưng không trừ/dự trữ; thực hiện tiêm chỉ tạo `administered_doses` mà không cập nhật lô hoặc movement.

**Kịch bản tấn công:** Nhiều người cùng đặt loại vắc xin có số lượng thấp. Tất cả lịch được chấp nhận vì không có reservation nguyên tử. Nhân viên tiếp tục ghi nhận tiêm mà tồn kho không giảm.

**Tác động bảo mật/nghiệp vụ:** Oversell, thiếu liều tại thời điểm khám, sai số tồn kho, gián đoạn y tế, không truy xuất được lô thực tế.

**Khuyến nghị:** Xác định một nguồn tồn kho chuẩn. Trong transaction đặt lịch, khóa hàng tồn theo center/vaccine/lot, tăng reserved và giảm available bằng cập nhật có điều kiện. Khi tiêm, chuyển reserved thành consumed và ghi movement. Khi hủy/hết hạn, giải phóng reservation. Thêm constraint không âm và kiểm thử race thực sự.

## VAC-005 - State machine lịch hẹn và lâm sàng không nhất quán

**Mức độ:** HIGH  
**Trạng thái:** Đã xác nhận  
**Endpoint:** `PATCH /admin/registrations/{id}/status` và quy trình lâm sàng chưa route  
**Tệp/hàm:** `AdminRegistrationController.php:194-248`, `Registration.php:138-215`  

**Nguyên nhân gốc:** Có nhiều trường trạng thái chồng lấn. Controller quản trị chỉ chặn `completed` khi chưa thanh toán, nhưng không yêu cầu check-in, sàng lọc hoặc administered dose. Model lâm sàng lại chỉ cập nhật `status`, không đồng bộ `booking_status` hoặc `payment_status`.

**Chuyển trạng thái sai có thể xảy ra:**

```text
pending -> no_show
completed -> pending
completed -> confirmed
no_show -> pending
paid -> completed, không có bản ghi liều
administered dose -> status=completed nhưng booking_status=pending/unpaid
```

**Tác động:** Hồ sơ hành chính, thanh toán và y tế mâu thuẫn; có thể báo cáo hoàn tất dù chưa tiêm hoặc ghi nhận tiêm cho lịch chưa hợp lệ.

**Khuyến nghị:** Dùng một state machine phía server với bảng chuyển trạng thái cho phép. `completed` chỉ được thiết lập sau check-in, screening eligible, dose hợp lệ và điều kiện thanh toán phù hợp. Đồng bộ trạng thái trong một transaction và thêm kiểm tra invariant.

## VAC-006 - Có thể thanh toán lịch đã hủy và giải phóng slot sai

**Mức độ:** HIGH  
**Trạng thái:** Đã xác nhận từ logic server  
**Endpoint:** `POST /admin/registrations/{id}/settle`, `POST /admin/registrations/{id}/refund`  
**Tệp/hàm:** `RegistrationPaymentService.php:34-206`  

**Nguyên nhân gốc:** `settle()` kiểm tra payment status nhưng không chặn booking status `cancelled`. UI có thể ẩn nút nhưng service vẫn chấp nhận request trực tiếp.

**Kịch bản:** Admin hủy lịch chưa thanh toán, slot được giải phóng; sau đó gọi trực tiếp endpoint settle, tạo trạng thái `cancelled + paid`; tiếp tục refund và `releaseSlot()` lần nữa. Nếu slot đã được người khác đặt, số đếm của lịch hợp lệ có thể bị giảm.

**Tác động:** Sai đối soát thanh toán/điểm, slot capacity thấp hơn số lịch thực tế, khả năng nhận thêm lịch vượt sức chứa.

**Khuyến nghị:** `settle()` phải chỉ nhận trạng thái cho phép, ví dụ `pending/confirmed`; lưu cờ hoặc ledger cho việc giữ/giải phóng slot để thao tác đúng một lần; ràng buộc transition và refund trong cùng state machine.

## VAC-007 - Lộ giao dịch điểm liên chi nhánh

**Mức độ:** MEDIUM  
**Trạng thái:** Đã xác nhận  
**Endpoint:** `GET /admin/customers/{id}`  
**Tệp/hàm:** `AdminCustomerController.php:42-59`, `admin/customers/show.blade.php:38-59`  

**Nguyên nhân gốc:** Controller xác minh khách có registration tại chi nhánh, nhưng sau đó tải toàn bộ `pointTransactions` mà không lọc `center_id`.

**Tác động:** Branch A có thể xem lịch sử điểm và ghi chú phát sinh ở Branch B nếu khách từng có giao dịch tại Branch A.

**Khuyến nghị:** Scope point transaction theo center cho branch admin hoặc chỉ hiển thị số dư tổng không kèm chi tiết liên chi nhánh. Thêm test negative cho dữ liệu khách hàng đa chi nhánh.

## VAC-008 - Khóa tài khoản gây DoS và lộ trạng thái tài khoản

**Mức độ:** MEDIUM  
**Trạng thái:** Đã xác nhận  
**Endpoint:** `POST /admin/login`  
**Tệp/hàm:** `AdminAuthController.php:52-153`, `User.php:122-130`  

**Nguyên nhân gốc:** Năm mật khẩu sai khóa tài khoản ở cấp tài khoản, trong khi throttle bao gồm IP. Nhiều IP có thể cộng dồn lỗi trên cùng tài khoản. Phản hồi cho tài khoản khóa/vô hiệu hóa khác với tài khoản không tồn tại hoặc sai mật khẩu.

**Tác động:** Kẻ tấn công phân tán có thể khóa lặp lại tài khoản admin và xác định trạng thái một số tài khoản.

**Khuyến nghị:** Trả thông báo đồng nhất; dùng backoff/risk-based challenge thay vì khóa cứng dễ bị lạm dụng; cảnh báo chủ tài khoản; kết hợp giới hạn theo tài khoản, IP, ASN và thiết bị; thêm MFA.

## VAC-009 - Đổi mật khẩu không thu hồi session cũ

**Mức độ:** MEDIUM  
**Trạng thái:** Đã xác nhận  
**Tệp/hàm:** `AdminUserController.php:54-99`, `AdminAuth.php:20-36`, `AdminAuthController.php:159-167`  

**Nguyên nhân gốc:** Middleware chỉ tải lại user và kiểm tra active/locked; không so `password_changed_at` với thời điểm session và không xóa session khác.

**Tác động:** Session admin bị đánh cắp vẫn dùng được sau khi đổi mật khẩu. Logout chỉ hủy session hiện tại.

**Khuyến nghị:** Ghi `password_changed_at`, lưu `authenticated_at/password_version` trong session và từ chối session cũ; xóa các database session của user khi đổi mật khẩu hoặc thay đổi quyền.

## VAC-010 - Admin sửa trực tiếp số lượng slot đã giữ

**Mức độ:** MEDIUM  
**Trạng thái:** Đã xác nhận  
**Endpoint:** `PUT/PATCH /admin/slots/{slot}`  
**Tệp/hàm:** `AdminSlotController.php:81-107`, `Slot.php:12-19`  

**Nguyên nhân gốc:** `reserved_count` được nhận từ client và mass-assign. Kiểm tra capacity dùng giá trị reserved cũ, không bảo đảm payload mới thỏa `reserved_count <= capacity`.

**Tác động:** Mở lại slot đầy, khóa slot trống hoặc đặt reserved vượt capacity; phá vỡ invariant do transaction đặt lịch duy trì.

**Khuyến nghị:** Không cho client sửa `reserved_count`. Tính từ active registrations hoặc chỉ cập nhật qua service giữ/giải phóng slot. Thêm DB check constraint và reconciliation job.

## VAC-011 - Sửa tồn lô không có movement, audit hoặc invariant

**Mức độ:** MEDIUM  
**Trạng thái:** Đã xác nhận  
**Endpoint:** `PUT/PATCH /admin/inventory-lots/{id}`  
**Tệp/hàm:** `AdminInventoryLotController.php:115-140`  

**Nguyên nhân gốc:** `available_quantity` được ghi đè trực tiếp, không transaction, row lock, stock movement hoặc audit. Không kiểm tra với initial/reserved/consumed.

**Tác động:** Nhân viên có thể tạo/xóa số lượng tồn mà không để lại ledger đáng tin cậy; số liệu có thể âm về mặt nghiệp vụ dù validation chỉ chặn giá trị số âm trực tiếp.

**Khuyến nghị:** Thay overwrite bằng adjustment command có reason, movement bất biến, actor và before/after; khóa lot; kiểm tra công thức `initial + imports - consumed - disposed = available + reserved`.

## VAC-012 - Nhập kho không nguyên tử và hai sổ kho độc lập

**Mức độ:** MEDIUM  
**Trạng thái:** Đã xác nhận  
**Tệp/hàm:** `AdminStockController.php:66-91`, `AdminInventoryLotController.php:63-109`  

**Nguyên nhân gốc:** Stock import tạo movement và cập nhật aggregate bằng read-modify-write nhưng không có transaction/lock. Lot inventory cập nhật độc lập với `center_vaccines.stock_quantity`.

**Kịch bản:** Hai yêu cầu nhập kho đồng thời đọc cùng số cũ rồi ghi đè kết quả của nhau; hoặc lỗi giữa tạo movement và cập nhật aggregate làm ledger không khớp.

**Khuyến nghị:** Hợp nhất inventory source of truth; dùng transaction và `lockForUpdate()` hoặc atomic increment; ledger phải là bất biến và aggregate được tính/đối soát từ ledger.

## VAC-013 - Chính sách mật khẩu không nhất quán và không ép đổi mật khẩu

**Mức độ:** MEDIUM  
**Trạng thái:** Đã xác nhận  
**Tệp/hàm:** `AdminUserController.php:91-99`, `CreateAdminCommand.php:96-114`, `User.php:30-33`  

**Nguyên nhân gốc:** Web cho phép mật khẩu tối thiểu 6 ký tự, CLI yêu cầu 8; không kiểm tra mật khẩu rò rỉ. `must_change_password` và `password_changed_at` tồn tại nhưng không được login/middleware thực thi.

**Khuyến nghị:** Dùng một policy tập trung tối thiểu 12 ký tự hoặc passphrase, kiểm tra compromised password, bắt buộc đổi mật khẩu tạm, thêm MFA cho super admin và thao tác nhạy cảm.

## VAC-014 - Khả năng Stored XSS qua URL scheme chuẩn hóa chưa đầy đủ

**Mức độ:** MEDIUM  
**Trạng thái:** Có khả năng; cần kiểm thử browser  
**Thành phần:** Article/banner CMS  
**Tệp/hàm:** `SecurityHelper.php:92-99`, `AdminBannerController.php:53-63,113-123`, `articles/show.blade.php:65-68`  

**Nguyên nhân gốc:** Kiểm tra chỉ phát hiện chuỗi `javascript:`/`data:` liên tục, chưa loại control character trước khi so sánh. Nội dung bài viết được render dạng HTML không escape.

**Điều kiện khai thác:** Cần super admin độc hại/bị chiếm tài khoản hoặc dữ liệu CMS cũ/nhập ngoài sanitizer.

**Khuyến nghị:** Chuẩn hóa URL theo parser, loại control/whitespace, allowlist `https/http` rõ ràng; dùng thư viện sanitizer trưởng thành; thêm CSP; kiểm thử payload có tab/newline/null/control bytes.

## VAC-015 - Bí mật và cấu hình nhạy cảm trong workspace

**Mức độ:** HIGH  
**Trạng thái:** Đã xác nhận trong workspace; chưa xác định bí mật còn hiệu lực  
**Tệp:** `.env` bị ignore  

**Bằng chứng:** Workspace chứa application key, thông tin MySQL từ xa, thông tin FTP và thông tin admin có vẻ sử dụng được. Tệp không được Git track theo trạng thái hiện tại; báo cáo cố ý không ghi giá trị.

**Tác động:** Nếu còn hiệu lực, người có quyền đọc workspace có thể truy cập DB/FTP, giải mã dữ liệu được bảo vệ bằng application key hoặc chiếm tài khoản. FTP port 21 còn có rủi ro truyền plaintext nếu TLS không được ép buộc.

**Khuyến nghị:** Luân chuyển toàn bộ bí mật ngay; kiểm tra lịch sử Git, backup, artifact và log; dùng secret manager; chuyển FTP sang SFTP/FTPS bắt buộc; tách credential theo môi trường và quyền tối thiểu.

## VAC-016 - Dependency có advisory bảo mật

**Mức độ:** MEDIUM  
**Trạng thái:** Đã xác nhận bằng `composer audit --locked` và `npm audit --omit=dev` ngày 07/08/2026  

**Dependency ảnh hưởng:**

| Package | Phiên bản khóa | Advisory nổi bật |
|---|---:|---|
| `guzzlehttp/guzzle` | 7.15.1 | CVE-2026-69246 mức cao, CVE-2026-69245 mức trung bình |
| `laravel/framework` | 11.55.0 | CRLF injection và signed URL path confusion |
| `league/commonmark` | 2.8.3 | Nhiều DoS mức cao và unsafe-link bypass |
| `postcss` | 8.5.19 | Đọc `.map` ngoài ý muốn, mức trung bình |

**Lưu ý:** Khả năng khai thác phụ thuộc chức năng ứng dụng sử dụng; phiên bản bị ảnh hưởng là bằng chứng xác nhận, nhưng không phải mọi advisory đều có đường khai thác trong hệ thống này.

**Khuyến nghị:** Nâng lên phiên bản đã vá tương thích; chạy full test; bật audit trong CI và không tắt advisory blocking trong Composer nếu không có quy trình exception chính thức.

## VAC-017 - Thiếu security headers ở tầng ứng dụng/repository

**Mức độ:** LOW  
**Trạng thái:** Đã xác nhận trong repository; proxy bên ngoài không thể xác định  
**Tệp:** `bootstrap/app.php:14-16`, `public/.htaccess`  

Không tìm thấy CSP, HSTS, `X-Content-Type-Options`, `frame-ancestors`/`X-Frame-Options`, `Referrer-Policy` hoặc `Permissions-Policy`.

**Khuyến nghị:** Thiết lập tại reverse proxy và middleware; bắt đầu với CSP report-only, sau đó nonce/hash cho inline script; bật HSTS chỉ sau khi toàn bộ domain/subdomain dùng HTTPS.

## VAC-018 - Dữ liệu y tế lưu plaintext và TLS database chưa chứng minh

**Mức độ:** MEDIUM  
**Trạng thái:** Có khả năng  
**Tệp:** Migration registrations/patients/administered_doses, `config/database.php:45-47`  

Tên, DOB, điện thoại, địa chỉ, định danh, medical history, screening và observation notes được lưu dưới dạng trường thông thường, không có encrypted cast. DB nằm từ xa nhưng workspace không chứng minh CA/TLS bắt buộc.

**Khuyến nghị:** Bắt buộc TLS có xác thực chứng thư tới MySQL; mã hóa backup và ổ đĩa; cân nhắc field-level encryption cho định danh/medical notes; tách khóa khỏi DB; áp dụng retention và quyền truy cập tối thiểu.

## VAC-019 - Patient controller không có branch scope nếu được bật

**Mức độ:** CRITICAL nếu được route  
**Trạng thái:** Có điều kiện; hiện chưa có route  
**Tệp/hàm:** `AdminPatientController.php:14-127`  

Controller truy vấn bệnh nhân toàn hệ thống, trả registration, administered dose, inventory lot và cho sửa medical history/is_active nhưng không gọi `AdminContext`, policy hoặc center scope.

**Khuyến nghị:** Không route controller trước khi bổ sung role lâm sàng, branch/center scope, policy resource-level, audit truy cập hồ sơ y tế và test cross-branch.

## VAC-020 - Quy trình tiêm thiếu ràng buộc y tế và tồn kho nếu được bật

**Mức độ:** CRITICAL nếu được route  
**Trạng thái:** Có điều kiện; hiện chưa có route  
**Tệp/hàm:** `VaccinationWorkflowController.php:15-115`, `Registration.php:179-215`  

Không có kiểm tra center/ownership/clinical role. `vaccine_id` và `inventory_lot_id` chỉ cần tồn tại toàn cục; không chứng minh vaccine thuộc lịch, lot thuộc vaccine/center, lot còn hạn/active/còn số lượng. Không có transaction, trừ tồn hoặc chống ghi liều trùng.

**Khuyến nghị:** Xây dựng service lâm sàng nguyên tử; khóa registration và lot; kiểm tra role, center, payment/booking/screening state, vaccine được chỉ định, lot hợp lệ/còn hạn/còn liều; thêm unique/idempotency và movement consumption.

---

# G. Đánh giá xác thực

## Kiểm soát tích cực

- Dùng session phía server, không lưu JWT/access token trong `localStorage`.
- Regenerate session ID sau đăng nhập tại `AdminAuthController.php:108-114`.
- Logout invalidate session và regenerate CSRF token tại `AdminAuthController.php:159-167`.
- Middleware tải lại user và chặn tài khoản inactive/locked.
- Có rate limit route và rate limiter theo username/IP.
- Mật khẩu được kiểm tra bằng Laravel `Hash`.

## Khoảng trống

- Không MFA.
- Không có password reset/recovery/verification đang hoạt động.
- Không ép `must_change_password`.
- Đổi mật khẩu không thu hồi session.
- Chính sách mật khẩu yếu và không nhất quán.
- Lockout có thể bị dùng làm DoS.
- `SESSION_SECURE_COOKIE` và HTTPS production chưa được chứng minh chắc chắn.

**Kết luận:** Kiến trúc session cơ bản hợp lý nhưng chưa đủ an toàn cho tài khoản quản trị chứa dữ liệu y tế và quyền hoàn tiền/tồn kho.

---

# H. Đánh giá phân quyền

## Kiểm soát tích cực

- Toàn bộ nhóm admin chính dùng `admin.auth`.
- Chức năng super admin được bọc `super.admin`.
- Nhiều controller registration/vaccine/inventory có kiểm tra center thủ công.
- Middleware kiểm tra lại trạng thái tài khoản mỗi request.

## Vấn đề

- Policy được đăng ký nhưng không tìm thấy nơi gọi `authorize`, `Gate::authorize` hoặc `can:`.
- Enforcement phụ thuộc từng controller, dễ bỏ sót khi thêm endpoint.
- Customer point transaction bị thiếu center scope.
- Vai trò branch admin quá rộng, không có phân tách nhiệm vụ.
- Controller lâm sàng chưa route nhưng hoàn toàn thiếu object-level authorization.

**Kết luận:** Các luồng quản trị đang hoạt động có nhiều kiểm tra branch thủ công, nhưng không thể khẳng định mọi tài nguyên nhạy cảm được bảo vệ nhất quán. Cần chuyển authorization thành policy/middleware bắt buộc và thêm test deny-by-default.

---

# I. Đánh giá luồng đặt lịch

## Luồng hiện tại

```text
Chọn trung tâm trong session
→ Chọn vaccine và slot
→ Nhập một hoặc nhiều bệnh nhân
→ Lock slot
→ Kiểm tra capacity và stock_status
→ Tạo customer/registration/pivot
→ Tăng reserved_count
→ Trạng thái pending + unpaid
```

## Kiểm soát tích cực

- Giá và center-vaccine được giải quyết phía server.
- Slot được `lockForUpdate()` và kiểm tra lại capacity trong transaction.
- Slot phải thuộc center đang chọn và schedule còn hoạt động.
- Vaccine ID có validation và membership theo center.

## Điểm yếu

- Không giới hạn lạm dụng.
- Idempotency bị lỗi.
- Không reserve tồn kho.
- Public flow chỉ dùng `stock_status`, không dùng quantity.
- Không có xác minh số điện thoại trước khi giữ chỗ.
- Không có auto-expiry cho lịch chưa xác nhận.
- State transition cho phép bỏ qua các bước y tế.

**Kết luận:** Chống double booking slot bằng row lock là kiểm soát tốt nhất của hệ thống, nhưng chỉ bảo vệ số đếm slot. Nó không giải quyết booking spam, retry trùng, tồn kho hoặc tính đúng đắn của vòng đời lâm sàng.

---

# J. Đánh giá tồn kho

Hệ thống có hai nguồn số liệu độc lập:

```text
center_vaccines.stock_quantity/status
            và
inventory_lots.available_quantity/reserved_quantity
```

Không có service đồng bộ hoặc quy tắc chứng minh tổng lot bằng aggregate. Booking không reserve; vaccination không consume; cancellation/refund không restock vaccine. Lot có thể sửa trực tiếp mà không movement.

**Kết luận:** Không thể dùng dữ liệu hiện tại để bảo đảm “một lịch hợp lệ tương ứng với một liều có thể cung cấp”. Rủi ro oversell và truy xuất lô sai là cao.

---

# K. Đánh giá dữ liệu bệnh nhân

## Dữ liệu nhạy cảm đã xác định

- Họ tên, ngày sinh, giới tính.
- Điện thoại, địa chỉ, người giám hộ.
- Lịch sử lịch hẹn và vắc xin.
- Trạng thái thanh toán và tổng tiền.
- Số giấy tờ, medical history, screening/observation notes trong schema lâm sàng.

## Rủi ro chính

- Public lookup lộ dữ liệu chỉ bằng phone.
- Customer transaction lộ liên chi nhánh.
- CSV export chứa PII; đã có CSV formula sanitization nhưng vẫn cần kiểm soát tải/xóa/chia sẻ.
- Dữ liệu lưu plaintext; TLS/at-rest/backup không xác minh được.
- Patient dedup theo phone có thể gộp nhầm các thành viên gia đình nếu workflow được sử dụng.
- Audit truy cập hồ sơ y tế chưa có vì patient workflow chưa hoàn thiện.

---

# L. Đánh giá logic nghiệp vụ

| Hành vi mong đợi | Hành vi thực tế | Rủi ro |
|---|---|---|
| Retry không tạo lịch mới | Idempotency key tìm/lưu không cùng định dạng | Lịch và slot trùng |
| Chỉ đặt được khi có liều | Public booking chỉ kiểm tra stock status | Oversell |
| Hoàn tất phải có liều tiêm | Admin chỉ yêu cầu đã thanh toán | Hồ sơ hoàn tất giả |
| Lịch hủy không thể thanh toán | Service settle không chặn cancelled | Trạng thái tiền mâu thuẫn |
| Slot chỉ thay đổi qua booking | Admin có thể sửa `reserved_count` | Capacity giả |
| Tồn kho có ledger đầy đủ | Lot quantity có thể overwrite | Không truy vết được |
| Một chi nhánh chỉ xem dữ liệu của mình | Customer points không scope center | Rò rỉ liên chi nhánh |
| Một người tương ứng một patient | Có thể match chỉ theo phone | Gộp nhầm hồ sơ gia đình |

---

# M. Chuỗi tấn công thực tế

## Chuỗi 1 - Lộ thông tin lịch tiêm

```text
Biết/đoán số điện thoại
→ POST /tra-cuu-lich-hen
→ Không cần OTP hoặc tài khoản
→ Nhận tên, mã lịch, thời gian, vắc xin, số tiền, trạng thái thanh toán
```

## Chuỗi 2 - Chiếm khung giờ hàng loạt

```text
Không cần tài khoản
→ Gửi POST /register không bị throttle
→ Mỗi request chứa nhiều patients
→ Transaction hợp lệ tăng reserved_count
→ Toàn bộ slot bị giữ bởi dữ liệu giả
→ Người dùng thật không thể đặt lịch
```

## Chuỗi 3 - Sai lệch slot qua thanh toán lịch đã hủy

```text
Branch admin hủy lịch chưa thanh toán
→ Slot được giải phóng
→ Gọi trực tiếp settle cho lịch cancelled
→ Lịch trở thành cancelled + paid
→ Refund
→ Slot bị giải phóng lần hai
→ Có thể nhận thêm lịch vượt sức chứa thực tế
```

## Chuỗi 4 - Sai lệch tồn kho và y tế

```text
Nhiều lịch được tạo nhưng không reserve vaccine
→ Aggregate và lot inventory không đồng bộ
→ Thực hiện tiêm không consume lot
→ Báo cáo còn hàng dù liều thực tế đã dùng
→ Nguy cơ thiếu liều hoặc truy xuất sai lô
```

## Chuỗi 5 - Nếu workflow lâm sàng được bật nguyên trạng

```text
Branch admin
→ Dùng registration ID chi nhánh khác
→ Check-in/sàng lọc không kiểm tra center
→ Chọn vaccine/lot tồn tại bất kỳ
→ Ghi administered dose không trừ tồn
→ Tạo hồ sơ y tế sai hoặc liều trùng
```

---

# N. Logging và giám sát

## Đang có

- Ghi login thành công/thất bại/khóa tài khoản.
- `AuditLogger` ghi actor, center, resource, before/after, IP và user agent cho một số thao tác.
- Settlement, refund và một số cập nhật nghiệp vụ có audit.

## Thiếu hoặc chưa nhất quán

- Sửa quantity của inventory lot không có movement/audit.
- Chưa có audit đọc hồ sơ y tế hoặc export dữ liệu chi tiết theo mục đích.
- Log hiện dùng single file trong cấu hình workspace, chưa chứng minh rotation/retention production.
- Failed jobs có thể giữ serialized payload và exception, chưa thấy lịch prune.
- Cần cảnh báo cho lookup enumeration, booking burst, hoàn tiền bất thường, sửa điểm, sửa tồn và thao tác cross-branch bị từ chối.

---

# O. Upload và frontend

## Kiểm soát tích cực

- Upload ảnh giới hạn JPEG/PNG/WebP và khoảng 2 MB.
- Có kiểm tra nội dung ảnh tùy biến, chặn SVG/XML/script signature.
- Route upload yêu cầu admin; article/banner yêu cầu super admin.
- Không tìm thấy token xác thực trong `localStorage`; chỉ có theme.
- Nhiều `innerHTML` động dùng `escapeHtml` hoặc HTML cùng origin.
- CSV export có bảo vệ formula injection.

## Rủi ro còn lại

- File được ghi trực tiếp dưới webroot.
- Không re-encode ảnh, strip metadata, malware scan hoặc giới hạn pixel/decompression bomb.
- Script CDN thiếu SRI; một số dùng `@latest`.
- Nhiều inline script làm CSP nghiêm ngặt khó triển khai.
- Stored HTML CMS làm tăng tác động nếu sanitizer bị bypass.

---

# P. Kiểm soát cơ sở dữ liệu

## Constraint tích cực

- Unique registration code.
- Unique idempotency key.
- Unique customer phone.
- Unique loyalty source key.
- Unique center-vaccine.
- Unique center-date schedule.
- Unique schedule-start-end slot.

## Constraint còn thiếu

- Không có DB check `reserved_count <= capacity`.
- Không có check quantity không âm và `reserved <= initial/available`.
- Không có unique chống administered dose trùng.
- Không có unique lot number theo center/vaccine.
- Không có check constraint cho state values/transitions.
- Không có unique `(registration_id, vaccine_id)` trên pivot.

---

# Q. Ưu tiên khắc phục

## P0 - Sửa ngay

1. Đóng hoặc bổ sung OTP/ownership verification cho tra cứu lịch hẹn.
2. Luân chuyển application key, DB, FTP và admin credential có trong workspace; điều tra phạm vi lộ lọt.
3. Thêm chống abuse cho `POST /register`: rate limit, giới hạn patients, OTP và expiry cho lịch chưa xác nhận.
4. Sửa idempotency request-level và kiểm thử retry đồng thời.
5. Chặn settlement của lịch cancelled và bảo đảm slot chỉ release đúng một lần.

## P1 - Trước production

1. Thiết kế lại vòng đời tồn kho nguyên tử từ reservation đến administration/cancellation.
2. Hợp nhất state machine booking/payment/clinical và chặn transition không hợp lệ.
3. Không bật patient/vaccination routes trước khi bổ sung role, center scope, lot validation, transaction và chống duplicate.
4. Thu hồi session khi đổi mật khẩu/quyền; bắt buộc MFA cho admin.
5. Sửa lỗi lộ point transaction liên chi nhánh.
6. Loại quyền sửa trực tiếp `reserved_count` và lot available quantity.
7. Nâng cấp dependency có advisory và chạy regression test.
8. Tắt debug, bắt buộc HTTPS/secure cookie và xác minh TLS MySQL.

## P2 - Bản phát hành bảo mật kế tiếp

1. Tách vai trò branch admin thành receptionist, cashier, inventory, clinician và manager.
2. Bắt buộc policy/authorization service thay cho kiểm tra thủ công rải rác.
3. Thêm CSP, HSTS, frame protection, nosniff và referrer policy.
4. Bổ sung movement/audit bất biến và reconciliation cho tồn kho/slot.
5. Mã hóa dữ liệu y tế nhạy cảm phù hợp, áp dụng retention và quyền DB tối thiểu.
6. Cứng hóa sanitizer URL/HTML và CDN dependency.

## P3 - Gia cố

1. Re-encode ảnh, strip metadata, giới hạn dimensions và tách upload sang origin không thực thi.
2. Thêm cảnh báo SIEM cho booking burst, lookup enumeration, refund và stock adjustment.
3. Prune failed jobs/log theo chính sách và rà soát dữ liệu nhạy cảm trong exception.
4. Pin GitHub Actions theo commit SHA và chạy SAST/dependency audit trong CI.

---

# R. Kế hoạch kiểm thử xác nhận sau sửa

1. Test guest không thể xem lịch chỉ bằng phone; OTP hết hạn, dùng lại và brute force đều bị chặn.
2. Test cùng idempotency key với cùng payload trả cùng danh sách registration; payload khác bị từ chối.
3. Test đồng thời nhiều request vào slot cuối chỉ tạo đúng số lịch bằng capacity.
4. Test đồng thời vào liều cuối chỉ một reservation thành công.
5. Test mọi transition không có trong bảng trạng thái trả 409/422 và không đổi DB.
6. Test completed bắt buộc có administered dose hợp lệ và tồn kho được consume đúng một lần.
7. Test branch admin không đọc/sửa registration, customer points, lot, patient của chi nhánh khác.
8. Test đổi mật khẩu/role làm session cũ mất hiệu lực ngay.
9. Test cancelled registration không settle được và refund/cancel retry không giảm slot lần hai.
10. Test inventory adjustment luôn tạo movement/audit và giữ các invariant số lượng.
11. Chạy kiểm thử race bằng transaction/process đồng thời, không chỉ gửi request tuần tự.
12. Chạy `composer audit`, `npm audit`, full PHPUnit và kiểm thử header/cookie trên môi trường gần production.

---

# S. Kết luận

Hệ thống có một số kiểm soát tốt như session regeneration, CSRF, server-side price resolution, branch check ở nhiều controller và row lock cho slot. Tuy nhiên, các kiểm soát này chưa tạo thành một vòng đời nghiệp vụ an toàn xuyên suốt.

Rủi ro ưu tiên cao tập trung ở bốn khu vực: lộ dữ liệu qua tra cứu phone, lạm dụng/idempotency của đặt lịch công khai, trạng thái thanh toán-lịch-lâm sàng mâu thuẫn và tồn kho không được quản lý nguyên tử. Các controller lâm sàng chưa được route phải được xem là mã chưa hoàn thiện và không được đưa vào production trước khi bổ sung authorization và invariant y tế bắt buộc.
