<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
session_name('mvc_demo_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

require_once __DIR__ . '/../app/Support/helpers.php';
require_once __DIR__ . '/../app/Models/Patient.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Controllers/PatientController.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/ApiController.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$route = is_string($_GET['route'] ?? null) ? trim($_GET['route'], '/') : '';
$action = is_string($_GET['action'] ?? null) ? $_GET['action'] : 'index';

try {
    $pdo = require __DIR__ . '/../config/database.php';
    $patient = new Patient($pdo);
    $user = new User($pdo);

    if (str_starts_with($route, 'api/')) {
        (new ApiController($patient, $user))->dispatch($route, $method);
    }

    $patients = new PatientController($patient);
    $auth = new AuthController($user);
    $routes = [
        'GET' => [
            'index' => [$patients, 'index'],
            'show' => [$patients, 'show'],
            'edit' => [$patients, 'edit'],
            'login' => [$auth, 'form']
        ],
        'POST' => [
            'store' => [$patients, 'store'],
            'update' => [$patients, 'update'],
            'delete' => [$patients, 'destroy'],
            'login' => [$auth, 'login'],
            'logout' => [$auth, 'logout']
        ]
    ];
    if (isset($routes[$method][$action])) {
        call_user_func($routes[$method][$action]);
    } elseif (isset($routes['GET'][$action]) || isset($routes['POST'][$action])) {
        header('Allow: ' . (isset($routes['GET'][$action]) ? 'GET' : 'POST'));
        http_response_code(405);
        echo 'Phương thức không được hỗ trợ.';
    } else {
        http_response_code(404);
        echo 'Không tìm thấy trang.';
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    if (str_starts_with($route, 'api/')) jsonResponse(['error' => 'Lỗi cơ sở dữ liệu.'], 500);
    http_response_code(500);
    echo 'Có lỗi cơ sở dữ liệu. Vui lòng kiểm tra cấu hình kết nối.';
}
