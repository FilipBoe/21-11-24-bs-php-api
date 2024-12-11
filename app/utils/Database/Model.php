<?php

namespace App\Utils\Database;

use PDO;

abstract class Model
{
    protected string $table;
    protected array $attributes;
    protected array $values = [];

    public function all(string $filters = '', array $data = []): array
    {
        /** @var PDO $conn */
        $conn = app(Connection::class)->getConnection();

        $stmt = $conn->prepare("SELECT * FROM {$this->table} " . $filters);

        $stmt->execute($data);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($item) => (new static())->parse($item), $data);
    }

    public function find(int $id): self
    {
        $conn = app(Connection::class)->getConnection();

        $stmt = $conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");

        $stmt->execute(['id' => $id]);

        $this->parse($stmt->fetch(PDO::FETCH_ASSOC));

        return $this;
    }

    public function queryOne(string $filters, array $data = []): ?User
    {
        $conn = app(Connection::class)->getConnection();

        $stmt = $conn->prepare("SELECT * FROM {$this->table} $filters");

        $stmt->execute($data);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return (new static())->parse($data);
    }

    public function create(array $data): int
    {
        $conn = app(Connection::class)->getConnection();

        $columns = implode(', ', array_map(fn($key) => "`$key`", array_keys($data)));
        $values = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));

        $stmt = $conn->prepare("INSERT INTO {$this->table} ($columns) VALUES ($values)");

        $stmt->execute($data);

        return (int) $conn->lastInsertId();
    }

    public function updateOrNew(array $rows, array $unique = ['id']): array
    {
        $ids = [];
        $conn = app(Connection::class)->getConnection();

        foreach ($rows as $row) {
            $rowExistsQuery = $conn->prepare("SELECT id FROM {$this->table} WHERE " . implode(' AND ', array_map(fn($key) => "`$key` = :$key", $unique)));

            $bindParams = [];

            foreach ($unique as $key) {
                $bindParams[':' . $key] = $row[$key];
            }

            $rowExistsQuery->execute($bindParams);

            $rowExists = $rowExistsQuery->fetch(PDO::FETCH_ASSOC);

            if ($rowExists) {
                $id = (new static())->parse($rowExists)->get('id');

                $updateStmt = $conn->prepare("UPDATE {$this->table} SET " . implode(', ', array_map(fn($key) => "`$key` = :$key", array_keys($row))) . " WHERE id = :id");
                $updateStmt->execute(array_merge($row, ['id' => $id]));

                $ids[] = $id;
                continue;
            }

            $ids[] = $this->create($row);
        }

        return $ids;
    }

    public function delete(int $id): bool
    {
        $conn = app(Connection::class)->getConnection();

        $stmt = $conn->prepare("DELETE FROM {$this->table} WHERE id = :id");

        return $stmt->execute(['id' => $id]);
    }

    public function parse(array $data): self
    {
        $this->values = $data;

        return $this;
    }

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }

    public function unset(string $key): self
    {
        unset($this->values[$key]);

        return $this;
    }

    public function toArray(): array
    {
        return $this->values;
    }
}
