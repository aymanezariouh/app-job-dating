<?php

namespace App\models;

use App\core\Database;

class Company
{
    public function all(): array
    {
        $stmt = Database::query(
            "SELECT id, name FROM companies ORDER BY name ASC"
        );
        return $stmt->fetchAll();
    }

    public function allFull(): array
    {
        $stmt = Database::query(
            "SELECT * FROM companies ORDER BY id DESC"
        );
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        $stmt = Database::query(
            "SELECT COUNT(*) AS total FROM companies"
        );
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function find(int $id): ?array
    {
        $stmt = Database::query(
            "SELECT * FROM companies WHERE id = :id LIMIT 1",
            ['id' => $id]
        );

        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): void
    {
        Database::query(
            "INSERT INTO companies (name, sector, location, email, phone, avatar)
             VALUES (:name, :sector, :location, :email, :phone, :avatar)",
            [
                'name' => $data['name'],
                'sector' => $data['sector'],
                'location' => $data['location'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'avatar' => $data['avatar'],
            ]
        );
    }

    public function updateById(int $id, array $data): void
    {
        Database::query(
            "UPDATE companies
             SET name = :name,
                 sector = :sector,
                 location = :location,
                 email = :email,
                 phone = :phone,
                 avatar = :avatar
             WHERE id = :id",
            [
                'id' => $id,
                'name' => $data['name'],
                'sector' => $data['sector'],
                'location' => $data['location'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'avatar' => $data['avatar'],
            ]
        );
    }

    public function deleteById(int $id): void
    {
        Database::query(
            "DELETE FROM companies WHERE id = :id",
            ['id' => $id]
        );
    }
}
