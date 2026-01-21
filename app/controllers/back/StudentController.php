<?php

namespace App\controllers\back;

use App\core\Controller;
use App\models\Student;

class StudentController extends Controller
{
    private Student $studentModel;

    public function __construct()
    {
        parent::__construct();
        $this->studentModel = new Student();
    }

    public function index()
    {
        $this->auth->requireAdmin();

        $students = $this->studentModel->allWithUsers();

        return $this->render('back/students/index.twig', [
            'students' => $students
        ]);
    }
}
