<?php

namespace App\controllers\front;

use App\core\Controller;
use App\core\Session;
use App\models\Announcement;
use App\models\Student;

class HomeController extends Controller
{
    public function index()
    {
        $userId = 5;

        $studentData = null;
        if ($userId) {
            $studentModel = new Student();
            $studentData = $studentModel->getStudent($userId);
        }

        $announcementModel = new Announcement();
        $jobs = $announcementModel->allWithCompany();
        $totalJobs = $announcementModel->countActive();

        return $this->render('frontend/index.twig', [
            'student' => $studentData,
            'jobs' => $jobs,
            'totalJobs' => $totalJobs
        ]);
    }
}
