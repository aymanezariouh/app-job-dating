<?php

namespace App\models;

use App\core\Database;

class Company
{
    public function all(): array
    {
        $stmt = Database::query("SELECT id, name FROM companies ORDER BY name ASC");
        return $stmt->fetchAll();
    }
    public function countAll(): int
{
    $stmt = Database::query("SELECT COUNT(*) AS total FROM companies");
    return (int)($stmt->fetch()['total'] ?? 0);
}

}
