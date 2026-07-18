<?php
/**
 * Chức năng: Script phụ tạo cơ sở dữ liệu MySQL medicare_codo nếu chưa tồn tại.
 * Lý do tạo: Tách biệt logic kết nối MySQL để tránh lỗi phân tích cú pháp dấu ngoặc đơn trong tệp batch của Windows.
 */

try {
    // Kết nối đến MySQL (root và mật khẩu rỗng mặc định)
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tạo cơ sở dữ liệu
    $pdo->exec("CREATE DATABASE IF NOT EXISTS medicare_codo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[+] Kiem tra va khoi tao database MySQL 'medicare_codo' thanh cong!\n";
    exit(0);
} catch (Exception $e) {
    fwrite(STDERR, "[LOI] Khong the ket noi MySQL: " . $e->getMessage() . "\n");
    exit(1);
}
