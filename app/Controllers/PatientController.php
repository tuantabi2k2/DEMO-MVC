<?php
declare(strict_types=1);

final class PatientController
{
    public function __construct(private Patient $patients) {}

    private function id(): int
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);
        if ($id === false || $id === null) {
            http_response_code(400);
            exit('ID không hợp lệ.');
        }
        return $id;
    }

    private function getPatient(): array
    {
        $patient = $this->patients->find($this->id());
        if ($patient === null) {
            http_response_code(404);
            exit('Không tìm thấy bệnh nhân.');
        }
        return $patient;
    }

    public function index(): void
    {
        requireWebAuth();
        $q = $_GET['q'] ?? '';
        $q = is_string($q) ? trim($q) : '';
        $yearRaw = $_GET['year'] ?? '';
        $year = null;
        if (is_string($yearRaw) && $yearRaw !== '') {
            $parsed = filter_var($yearRaw, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1900, 'max_range' => (int) date('Y')]
            ]);
            if ($parsed !== false) $year = $parsed;
        }
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]) ?: 1;
        $limit = 10;
        $total = $this->patients->countMatching($q, $year);
        $totalPages = max(1, (int) ceil($total / $limit));
        $page = min($page, $totalPages);
        $patients = $this->patients->paged($q, $year, $page, $limit);
        $success = $_SESSION['flash_success'] ?? null;
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        require __DIR__ . '/../Views/patients/index.php';
    }

    public function show(): void
    {
        requireWebAuth();
        $patient = $this->getPatient();
        require __DIR__ . '/../Views/patients/show.php';
    }

    public function edit(): void
    {
        requireWebAuth();
        $patient = $this->getPatient();
        require __DIR__ . '/../Views/patients/edit.php';
    }

    public function store(): void
    {
        requireWebAuth();
        requireWebCsrf();
        try {
            [$name, $year] = patientInput($_POST);
            $this->patients->create($name, $year);
            redirectWithMessage('Đã thêm bệnh nhân thành công.');
        } catch (InvalidArgumentException $e) {
            redirectWithMessage($e->getMessage(), 'error');
        }
    }

    public function update(): void
    {
        requireWebAuth();
        requireWebCsrf();
        $patient = $this->getPatient();
        try {
            [$name, $year] = patientInput($_POST);
            $this->patients->update((int) $patient['id'], $name, $year);
            redirectWithMessage('Đã cập nhật bệnh nhân.');
        } catch (InvalidArgumentException $e) {
            redirectWithMessage($e->getMessage(), 'error');
        }
    }

    public function destroy(): void
    {
        requireWebAuth();
        requireWebCsrf();
        $patient = $this->getPatient();
        $this->patients->delete((int) $patient['id']);
        redirectWithMessage('Đã xóa bệnh nhân.');
    }
}
