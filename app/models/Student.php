<?php

namespace App\models;

use App\core\Database;

class Student
{
    public function countAll(): int
    {
        $stmt = Database::query("SELECT COUNT(*) AS total FROM students");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function allWithUsers(): array
    {
        $stmt = Database::query(
            "SELECT s.id,
                    s.promotion,
                    s.specialization,
                    u.id AS user_id,
                    u.name,
                    u.email,
                    u.created_at
             FROM students s
             JOIN users u ON u.id = s.user_id
             ORDER BY s.id DESC"
        );

        return $stmt->fetchAll();
    }
}
