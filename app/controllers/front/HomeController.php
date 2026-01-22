<?php

namespace App\controllers\front;

use App\core\Controller;
use App\models\Announcement;
use App\models\Student;

class HomeController extends Controller
{
    public function index()
    {
        $this->auth->requireAuth();
        $this->auth->requireStudent();

        $userId = $_SESSION['userId'] ?? null;
        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $studentModel = new Student();
        $studentData = $studentModel->getStudentByUserId((int)$userId);

        $announcementModel = new Announcement();
        $jobs = $announcementModel->allWithCompany(false);
        $totalJobs = $announcementModel->countActive();

        return $this->render('frontend/index.twig', [
            'student' => $studentData,
            'jobs' => $jobs,
            'totalJobs' => $totalJobs
        ]);
    }

    public function jobs()
    {
        return $this->index();
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
