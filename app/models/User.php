<?php
namespace App\models;

use App\core\Model;

class User extends Model
{
    protected $table = 'users';
    private $role;
    
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt->fetch();
    }

    public function getRole()
    {
        return $this->role;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStudent()
    {
    return $this->role === 'student';
    }


}