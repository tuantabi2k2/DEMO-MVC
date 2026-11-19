<?php
declare(strict_types=1);

final class PatientController
{
    public function __construct(
        private Patient $patientModel
    ) {}

    // Hiển thị danh sách bệnh nhân
    public function index(): void
    {
        $patients = $this->patientModel->all();

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        require __DIR__ . '/../Views/patients/index.php';
    }

    // Xử lý thêm bệnh nhân
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo 'Phương thức không hợp lệ';
            return;
        }

        // Kiểm tra CSRF token
        $token = $_POST['csrf_token'] ?? null;

        if (
            !is_string($token) ||
            !hash_equals(
                $_SESSION['csrf_token'] ?? '',
                $token
            )
        ) {
            http_response_code(403);
            echo 'CSRF token không hợp lệ';
            return;
        }

        $nameInput = $_POST['name'] ?? null;
        $yearInput = $_POST['birth_year'] ?? null;

        if (
            !is_string($nameInput) ||
            !is_string($yearInput)
        ) {
            $_SESSION['flash_error'] = 'Dữ liệu không hợp lệ';
            header('Location: index.php');
            exit;
        }

        $name = trim($nameInput);

        $birthYear = filter_var(
            $yearInput,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1900,
                    'max_range' => (int) date('Y')
                ]
            ]
        );

        if (
            preg_match('/\A.{1,150}\z/u', $name) !== 1 ||
            $birthYear === false
        ) {
            $_SESSION['flash_error'] =
                'Họ tên hoặc năm sinh không hợp lệ';

            header('Location: index.php');
            exit;
        }

        // Gọi Model để lưu dữ liệu
        $this->patientModel->create(
            $name,
            $birthYear
        );

        // Chuyển về trang danh sách
        header('Location: index.php');
        exit;
    }
}
