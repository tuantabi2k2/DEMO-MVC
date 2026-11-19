<?php
declare(strict_types=1);
final class Patient
{
    public function __construct(
        private PDO $pdo
    ) {}

    // Lấy tất cả bệnh nhân
    public function all(): array
    {
        $sql = "
            SELECT id, name, birth_year
            FROM patients
            ORDER BY id DESC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    // Thêm bệnh nhân
    public function create(
        string $name,
        int $birthYear
    ): void {
        $sql = "
            INSERT INTO patients (name, birth_year)
            VALUES (:name, :birth_year)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'name' => $name,
            'birth_year' => $birthYear
        ]);
    }
}
