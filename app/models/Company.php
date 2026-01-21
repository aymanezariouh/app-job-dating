<?php

namespace App\models;

use App\core\Database;
use App\core\Model;

class Company extends Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'companies';
    }

    public function countAll(): int
    {
        $stmt = Database::query(
            "SELECT COUNT(*) AS total FROM companies"
        );
        return (int)($stmt->fetch()['total'] ?? 0);
    }


    public function addCompany(array $data): void
    {
        $this->create($data);
    }

    public function updateById(int $id, array $data): void
    {
        $this->update($id, $data);
    }

    public function deleteById(int $id): void
    {
        $this->delete($id);
    }
}
