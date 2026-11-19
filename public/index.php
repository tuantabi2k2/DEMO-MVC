<?php
declare(strict_types=1);
session_start();
// Tạo CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Nạp Model
require_once __DIR__ . '/../app/Models/Patient.php';

// Nạp Controller
require_once __DIR__ . '/../app/Controllers/PatientController.php';

// Lấy action từ URL
$action = $_GET['action'] ?? 'index';

try {
    // Kết nối Database
    $pdo = require __DIR__ . '/../config/database.php';

    // Khởi tạo Model
    $patientModel = new Patient($pdo);

    // Khởi tạo Controller
    $controller = new PatientController($patientModel);

    // Router
    if (
        $action === 'index' &&
        $_SERVER['REQUEST_METHOD'] === 'GET'
    ) {
        $controller->index();

    } elseif (
        $action === 'store' &&
        $_SERVER['REQUEST_METHOD'] === 'POST'
    ) {
        $controller->store();

    } elseif (
        $action === 'index' ||
        $action === 'store'
    ) {
        http_response_code(405);

        header('Allow: ' . (
            $action === 'index' ? 'GET' : 'POST'
        ));

        echo 'Phương thức không được hỗ trợ';

    } else {
        http_response_code(404);
        echo 'Không tìm thấy trang';
    }

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo 'Có lỗi cơ sở dữ liệu xảy ra';
}
