<?php
declare(strict_types=1);

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrfToken(): string
{
    return $_SESSION['csrf_token'];
}

function validCsrf(): bool
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    return is_string($token) && hash_equals(csrfToken(), $token);
}

function requireWebAuth(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit;
    }
}

function requireWebCsrf(): void
{
    if (!validCsrf()) {
        http_response_code(403);
        exit('CSRF token không hợp lệ.');
    }
}

function redirectWithMessage(string $message, string $key = 'success'): never
{
    $_SESSION['flash_' . $key] = $message;
    header('Location: index.php');
    exit;
}

function patientInput(array $input): array
{
    $name = $input['name'] ?? null;
    $birthYear = $input['birth_year'] ?? null;

    if (!is_string($name) || (!is_string($birthYear) && !is_int($birthYear))) {
        throw new InvalidArgumentException('Họ tên hoặc năm sinh không hợp lệ.');
    }
    $name = trim($name);
    if (preg_match('/\A.{1,150}\z/u', $name) !== 1) {
        throw new InvalidArgumentException('Họ tên phải có từ 1 đến 150 ký tự.');
    }
    $year = filter_var($birthYear, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1900, 'max_range' => (int) date('Y')]
    ]);
    if ($year === false) {
        throw new InvalidArgumentException('Năm sinh không hợp lệ.');
    }
    return [$name, $year];
}

function jsonResponse(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function jsonInput(): array
{
    try {
        $data = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        throw new InvalidArgumentException('JSON không hợp lệ.');
    }
    if (!is_array($data) || array_is_list($data)) {
        throw new InvalidArgumentException('Body phải là JSON object.');
    }
    return $data;
}
