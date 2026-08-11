# Feedback triển khai cấu hình tích điểm động

## Kết luận

Implementation hiện tại **chưa đáp ứng đầy đủ kế hoạch**. Phần cấu hình giao diện và tính điểm cơ bản đã được triển khai, nhưng thiết kế ledger/FIFO quan trọng nhất vẫn theo phương án tính lại lịch sử và sẽ cho số dư sai sau khi lô điểm đã sử dụng hết hạn.

## Phát hiện

### Nghiêm trọng

#### 1. FIFO không lưu allocation

`app/Services/RegistrationPaymentService.php:50-106` loại các lô hết hạn rồi phân bổ lại toàn bộ redeem lịch sử trên các lô còn hạn.

Ví dụ:

- Lô A: 20 điểm, hết hạn ngày mai.
- Lô B: 20 điểm, hết hạn tháng sau.
- Hôm nay redeem 20, đúng ra dùng hết A và còn B = 20.
- Ngày mai A hết hạn, code loại A rồi áp lại redeem 20 vào B.
- Số dư bị tính thành 0 thay vì 20.

Implementation chưa có:

- Migration `point_allocations`.
- Model `PointAllocation`.
- Quan hệ `debit_transaction_id`/`credit_transaction_id`.
- Backfill allocation lịch sử.

Migration hiện tại chỉ thêm `expired_at` tại `modules/VaccineRegistration/Database/Migrations/2026_08_11_183842_add_expired_at_to_point_transactions_table.php:14-17`.

#### 2. Settlement có thể dùng hai phiên bản settings

- `quote()` đọc settings tại `app/Services/RegistrationPaymentService.php:111`.
- `settle()` đọc lại tại `app/Services/RegistrationPaymentService.php:189`.

Nếu settings thay đổi giữa hai lần đọc, điểm có thể được trừ nhưng discount bằng 0, hoặc giới hạn redeem được kiểm tra bằng cấu hình cũ nhưng quyết toán bằng giá trị mới.

Cần đọc settings một lần và sử dụng cùng snapshot trong toàn bộ transaction settlement.

#### 3. Refund không khôi phục đúng lô đã sử dụng

`app/Services/RegistrationPaymentService.php:372-385` chỉ tạo một transaction dương, không biết redeem ban đầu đã sử dụng lô nào và hạn dùng nào.

Hậu quả:

- Khôi phục nhầm lô.
- Làm sống lại điểm đã hết hạn.
- Tác động vào các lô mới không liên quan.

### Cao

#### 4. Các màn hình khách hàng vẫn hiển thị raw balance

Các vị trí vẫn dùng `SUM(points)`:

- `modules/VaccineRegistration/Http/Controllers/Admin/AdminCustomerController.php:19`
- `modules/VaccineRegistration/Http/Controllers/Admin/AdminCustomerController.php:95`
- `modules/VaccineRegistration/Models/Customer.php:43-46`

Trang khách hàng có thể báo còn điểm nhưng màn hình thanh toán lại không cho sử dụng vì điểm đã hết hạn.

#### 5. Adjustment âm không chạy FIFO

`modules/VaccineRegistration/Http/Controllers/Admin/AdminCustomerController.php:119-138` ghi trực tiếp transaction âm mà không:

- Kiểm tra available balance.
- Khóa các credit lot.
- Tạo allocation FIFO.
- Áp dụng chính sách dư nợ.

#### 6. Nhiều campaign không được cộng dồn

`app/Services/RegistrationPaymentService.php:225-235` chỉ giữ multiplier lớn nhất bằng `max()`.

Kế hoạch yêu cầu cộng phần dư của tất cả campaign đang hoạt động.

#### 7. Adjustment dương đang làm tăng hạng

`app/Services/RegistrationPaymentService.php:204-207` tính lịch sử bằng cả `EARN` và `ADJUSTMENT`, trong khi kế hoạch quy định adjustment thủ công không làm tăng hạng.

#### 8. Ngưỡng đơn tối thiểu để earn kiểm tra sai cơ sở

`app/Services/RegistrationPaymentService.php:203` so sánh `min_order_value_to_earn` với `$netPaid`.

Theo kế hoạch:

- Kiểm tra ngưỡng trên `total_price`.
- Sau khi đủ điều kiện mới tính điểm trên `netPaid`.

#### 9. Settings chưa theo schema mới

Code vẫn sử dụng một trường float là `redeem_point_value`, trong khi kế hoạch đã tách:

- `redeem_vnd_per_point`.
- `redeem_percent_bps_per_point`.

Phép tính phần trăm tại `app/Services/RegistrationPaymentService.php:137-144,192-197` sử dụng float, chưa bảo đảm làm tròn tiền xác định.

#### 10. Không có snapshot cấu hình trên transaction

Implementation chưa có:

- `metadata`.
- `reverses_transaction_id`.
- Tier/campaign/rate đã áp dụng.
- Chính sách expiration tại thời điểm earn/redeem.

Sau khi admin đổi settings sẽ không thể audit hoặc tái hiện cách tính giao dịch cũ.

### Trung bình

#### 11. `max_redeem_amount = 0` bị hiểu thành không giới hạn

`app/Services/RegistrationPaymentService.php:133` dùng `empty()`, nên giá trị 0 bị bỏ qua. Controller lại cho phép nhập 0 tại `modules/VaccineRegistration/Http/Controllers/Admin/AdminLoyaltySettingController.php:63`.

#### 12. Cách tính expiry chưa đúng kế hoạch

`app/Services/RegistrationPaymentService.php:278` dùng:

```php
now()->addMonths($expiryMonths)->endOfDay()
```

Kế hoạch yêu cầu `addMonthsNoOverflow()`. Ngày 29, 30 hoặc 31 có thể cho kết quả khác mong muốn.

#### 13. Timezone campaign và birthday chưa cố định

Code dùng `now()->toDateString()` tại `app/Services/RegistrationPaymentService.php:226`, chưa áp dụng timezone nghiệp vụ `Asia/Ho_Chi_Minh`.

#### 14. Birthday mặc định dùng người tiêm

`app/Services/RegistrationPaymentService.php:239-247` dùng DOB của `Patient`. Kế hoạch đánh dấu đây là quyết định cần khách hàng xác nhận, nhưng implementation đã tự chọn mà chưa ghi rõ chính sách.

#### 15. Migration settings có thể ghi đè dữ liệu thật

`modules/VaccineRegistration/Database/Migrations/2026_08_11_184151_insert_default_loyalty_settings.php:14-38` dùng `updateOrInsert`, có thể ghi đè cấu hình đã tồn tại.

`down()` tại dòng 44-47 xóa settings không điều kiện. Seeder cũng ghi đè cấu hình tại `modules/VaccineRegistration/Database/Seeders/SettingSeeder.php:109-113`.

#### 16. Loyalty bị tắt thì quote báo balance bằng 0

`app/Services/RegistrationPaymentService.php:112-117` trả `balance = 0`. Theo kế hoạch, điểm vẫn được bảo toàn; chỉ `available_points` để redeem mới nên bằng 0.

#### 17. UI còn hardcode giới hạn 50%

`modules/VaccineRegistration/resources/views/admin/registrations/show.blade.php:60` vẫn ghi `(50% đơn)` dù admin có thể thay đổi giới hạn.

#### 18. Save settings và audit không nguyên tử

`modules/VaccineRegistration/Http/Controllers/Admin/AdminLoyaltySettingController.php:93-105` lưu settings rồi mới ghi audit, không có DB transaction.

## Mức độ hoàn thành

| Hạng mục | Trạng thái |
|---|---|
| Migration `expired_at` | Đã có |
| Fillable/cast `expired_at` | Đã có |
| UI và route cấu hình | Đã có |
| Quyền super admin | Đã có |
| Min order, VND/percent và caps cơ bản | Một phần |
| Tier và birthday multiplier | Một phần |
| Nhiều campaign cộng dồn | Sai |
| FIFO bền vững theo lịch sử | Chưa có |
| Allocation schema/model | Chưa có |
| Backfill allocation | Chưa có |
| Refund/reversal đúng lô | Chưa có |
| Balance nhất quán toàn hệ thống | Chưa có |
| Snapshot cấu hình | Chưa có |
| Concurrency trên từng credit lot | Chưa có |
| Test đầy đủ theo kế hoạch | Chưa có |

## Kết quả kiểm tra

- PHP syntax của service, controller, migration và test loyalty hợp lệ.
- `LoyaltyPointsDynamicConfigTest`: 3 test hiện có vượt qua.
- `CustomerLoyaltyAndManualPaymentTest`: 7 test vượt qua.
- Test hiện tại chưa kiểm tra lô đã dùng rồi mới hết hạn, nên chưa phát hiện lỗi FIFO.
- `git diff --check` báo trailing whitespace trong `RegistrationPaymentService.php` và `booking_lookup.blade.php`.
- Có file chưa track `toArray())` chứa output lỗi PsySH, nhiều khả năng là file rác tạo nhầm.

## Test còn thiếu

- Redeem lô sắp hết hạn, sau đó tiến thời gian qua hạn và xác nhận lô sau vẫn còn nguyên.
- FIFO khi nhiều lô cùng hạn và khi có lô vô hạn.
- Refund trước và sau khi hạn gốc đã qua.
- Earn reversal sau khi điểm đã được dùng một phần hoặc toàn bộ.
- Nhiều campaign đồng thời.
- Percentage redemption, rounding và basis point.
- `max_redeem_amount` với `null`, 0 và số dương.
- Minimum earn/redeem ở dưới, đúng và trên ngưỡng.
- Expiry tại ngày 29, 30 và 31.
- Adjustment âm/dương, authorization, audit và idempotency.
- Loyalty bị tắt rồi bật lại với điểm hiện có.
- Balance trên quote, danh sách và chi tiết khách hàng phải giống nhau.
- Hai settlement đồng thời trên cùng customer.
- Settings thay đổi đồng thời với settlement.

## Thứ tự khắc phục đề xuất

1. Thêm `point_allocations`, model và backfill.
2. Chuyển balance, redeem, refund và adjustment sang `LoyaltyService`.
3. Dùng một snapshot settings trong settlement.
4. Sửa refund/reversal theo allocation gốc.
5. Cập nhật mọi màn hình dùng available balance.
6. Sửa campaign cộng dồn, tier, minimum order, timezone và rounding.
7. Bổ sung test expiration sau redeem, refund sau expiration và concurrency.
