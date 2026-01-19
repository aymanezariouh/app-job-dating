<?php
namespace App\models;

use App\core\Model;

class User extends Model
{
    protected $table = 'users';
    
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt->fetch();
    }
}