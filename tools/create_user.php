<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Chỉ chạy bằng PHP CLI.');
}

require_once __DIR__ . '/../app/Models/User.php';

// Nhập tên tài khoản
echo "Tên tài khoản (3-50 ký tự): ";
$newUsername = trim((string) fgets(STDIN));

// Nhập mật khẩu
echo "Mật khẩu (ít nhất 10 ký tự): ";
$newPassword = rtrim((string) fgets(STDIN), "\r\n");

// Kiểm tra dữ liệu
if (
    preg_match('/\A[A-Za-z0-9_]{3,50}\z/', $newUsername) !== 1 ||
    strlen($newPassword) < 10
) {
    exit("Tên tài khoản hoặc mật khẩu không hợp lệ.\n");
}

try {
    // Cô lập các biến trong file cấu hình database
    $pdo = (static function (): PDO {
        return require __DIR__ . '/../config/database.php';
    })();

    // Khởi tạo User Model
    $userModel = new User($pdo);

    // Kiểm tra tài khoản đã tồn tại chưa
    if ($userModel->findByUsername($newUsername) !== null) {
        exit("Tên tài khoản đã tồn tại.\n");
    }

    // Tạo tài khoản mới
    $userModel->create($newUsername, $newPassword);

    echo "Đã tạo tài khoản thành công: {$newUsername}\n";

} catch (PDOException $e) {
    error_log($e->getMessage());

    exit("Không thể tạo tài khoản. Kiểm tra database.\n");
}
