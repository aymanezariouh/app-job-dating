<?php

namespace App\controllers\front;

use App\core\Controller;
use App\core\Security;
use App\models\Announcement;
use App\models\Student;
use App\models\Application;

class JobController extends Controller
{
    public function show()
    {
        $this->auth->requireAuth();
        if (method_exists($this->auth, 'requireStudent')) {
            $this->auth->requireStudent();
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /jobs');
            exit;
        }

        $announcementModel = new Announcement();
        $job = $announcementModel->findActiveWithCompany($id);

        if (!$job) {
            header('Location: /jobs');
            exit;
        }

        return $this->render('front/jobs/show.twig', [
            'job' => $job,
        ]);
    }

    public function apply()
    {
        $this->auth->requireAuth();
        if (method_exists($this->auth, 'requireStudent')) {
            $this->auth->requireStudent();
        }

        $userId = (int)($_SESSION['userId'] ?? 0);
        if ($userId <= 0) {
            header('Location: /login');
            exit;
        }

        $jobId = (int)($_GET['id'] ?? 0);
        if ($jobId <= 0) {
            header('Location: /jobs');
            exit;
        }

        $announcementModel = new Announcement();
        $job = $announcementModel->findActiveWithCompany($jobId);
        if (!$job) {
            header('Location: /jobs');
            exit;
        }

        $studentModel = new Student();
        $student = $studentModel->getStudent($userId);
        if (!$student) {
            header('Location: /jobs');
            exit;
        }

        $applicationModel = new Application();
        $alreadyApplied = $applicationModel->exists((int)$student['id'], $jobId);

        $errors = $_SESSION['form_errors'] ?? [];
        $old = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);

        return $this->render('front/jobs/apply.twig', [
            'job' => $job,
            'student' => $student,
            'alreadyApplied' => $alreadyApplied,
            'csrf_token' => Security::csrfToken(),
            'errors' => $errors,
            'old' => $old
        ]);
    }

    public function applySubmit()
    {
        $this->auth->requireAuth();
        if (method_exists($this->auth, 'requireStudent')) {
            $this->auth->requireStudent();
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!Security::verifyCsrfToken($token)) {
            http_response_code(419);
            echo 'CSRF token invalid';
            exit;
        }

        $userId = (int)($_SESSION['userId'] ?? 0);
        if ($userId <= 0) {
            header('Location: /login');
            exit;
        }

        $jobId = (int)($_GET['id'] ?? 0);
        if ($jobId <= 0) {
            header('Location: /jobs');
            exit;
        }

        $studentModel = new Student();
        $student = $studentModel->getStudent($userId);
        if (!$student) {
            header('Location: /jobs');
            exit;
        }

        $message = trim((string)($_POST['message'] ?? ''));
        $errors = [];

        if ($message === '') {
            $errors['message'] = 'Message is required.';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = ['message' => $message];
            header('Location: /jobs/apply?id=' . $jobId);
            exit;
        }

        $applicationModel = new Application();
        if ($applicationModel->exists((int)$student['id'], $jobId)) {
            $_SESSION['flash_error'] = 'You already applied to this job.';
            header('Location: /jobs/show?id=' . $jobId);
            exit;
        }

        $applicationModel->create((int)$student['id'], $jobId, $message);

        $_SESSION['flash_success'] = 'Application sent successfully.';
        header('Location: /jobs/show?id=' . $jobId);
        exit;
    }
}
