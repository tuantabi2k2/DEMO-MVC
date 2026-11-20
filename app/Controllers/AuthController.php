<?php
declare(strict_types=1);

final class AuthController
{
    public function __construct(private User $users) {}

    public function form(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        requireWebCsrf();
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;
        $user = is_string($username) && is_string($password)
            ? $this->users->findByUsername(trim($username)) : null;

        if ($user === null || !password_verify((string) $password, $user['password_hash'])) {
            $_SESSION['flash_error'] = 'Tài khoản hoặc mật khẩu không chính xác.';
            header('Location: index.php?action=login');
            exit;
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    }

    public function logout(): void
    {
        requireWebCsrf();
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: index.php?action=login');
        exit;
    }
}
