<?php

namespace App\core;

use App\models\User;

class Auth
{
    public array $errors = [];

    public function login($email, $password)
    {
        $userObj = new User();
        $user = $userObj->findByEmail($email);

        if (!empty($user) && Security::verify($password, $user['password'])) {
            Session::set('userId', (int)$user['id']);
            Session::set('role', (string)$user['role']);
            Session::set('last_activity', time());
            return true;
        }

        $this->errors[] = "Invalid email or password.";
        return false;
    }

    public function logout()
    {
        Session::destroy();
    }

    public function isLoggedIn()
    {
        $userId = Session::get('userId');
        if (!$userId) return false;

        $last = (int) Session::get('last_activity', 0);
        $now = time();

        if ($last > 0 && ($now - $last) > 7200) {
            $this->logout();
            return false;
        }

        Session::set('last_activity', $now);
        return true;
    }

    public function role()
    {
        return Session::get('role');
    }

    public function requireAuth()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function requireAdmin()
    {
        $this->requireAuth();
        if ($this->role() !== 'admin') {
            http_response_code(403);
            echo "403 Forbidden";
            exit;
        }
    }

    public function requireStudent()
    {
        $this->requireAuth();
        if ($this->role() !== 'student') {
            http_response_code(403);
            echo "403 Forbidden";
            exit;
        }
    }

    public function register($name, $email, $password, $role = 'student')
    {
        $this->errors = [];

        // Validate input
        $validator = new Validator([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        $validator->required(['name', 'email', 'password'])
                  ->email('email')
                  ->min('password', 6);

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        // Check if email already exists
        $userObj = new User();
        $existingUser = $userObj->findByEmail($email);

        if ($existingUser) {
            $this->errors['email'] = "Email already registered.";
            return false;
        }

        // Create user
        $hashedPassword = Security::hash($password);
        $userData = [
            'name' => Security::sanitize($name),
            'email' => Security::sanitize($email),
            'password' => $hashedPassword,
            'role' => $role
        ];

        try {
            $userObj->create($userData);
            return true;
        } catch (\Exception $e) {
            $this->errors[] = "Registration failed. Please try again.";
            return false;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
