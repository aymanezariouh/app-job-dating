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
}
