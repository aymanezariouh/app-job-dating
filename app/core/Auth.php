<?php

namespace App\core;

use App\models\User;

class Auth
{
    private array $errors = [];

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

    public function getErrors()
    {
        return $this->errors;
    }
}
