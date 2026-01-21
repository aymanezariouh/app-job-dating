<?php

namespace App\controllers\back;

use App\core\Controller;
use App\models\Announcement;
use App\models\Company;
use App\models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $this->auth->requireAdmin();

        $ann = new Announcement();
        $comp = new Company();
        $stud = new Student();

        $stats = [
            'active_announcements' => $ann->countActive(),
            'archived_announcements' => $ann->countArchived(),
            'companies' => $comp->countAll(),
            'students' => $stud->countAll(),
        ];

        $latest = $ann->latestActive(3);

        return $this->render('back/dashboard/index.twig', [
            'stats' => $stats,
            'latest_announcements' => $latest
        ]);
    }
}
