<?php

namespace App\controllers\back;

use App\core\Controller;
use App\core\Security;
use App\models\Announcement;
use App\models\Company;

class AnnouncementController extends Controller
{
    private Announcement $announcementModel;
    private Company $companyModel;

    public function __construct()
    {
        parent::__construct();
        $this->announcementModel = new Announcement();
        $this->companyModel = new Company();
    }

    public function index()
    {
        $this->auth->requireAdmin();

        $includeArchived = isset($_GET['all']) && $_GET['all'] === '1';
        $announcements = $this->announcementModel->allWithCompany($includeArchived);

        $flash = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        return $this->render('/back/announcements/index.twig', [
            'announcements' => $announcements,
            'includeArchived' => $includeArchived,
            'flash_success' => $flash,
            'csrf_token' => Security::csrfToken()
        ]);
    }

    public function create()
    {
        $this->auth->requireAdmin();

        $companies = $this->companyModel->all();

        $errors = $_SESSION['form_errors'] ?? [];
        $old = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);

        return $this->render('/back/announcements/create.twig', [
            'companies' => $companies,
            'csrf_token' => Security::csrfToken(),
            'errors' => $errors,
            'old' => $old
        ]);
    }

    public function store()
    {
        $this->auth->requireAdmin();

        $token = (string)($_POST['csrf_token'] ?? '');
        if (!Security::verifyCsrfToken($token)) {
            http_response_code(419);
            echo "CSRF token invalid";
            exit;
        }

        $validated = $this->validate($_POST);

        if (!empty($validated['errors'])) {
            $_SESSION['form_errors'] = $validated['errors'];
            $_SESSION['form_old'] = $validated['old'];
            header('Location: /admin/announcements/create');
            exit;
        }

        $this->announcementModel->create($validated['clean']);

        $_SESSION['flash_success'] = "Announcement created.";
        header('Location: /admin/announcements');
        exit;
    }

    public function edit()
    {
        $this->auth->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /admin/announcements');
            exit;
        }

        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            header('Location: /admin/announcements');
            exit;
        }

        $companies = $this->companyModel->all();

        $errors = $_SESSION['form_errors'] ?? [];
        $old = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);

        return $this->render('/back/announcements/edit.twig', [
            'announcement' => $announcement,
            'companies' => $companies,
            'csrf_token' => Security::csrfToken(),
            'errors' => $errors,
            'old' => $old
        ]);
    }
    public function archived()
{
    $this->auth->requireAdmin();

    $archived = $this->announcementModel->archivedWithCompany();

    $flash = $_SESSION['flash_success'] ?? null;
    unset($_SESSION['flash_success']);

    return $this->render('back/archived/index.twig', [
        'announcements' => $archived,
        'flash_success' => $flash,
        'csrf_token' => Security::csrfToken()
    ]);
}

    public function update()
    {
        $this->auth->requireAdmin();

        $token = (string)($_POST['csrf_token'] ?? '');
        if (!Security::verifyCsrfToken($token)) {
            http_response_code(419);
            echo "CSRF token invalid";
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /admin/announcements');
            exit;
        }

        $announcement = $this->announcementModel->find($id);
        if (!$announcement) {
            header('Location: /admin/announcements');
            exit;
        }

        $validated = $this->validate($_POST);

        if (!empty($validated['errors'])) {
            $_SESSION['form_errors'] = $validated['errors'];
            $_SESSION['form_old'] = $validated['old'];
            header('Location: /admin/announcements/edit?id=' . $id);
            exit;
        }

        $this->announcementModel->updateById($id, $validated['clean']);

        $_SESSION['flash_success'] = "Announcement updated.";
        header('Location: /admin/announcements');
        exit;
    }

    public function archive()
    {
        $this->auth->requireAdmin();

        $token = (string)($_POST['csrf_token'] ?? '');
        if (!Security::verifyCsrfToken($token)) {
            http_response_code(419);
            echo "CSRF token invalid";
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->announcementModel->archive($id);
            $_SESSION['flash_success'] = "Announcement archived.";
        }

        header('Location: /admin/announcements');
        exit;
    }

    public function restore()
    {
        $this->auth->requireAdmin();

        $token = (string)($_POST['csrf_token'] ?? '');
        if (!Security::verifyCsrfToken($token)) {
            http_response_code(419);
            echo "CSRF token invalid";
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->announcementModel->restore($id);
            $_SESSION['flash_success'] = "Announcement restored.";
        }

        header('Location: /admin/announcements?all=1');
        exit;
    }

    private function validate(array $post): array
    {
        $errors = [];

        $old = [
            'title' => trim((string)($post['title'] ?? '')),
            'company_id' => (string)($post['company_id'] ?? ''),
            'contract_type' => trim((string)($post['contract_type'] ?? '')),
            'location' => trim((string)($post['location'] ?? '')),
            'description' => trim((string)($post['description'] ?? '')),
            'skills' => trim((string)($post['skills'] ?? '')),
        ];

        if ($old['title'] === '') $errors['title'] = 'Title is required.';
        if ($old['company_id'] === '' || !ctype_digit($old['company_id'])) $errors['company_id'] = 'Company is required.';
        if ($old['contract_type'] === '') $errors['contract_type'] = 'Contract type is required.';
        if ($old['location'] === '') $errors['location'] = 'Location is required.';
        if ($old['description'] === '') $errors['description'] = 'Description is required.';

        $clean = [
            'title' => Security::sanitize($old['title']),
            'company_id' => (int)$old['company_id'],
            'contract_type' => Security::sanitize($old['contract_type']),
            'location' => Security::sanitize($old['location']),
            'description' => $old['description'],
            'skills' => Security::sanitize($old['skills']),
        ];

        return [
            'errors' => $errors,
            'old' => $old,
            'clean' => $clean
        ];
    }
}
