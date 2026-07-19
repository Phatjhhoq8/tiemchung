<?php
/**
 * Chức năng: Script phụ tạo các cơ sở dữ liệu MySQL cho ứng dụng và kiểm thử nếu chưa tồn tại.
 * Lý do tạo: Tách biệt logic kết nối MySQL để tránh lỗi phân tích cú pháp dấu ngoặc đơn trong tệp batch của Windows.
 */

try {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $username = getenv('DB_USERNAME') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';

    // Kết nối bằng cấu hình môi trường để dùng được cả Docker và MySQL cục bộ.
    $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tạo cơ sở dữ liệu ứng dụng và database tách biệt cho PHPUnit.
    $pdo->exec("CREATE DATABASE IF NOT EXISTS medicare_codo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("CREATE DATABASE IF NOT EXISTS medicare_codo_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[+] Khoi tao database MySQL 'medicare_codo' va 'medicare_codo_test' thanh cong!\n";
    exit(0);
} catch (Exception $e) {
    fwrite(STDERR, "[LOI] Khong the ket noi MySQL: " . $e->getMessage() . "\n");
    exit(1);
}
