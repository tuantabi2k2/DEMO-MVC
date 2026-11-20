<?php
declare(strict_types=1);

final class Patient
{
    public function __construct(private PDO $pdo) {}

    private function conditions(string $q, ?int $year): array
    {
        $where = [];
        $params = [];
        if ($q !== '') {
            // ESCAPE '!' để %, _ trong từ khóa được hiểu là ký tự thường.
            $term = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $q);
            $params[':q'] = '%' . $term . '%';
            if (ctype_digit($q) && strlen($q) < 10) {
                $where[] = "(name LIKE :q ESCAPE '!' OR id = :exact_id)";
                $params[':exact_id'] = (int) $q;
            } else {
                $where[] = "name LIKE :q ESCAPE '!'";
            }
        }
        if ($year !== null) {
            $where[] = 'birth_year = :birth_year';
            $params[':birth_year'] = $year;
        }
        return [$where ? ' WHERE ' . implode(' AND ', $where) : '', $params];
    }

    private function bind(PDOStatement $stmt, array $params): void
    {
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    }

    public function countMatching(string $q = '', ?int $year = null): int
    {
        [$where, $params] = $this->conditions($q, $year);
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM patients' . $where);
        $this->bind($stmt, $params);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function paged(string $q, ?int $year, int $page, int $limit): array
    {
        [$where, $params] = $this->conditions($q, $year);
        $stmt = $this->pdo->prepare(
            'SELECT id, name, birth_year, created_at FROM patients' . $where .
            ' ORDER BY id DESC LIMIT :limit OFFSET :offset'
        );
        $this->bind($stmt, $params);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, birth_year, created_at FROM patients WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, int $year): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO patients (name, birth_year) VALUES (:name, :year)'
        );
        $stmt->execute([':name' => $name, ':year' => $year]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, int $year): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE patients SET name = :name, birth_year = :year WHERE id = :id'
        );
        $stmt->execute([':name' => $name, ':year' => $year, ':id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM patients WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
