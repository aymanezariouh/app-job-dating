<?php
namespace App\core;

class Validator
{
    private $data;
    private $errors = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function required($fields)
    {
        foreach ((array)$fields as $field) {
            if (empty($this->data[$field])) {
                $this->errors[$field] = "$field is required";
            }
        }
        return $this;
    }

    public function email($field)
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$field must be a valid email";
        }
        return $this;
    }

    public function min($field, $length)
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = "$field must be at least $length characters";
        }
        return $this;
    }

    public function max($field, $length)
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = "$field must not exceed $length characters";
        }
        return $this;
    }

    public function numeric($field)
    {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = "$field must be numeric";
        }
        return $this;
    }

    public function fails()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
    }
}
