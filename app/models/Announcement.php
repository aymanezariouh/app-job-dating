<?php

namespace App\models;

use App\core\Database;

class Announcement
{
    public function latestActive(int $limit = 3): array
    {
        $limit = max(1, min(20, (int)$limit));

        $stmt = Database::query(
            "SELECT a.id, a.title, a.location, a.contract_type, a.created_at, c.name AS company_name
             FROM announcements a
             JOIN companies c ON c.id = a.company_id
             WHERE a.deleted = 0
             ORDER BY a.created_at DESC
             LIMIT {$limit}"
        );

        return $stmt->fetchAll();
    }

    public function countActive(): int
    {
        $stmt = Database::query("SELECT COUNT(*) AS total FROM announcements WHERE deleted = 0");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function countArchived(): int
    {
        $stmt = Database::query("SELECT COUNT(*) AS total FROM announcements WHERE deleted = 1");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function allWithCompany(bool $includeArchived = false): array
    {
        if ($includeArchived) {
            $stmt = Database::query(
                "SELECT a.*, c.name AS company_name
                 FROM announcements a
                 JOIN companies c ON c.id = a.company_id
                 ORDER BY a.created_at DESC"
            );
            return $stmt->fetchAll();
        }

        $stmt = Database::query(
            "SELECT a.*, c.name AS company_name
             FROM announcements a
             JOIN companies c ON c.id = a.company_id
             WHERE a.deleted = 0
             ORDER BY a.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::query(
            "SELECT * FROM announcements WHERE id = :id LIMIT 1",
            ['id' => $id]
        );

        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        Database::query(
            "INSERT INTO announcements
             (title, company_id, contract_type, location, description, skills, deleted, created_at, updated_at)
             VALUES
             (:title, :company_id, :contract_type, :location, :description, :skills, 0, NOW(), NOW())",
            [
                'title' => $data['title'],
                'company_id' => $data['company_id'],
                'contract_type' => $data['contract_type'],
                'location' => $data['location'],
                'description' => $data['description'],
                'skills' => $data['skills'],
            ]
        );

        return (int)Database::pdo()->lastInsertId();
    }

    public function updateById(int $id, array $data): void
    {
        Database::query(
            "UPDATE announcements
             SET title = :title,
                 company_id = :company_id,
                 contract_type = :contract_type,
                 location = :location,
                 description = :description,
                 skills = :skills,
                 updated_at = NOW()
             WHERE id = :id",
            [
                'id' => $id,
                'title' => $data['title'],
                'company_id' => $data['company_id'],
                'contract_type' => $data['contract_type'],
                'location' => $data['location'],
                'description' => $data['description'],
                'skills' => $data['skills'],
            ]
        );
    }

    public function archive(int $id): void
    {
        Database::query(
            "UPDATE announcements SET deleted = 1, updated_at = NOW() WHERE id = :id",
            ['id' => $id]
        );
    }

    public function restore(int $id): void
    {
        Database::query(
            "UPDATE announcements SET deleted = 0, updated_at = NOW() WHERE id = :id",
            ['id' => $id]
        );
    }
}
