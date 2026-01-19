<?php
namespace App\core;
use PDO;
use PDOException;
class Model 
{
    protected $db;
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $stmt = $this->db->query($sql,[$id])->fetch();
    }

    public function findby($data)
    {
        $conditions = array_map(fn($key) => "$key = :$key", array_keys($data));
        $whereClause = implode(' AND ', $conditions);
        $params = array_combine(
            array_map(fn($key) => ":$key", array_keys($data)),
            array_values($data)
        );
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $keys = implode(',',array_keys($data));
        $placeHolders = ':' . implode(', :',array_keys($data));
        $data = array_combine(
            array_map(fn($key)=> ":$key",array_keys($data)),
            array_values($data)
            );
        $sql = "INSERT INTO {$this->table}({$keys}) VALUES ({$placeHolders})";
        $stmt = $this->db->query($sql, $data);
        return $stmt;
    }

    public function update($id, $data)
    {
        $fields = array_map(fn($key) => "$key = :$key", array_keys($data));
        $setClause = implode(', ', $fields);
        $params = array_combine(
            array_map(fn($key) => ":$key", array_keys($data)),
            array_values($data)
        );
        $params[':id'] = $id;
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        $stmt = $this->db->query($sql, $params);
        return $stmt;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt;
    }
}