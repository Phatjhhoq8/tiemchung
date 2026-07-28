<?php
/**
 * Chức năng: Script phụ tạo các cơ sở dữ liệu MySQL cho ứng dụng và kiểm thử nếu chưa tồn tại.
 * Lý do tạo: Tách biệt logic kết nối MySQL để tránh lỗi phân tích cú pháp dấu ngoặc đơn trong tệp batch của Windows.
 */

try {
    $envPath = dirname(__DIR__) . '/.env';
    $env = [];
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            $value = trim($value, '"\'');
            $env[$name] = $value;
        }
    }

    $host = $env['DB_HOST'] ?? getenv('DB_HOST') ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
    $database = $env['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? '';
    $username = $env['DB_USERNAME'] ?? getenv('DB_USERNAME') ?? 'root';
    $password = $env['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '';

    // Thử kết nối trực tiếp đến database được chỉ định trong cấu hình trước
    if (!empty($database)) {
        try {
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "[+] Da ket noi thanh cong den database '{$database}' tren host '{$host}'.\n";
            exit(0);
        } catch (Exception $e) {
            // Nếu không kết nối được trực tiếp (có thể database chưa tồn tại), tiếp tục thử tạo database mới
        }
    }

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
