<?php

namespace App\models;

use App\core\Database;
use App\core\Model;

class Student extends User
{
    public function countAll(): int
    {
        $stmt = Database::query("SELECT COUNT(*) AS total FROM students");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function getAllstudents(): array
    {
        $stmt = Database::query(
            "SELECT s.id,
                    s.promotion,
                    s.specialization,
                    u.id AS user_id,
                    u.name,
                    u.email
             FROM students s
             JOIN users u ON u.id = s.user_id
             ORDER BY s.id DESC"
        );

        return $stmt->fetchAll();
    }

    public function getStudent($id)
    {
        $stmt = Database::query(
                "SELECT s.id,
                    s.promotion,
                    s.specialization,
                    u.id AS user_id,
                    u.name,
                    u.email
             FROM students s
             JOIN users u ON u.id = s.user_id
             WHERE s.id = :id",
            [':id' => $id]
        );
        return $stmt->fetch();
    }
}
