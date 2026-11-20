<?php
declare(strict_types=1);

final class ApiController
{
    public function __construct(private Patient $patients, private User $users) {}

    public function dispatch(string $route, string $method): never
    {
        if ($route === 'api/csrf' && $method === 'GET') {
            jsonResponse(['csrf_token' => csrfToken()]);
        }

        if ($route === 'api/login' && $method === 'POST') {
            if (!validCsrf()) jsonResponse(['error' => 'CSRF token không hợp lệ.'], 403);
            try {
                $input = jsonInput();
            } catch (InvalidArgumentException $e) {
                jsonResponse(['error' => $e->getMessage()], 400);
            }
            $username = $input['username'] ?? null;
            $password = $input['password'] ?? null;
            $user = is_string($username) && is_string($password)
                ? $this->users->findByUsername(trim($username)) : null;
            if ($user === null || !password_verify((string) $password, $user['password_hash'])) {
                jsonResponse(['error' => 'Tài khoản hoặc mật khẩu không chính xác.'], 401);
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            jsonResponse(['message' => 'Đăng nhập thành công.', 'username' => $user['username'], 'csrf_token' => csrfToken()]);
        }

        if (empty($_SESSION['user_id'])) jsonResponse(['error' => 'Chưa đăng nhập.'], 401);

        if ($route === 'api/logout' && $method === 'POST') {
            if (!validCsrf()) jsonResponse(['error' => 'CSRF token không hợp lệ.'], 403);
            $_SESSION = [];
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            jsonResponse(['message' => 'Đã đăng xuất.']);
        }

        if (!preg_match('~^api/patients(?:/([1-9][0-9]*))?$~', $route, $matches)) {
            jsonResponse(['error' => 'Không tìm thấy API.'], 404);
        }
        $id = isset($matches[1]) ? filter_var($matches[1], FILTER_VALIDATE_INT) : null;
        if (isset($matches[1]) && ($id === false || $id === 0)) {
            jsonResponse(['error' => 'ID không hợp lệ.'], 400);
        }
        if (in_array($method, ['POST', 'PUT', 'DELETE'], true) && !validCsrf()) {
            jsonResponse(['error' => 'CSRF token không hợp lệ.'], 403);
        }

        if ($id === null && $method === 'GET') {
            $q = $_GET['q'] ?? '';
            $yearRaw = $_GET['year'] ?? '';
            $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $limit = filter_var($_GET['per_page'] ?? 10, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 50]]);
            if (!is_string($q) || (!is_string($yearRaw) && !is_int($yearRaw)) || $page === false || $limit === false) {
                jsonResponse(['error' => 'Tham số lọc/phân trang không hợp lệ.'], 422);
            }
            $year = null;
            if ((string) $yearRaw !== '') {
                $year = filter_var($yearRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1900, 'max_range' => (int) date('Y')]]);
                if ($year === false) jsonResponse(['error' => 'Năm sinh không hợp lệ.'], 422);
            }
            $q = trim($q);
            $total = $this->patients->countMatching($q, $year);
            $pages = max(1, (int) ceil($total / $limit));
            $page = min($page, $pages);
            jsonResponse([
                'data' => $this->patients->paged($q, $year, $page, $limit),
                'pagination' => ['page' => $page, 'per_page' => $limit, 'total' => $total, 'total_pages' => $pages]
            ]);
        }

        if ($id !== null && $method === 'GET') {
            $patient = $this->patients->find($id);
            if ($patient === null) jsonResponse(['error' => 'Không tìm thấy bệnh nhân.'], 404);
            jsonResponse(['data' => $patient]);
        }

        if ($id === null && $method === 'POST') {
            try {
                [$name, $year] = patientInput(jsonInput());
            } catch (InvalidArgumentException $e) {
                jsonResponse(['error' => $e->getMessage()], 422);
            }
            $newId = $this->patients->create($name, $year);
            jsonResponse(['message' => 'Đã tạo bệnh nhân.', 'data' => $this->patients->find($newId)], 201);
        }

        if ($id !== null && $method === 'PUT') {
            if ($this->patients->find($id) === null) jsonResponse(['error' => 'Không tìm thấy bệnh nhân.'], 404);
            try {
                [$name, $year] = patientInput(jsonInput());
            } catch (InvalidArgumentException $e) {
                jsonResponse(['error' => $e->getMessage()], 422);
            }
            $this->patients->update($id, $name, $year);
            jsonResponse(['message' => 'Đã cập nhật bệnh nhân.', 'data' => $this->patients->find($id)]);
        }

        if ($id !== null && $method === 'DELETE') {
            if ($this->patients->find($id) === null) jsonResponse(['error' => 'Không tìm thấy bệnh nhân.'], 404);
            $this->patients->delete($id);
            jsonResponse(['message' => 'Đã xóa bệnh nhân.']);
        }

        header('Allow: ' . ($id === null ? 'GET, POST' : 'GET, PUT, DELETE'));
        jsonResponse(['error' => 'HTTP method không được hỗ trợ.'], 405);
    }
}
