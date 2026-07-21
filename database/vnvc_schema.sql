-- =====================================================================
-- VNVC-LIKE SYSTEM — DATABASE SCHEMA (MySQL 8.0+)
-- Nguyên tắc thiết kế:
--   1) AN TOÀN DỮ LIỆU: ưu tiên soft-delete, giữ lịch sử, tránh mất
--      dữ liệu giao dịch/y tế do xóa cứng (hard delete) hoặc CASCADE.
--   2) MỞ RỘNG: tách log/lịch sử ra bảng riêng, không sửa cấu trúc
--      bảng chính khi nghiệp vụ phát triển thêm.
--   3) GUEST-FIRST: patient không bắt buộc gắn với user (guest checkout).
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
-- NHÓM 1: SẢN PHẨM & DỊCH VỤ
-- =====================================================================

CREATE TABLE manufacturers (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    country         VARCHAR(100),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Danh mục nhà sản xuất vắc-xin. Bảng danh mục thuần, ít thay đổi.';

CREATE TABLE diseases (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    description     TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Danh mục bệnh mà vắc-xin phòng ngừa. Dùng để lọc/tìm kiếm theo bệnh.';

CREATE TABLE age_groups (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL COMMENT 'VD: "Trẻ từ 2-6 tháng"',
    sort_order      INT DEFAULT 0 COMMENT 'Thứ tự hiển thị trên giao diện',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Danh mục nhóm tuổi, dùng để gợi ý gói vắc-xin/lọc sản phẩm phù hợp.';

CREATE TABLE vaccines (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(255) NOT NULL,
    sku                 VARCHAR(100) NOT NULL UNIQUE,
    description         TEXT,
    price               DECIMAL(12,2) NOT NULL DEFAULT 0,
    manufacturer_id     BIGINT UNSIGNED,
    injection_schedule  VARCHAR(500) COMMENT 'Phác đồ tiêm, vd: "Tiêm 3 mũi, cách nhau 1 tháng"',
    image_url           VARCHAR(500),
    is_active           BOOLEAN DEFAULT TRUE COMMENT 'Còn kinh doanh hay đã ngừng bán',
    deleted_at          TIMESTAMP NULL COMMENT 'Soft-delete: KHÔNG xóa cứng vì lịch sử tiêm/đăng ký còn tham chiếu tới vaccine này',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (manufacturer_id) REFERENCES manufacturers(id) ON DELETE SET NULL,
    INDEX idx_vaccines_active (is_active),
    INDEX idx_vaccines_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Vắc-xin lẻ. Bảng lõi của hệ thống — dùng soft-delete để bảo toàn lịch sử giao dịch.';

CREATE TABLE vaccine_packages (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    description     TEXT,
    total_price     DECIMAL(12,2) NOT NULL DEFAULT 0,
    age_group_id    BIGINT UNSIGNED,
    is_active       BOOLEAN DEFAULT TRUE,
    deleted_at      TIMESTAMP NULL COMMENT 'Soft-delete, lý do tương tự bảng vaccines',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (age_group_id) REFERENCES age_groups(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Gói vắc-xin (bundle nhiều vaccine lẻ, thường có giá ưu đãi hơn mua rời).';

CREATE TABLE package_vaccine (
    package_id      BIGINT UNSIGNED NOT NULL,
    vaccine_id      BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (package_id, vaccine_id),
    FOREIGN KEY (package_id) REFERENCES vaccine_packages(id) ON DELETE CASCADE,
    FOREIGN KEY (vaccine_id) REFERENCES vaccines(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Bảng trung gian: 1 gói vắc-xin gồm nhiều vaccine lẻ nào (many-to-many).';

CREATE TABLE vaccine_disease (
    vaccine_id      BIGINT UNSIGNED NOT NULL,
    disease_id      BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (vaccine_id, disease_id),
    FOREIGN KEY (vaccine_id) REFERENCES vaccines(id) ON DELETE CASCADE,
    FOREIGN KEY (disease_id) REFERENCES diseases(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Bảng trung gian: 1 vaccine phòng được những bệnh nào (many-to-many).';

CREATE TABLE vaccine_age_group (
    vaccine_id      BIGINT UNSIGNED NOT NULL,
    age_group_id    BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (vaccine_id, age_group_id),
    FOREIGN KEY (vaccine_id) REFERENCES vaccines(id) ON DELETE CASCADE,
    FOREIGN KEY (age_group_id) REFERENCES age_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Bảng trung gian: 1 vaccine phù hợp với những nhóm tuổi nào (many-to-many).';

-- =====================================================================
-- NHÓM 2: KHÁCH HÀNG & NGƯỜI DÙNG
-- Thiết kế Guest-first: patients KHÔNG bắt buộc gắn với users
-- =====================================================================

CREATE TABLE users (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    phone               VARCHAR(20) NOT NULL UNIQUE,
    password            VARCHAR(255) NOT NULL,
    full_name           VARCHAR(255) NOT NULL,
    email               VARCHAR(255) UNIQUE,
    phone_verified_at   TIMESTAMP NULL COMMENT 'Thời điểm xác thực OTP, cũng dùng khi backfill patient guest -> user',
    deleted_at          TIMESTAMP NULL COMMENT 'Soft-delete: không xóa cứng để giữ lịch sử đăng ký/hóa đơn liên quan',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Tài khoản đăng nhập. KHÔNG bắt buộc phải có để đặt lịch tiêm (xem bảng patients). Không lưu role trực tiếp — xem roles/user_role để hỗ trợ multi-role.';

-- ---------------------------------------------------------------------
-- MULTI-ROLE (RBAC): 1 user có thể mang nhiều role cùng lúc
-- (vd: vừa là "customer" vừa là "staff" tại 1 trung tâm khác)
-- Tách rời khỏi bảng users để KHÔNG phải sửa cấu trúc users khi
-- nghiệp vụ phân quyền phát triển thêm (mở rộng).
-- ---------------------------------------------------------------------

CREATE TABLE roles (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(50) NOT NULL UNIQUE COMMENT 'Định danh cố định dùng trong code, vd: "admin", "patient"',
    name            VARCHAR(100) NOT NULL COMMENT 'Tên hiển thị, vd: "Quản trị viên", "Bệnh nhân"',
    description     VARCHAR(500),
    deleted_at      TIMESTAMP NULL COMMENT 'Soft-delete: tránh phá vỡ user_role đang tham chiếu role này',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='[FRAMEWORK: Laravel] Danh mục vai trò (RBAC). Đang dùng thật, seed sẵn admin/patient. Cột (slug, name) đặt tên tương thích với package spatie/laravel-permission — nếu sau này cần nâng cấp lên package đó, chỉ cần map dữ liệu sang, KHÔNG phải xây lại từ đầu.';

CREATE TABLE permissions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(100) NOT NULL UNIQUE COMMENT 'VD: "vaccines.manage", "registrations.view", "stocks.update"',
    name            VARCHAR(150) NOT NULL,
    description     VARCHAR(500),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='[FRAMEWORK: Laravel — dùng chung với spatie/laravel-permission nếu nâng cấp] BẢNG CHỜ NÂNG CẤP: hiện tại KHÔNG bắt buộc dùng, để trống cũng không sao vì backend đang check quyền trực tiếp theo role (vd: Gate::define hoặc $user->roles). Bảng này lưu sẵn chỗ cho việc phân quyền chi tiết theo hành động (permission) khi nghiệp vụ phức tạp hơn — người mở rộng sau chỉ cần bắt đầu ghi dữ liệu vào đây, không cần tạo bảng mới hay đổi cấu trúc DB.';

CREATE TABLE role_permission (
    role_id         BIGINT UNSIGNED NOT NULL,
    permission_id   BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='[FRAMEWORK: Laravel] BẢNG CHỜ NÂNG CẤP, đi kèm với bảng permissions ở trên: 1 role gồm những permission nào (many-to-many). Để trống nếu chưa dùng — không ảnh hưởng gì đến việc backend check quyền theo role như hiện tại.';

CREATE TABLE user_role (
    user_id         BIGINT UNSIGNED NOT NULL,
    role_id         BIGINT UNSIGNED NOT NULL,
    assigned_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    INDEX idx_user_role_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='[FRAMEWORK: Laravel] Đang dùng thật: 1 user có thể có nhiều role cùng lúc (vd vừa admin vừa patient). Chưa cần cột center_id vì hiện chỉ 1 chi nhánh và role hiện tại áp dụng toàn hệ thống — có thể thêm cột đó sau nếu xuất hiện role kiểu "staff" chỉ quản lý 1 trung tâm.';

CREATE TABLE patients (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NULL COMMENT 'NULL = hồ sơ tạo bởi khách vãng lai (guest), chưa gắn tài khoản nào',
    full_name       VARCHAR(255) NOT NULL,
    phone           VARCHAR(20) NOT NULL COMMENT 'SĐT liên hệ trực tiếp — bắt buộc kể cả guest, dùng để tra cứu và backfill sau này',
    dob             DATE,
    gender          ENUM('male', 'female', 'other'),
    relationship    VARCHAR(100) COMMENT '"Bản thân", "Con", "Vợ/Chồng"...',
    address         VARCHAR(500),
    guardian_name   VARCHAR(255),
    source          ENUM('guest', 'registered') NOT NULL DEFAULT 'guest'
                    COMMENT 'guest = tạo lúc chưa đăng nhập; registered = tạo khi đã có tài khoản',
    deleted_at      TIMESTAMP NULL COMMENT 'Soft-delete: hồ sơ y tế không xóa cứng',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_patients_phone (phone),
    INDEX idx_patients_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Hồ sơ người được tiêm. Trung tâm của thiết kế guest-first: 1 user có thể quản lý nhiều patient, 1 patient có thể chưa thuộc user nào (guest).';

-- =====================================================================
-- NHÓM 3: VẬN HÀNH (Trung tâm & Tồn kho)
-- =====================================================================

CREATE TABLE centers (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    address         VARCHAR(500),
    city            VARCHAR(100),
    phone           VARCHAR(20),
    map_url         VARCHAR(500),
    working_hours   VARCHAR(255),
    is_active       BOOLEAN DEFAULT TRUE,
    deleted_at      TIMESTAMP NULL COMMENT 'Soft-delete: ngừng hoạt động khác với xóa dữ liệu lịch sử đăng ký tại trung tâm',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Trung tâm tiêm chủng (chi nhánh vật lý).';

CREATE TABLE stocks (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    center_id       BIGINT UNSIGNED NOT NULL,
    vaccine_id      BIGINT UNSIGNED NOT NULL,
    quantity        INT NOT NULL DEFAULT 0,
    expiry_date     DATE COMMENT 'Ngày hết hạn của LÔ vắc-xin cụ thể này',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE RESTRICT,
    FOREIGN KEY (vaccine_id) REFERENCES vaccines(id) ON DELETE RESTRICT,
    UNIQUE KEY uq_stock_lot (center_id, vaccine_id, expiry_date)
        COMMENT 'Chặn tạo trùng dòng tồn kho cho cùng 1 lô (cùng center+vaccine+hạn dùng)',
    INDEX idx_stocks_center_vaccine (center_id, vaccine_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Tồn kho vắc-xin theo từng trung tâm và từng lô (theo hạn dùng). RESTRICT thay vì CASCADE để không mất số liệu tồn kho khi xóa nhầm.';

-- =====================================================================
-- NHÓM 4: GIAO DỊCH & ĐĂNG KÝ
-- Hỗ trợ tra cứu cho guest bằng lookup_code + contact_phone
-- =====================================================================

CREATE TABLE registrations (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_code       VARCHAR(50) NOT NULL UNIQUE COMMENT 'Mã đăng ký đầy đủ, hiển thị chính thức (vd: VNVC-XYZ123)',
    lookup_code             VARCHAR(20) NOT NULL UNIQUE COMMENT 'Mã ngắn để guest tự tra cứu, kèm SĐT',
    patient_id              BIGINT UNSIGNED NOT NULL,
    center_id               BIGINT UNSIGNED NOT NULL,
    contact_phone           VARCHAR(20) NOT NULL COMMENT 'Lưu tách khỏi patients.phone — tránh bị ảnh hưởng nếu SĐT patient đổi sau này',
    desired_injection_date  DATE,
    status                  ENUM('pending_payment', 'confirmed', 'completed', 'cancelled')
                            NOT NULL DEFAULT 'pending_payment'
                            COMMENT 'Trạng thái hiện tại. Lịch sử đổi trạng thái xem bảng registration_status_logs',
    deleted_at              TIMESTAMP NULL COMMENT 'Soft-delete: không xóa cứng lịch sử đăng ký tiêm (dữ liệu y tế/pháp lý)',
    created_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE RESTRICT,
    INDEX idx_registrations_phone (contact_phone),
    INDEX idx_registrations_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Lượt đăng ký tiêm chủng. Bảng giao dịch lõi — RESTRICT toàn bộ FK để tránh mất dữ liệu khi xóa patient/center.';

CREATE TABLE registration_status_logs (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_id     BIGINT UNSIGNED NOT NULL,
    from_status         ENUM('pending_payment', 'confirmed', 'completed', 'cancelled') NULL,
    to_status           ENUM('pending_payment', 'confirmed', 'completed', 'cancelled') NOT NULL,
    changed_by          VARCHAR(100) COMMENT 'Ai/hệ thống nào thực hiện thay đổi (vd: user_id, admin_id, "system")',
    note                VARCHAR(500),
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    INDEX idx_status_logs_registration (registration_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Lịch sử thay đổi trạng thái của registrations — phục vụ tra soát, khiếu nại, audit. Tách riêng để mở rộng không ảnh hưởng bảng chính.';

CREATE TABLE registration_items (
    id                              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_id                 BIGINT UNSIGNED NOT NULL,
    vaccine_id                      BIGINT UNSIGNED NULL COMMENT 'Set khi mua vaccine lẻ; NULL nếu là gói',
    package_id                      BIGINT UNSIGNED NULL COMMENT 'Set khi mua gói; NULL nếu là vaccine lẻ',
    price_at_time_of_registration   DECIMAL(12,2) NOT NULL COMMENT 'Chốt giá tại thời điểm đăng ký, không đổi theo giá gốc sau này',
    created_at                      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    FOREIGN KEY (vaccine_id) REFERENCES vaccines(id) ON DELETE RESTRICT,
    FOREIGN KEY (package_id) REFERENCES vaccine_packages(id) ON DELETE RESTRICT,
    CONSTRAINT chk_reg_items_exactly_one_item CHECK (
        (vaccine_id IS NOT NULL AND package_id IS NULL) OR
        (vaccine_id IS NULL AND package_id IS NOT NULL)
    ),
    INDEX idx_reg_items_vaccine (vaccine_id),
    INDEX idx_reg_items_package (package_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Chi tiết 1 lượt đăng ký gồm những vaccine/gói nào. Dùng 2 cột FK riêng (thay vì polymorphic item_type+item_id) để DB tự đảm bảo toàn vẹn tham chiếu.';

CREATE TABLE invoices (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_id BIGINT UNSIGNED NOT NULL,
    invoice_number  VARCHAR(50) NOT NULL UNIQUE,
    total_amount    DECIMAL(12,2) NOT NULL DEFAULT 0,
    payment_method  VARCHAR(50),
    payment_status  ENUM('unpaid', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
    paid_at         TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL COMMENT 'Soft-delete: hóa đơn là chứng từ, không xóa cứng',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Hóa đơn thanh toán. Cho phép nhiều invoice/1 registration (hỗ trợ thanh toán từng phần); ràng buộc 1-1 nếu nghiệp vụ không cần.';

-- =====================================================================
-- NHÓM 5: MARKETING & NỘI DUNG (cơ bản, mở rộng sau)
-- =====================================================================

CREATE TABLE articles (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL UNIQUE,
    content         LONGTEXT,
    thumbnail_url   VARCHAR(500),
    is_published    BOOLEAN DEFAULT FALSE,
    published_at    TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Bài viết nội dung (tin tức, kiến thức tiêm chủng...). Không liên quan trực tiếp tới giao dịch nên không cần RESTRICT nghiêm ngặt.';

CREATE TABLE promotions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    code            VARCHAR(50) UNIQUE,
    discount_type   ENUM('percent', 'fixed') NOT NULL,
    discount_value  DECIMAL(12,2) NOT NULL,
    start_date      DATE,
    end_date        DATE,
    is_active       BOOLEAN DEFAULT TRUE,
    deleted_at      TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Khuyến mãi / mã giảm giá. Nên soft-delete để giữ lịch sử áp dụng nếu đã có invoice tham chiếu.';

-- =====================================================================
-- SEED DATA: Role mặc định
-- =====================================================================

INSERT INTO roles (slug, name, description) VALUES
    ('admin', 'Quản trị viên', 'Toàn quyền quản trị hệ thống (sản phẩm, trung tâm, kho, đăng ký, nội dung...)'),
    ('patient', 'Bệnh nhân', 'Người dùng đã đăng ký tài khoản, có thể đặt lịch và xem lịch sử tiêm chủng của mình');

-- Lưu ý: bệnh nhân KHÔNG có tài khoản (guest) không cần gán role gì cả,
-- vì họ không có bản ghi trong bảng users -> không có user_role tương ứng.
-- Khi guest tạo tài khoản (backfill), gán role 'patient' cho user_id mới:
--   INSERT INTO user_role (user_id, role_id) VALUES (:new_user_id,
--       (SELECT id FROM roles WHERE slug = 'patient'));

SET FOREIGN_KEY_CHECKS = 1;
