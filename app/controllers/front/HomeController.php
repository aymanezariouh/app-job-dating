<?php

namespace App\controllers\front;

use App\core\Controller;
use App\core\View;

class HomeController extends Controller
{
    public function index()
    {
        return $this->render('home/index', [
            'title' => 'Welcome to Framework Minimaliste'
        ]);
    }

    public function about()
    {
        return $this->render('home/about', [
            'title' => 'About Us'
        ]);
    }

    public function login()
    {
        return $this->render('auth/login', []);
    }

    public function loginSubmit()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if ($this->auth->login($email, $password)) {
            header('Location: /');
            exit;
        } else {
            $errors = $this->auth->errors();
            return $this->render('auth/login', ['errors' => $errors]);
        };
    }

    public function register()
    {
        return $this->render('auth/register', ['errors' => []]);
    }

    public function registerSubmit()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($this->auth->register($name, $email, $password)) {
            header('Location: /login');
            exit;
        }
        
        return $this->render('auth/register', ['errors' => $this->auth->errors()]);
    }
}
