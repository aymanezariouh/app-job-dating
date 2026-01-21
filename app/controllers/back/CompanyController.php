<?php

namespace App\controllers\back;

use App\core\Controller;
use App\core\Security;
use App\models\Company;

class CompanyController extends Controller
{
    private Company $companyModel;

    public function __construct()
    {
        parent::__construct();
        $this->companyModel = new Company();
    }

    public function index()
    {
        $this->auth->requireAdmin();

        $companies = $this->companyModel->allFull();

        $flash = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        return $this->render('back/companies/index.twig', [
            'companies' => $companies,
            'flash_success' => $flash,
            'csrf_token' => Security::csrfToken(),
        ]);
    }

    public function create()
    {
        $this->auth->requireAdmin();

        $errors = $_SESSION['form_errors'] ?? [];
        $old = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);

        return $this->render('back/companies/create.twig', [
            'csrf_token' => Security::csrfToken(),
            'errors' => $errors,
            'old' => $old,
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
            header('Location: /admin/companies/create');
            exit;
        }

        $this->companyModel->create($validated['clean']);

        $_SESSION['flash_success'] = "Company created.";
        header('Location: /admin/companies');
        exit;
    }

    public function edit()
    {
        $this->auth->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /admin/companies');
            exit;
        }

        $company = $this->companyModel->find($id);
        if (!$company) {
            header('Location: /admin/companies');
            exit;
        }

        $errors = $_SESSION['form_errors'] ?? [];
        $old = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);

        return $this->render('back/companies/edit.twig', [
            'company' => $company,
            'csrf_token' => Security::csrfToken(),
            'errors' => $errors,
            'old' => $old,
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
            header('Location: /admin/companies');
            exit;
        }

        $company = $this->companyModel->find($id);
        if (!$company) {
            header('Location: /admin/companies');
            exit;
        }

        $validated = $this->validate($_POST);

        if (!empty($validated['errors'])) {
            $_SESSION['form_errors'] = $validated['errors'];
            $_SESSION['form_old'] = $validated['old'];
            header('Location: /admin/companies/edit?id=' . $id);
            exit;
        }

        $this->companyModel->updateById($id, $validated['clean']);

        $_SESSION['flash_success'] = "Company updated.";
        header('Location: /admin/companies');
        exit;
    }

    public function delete()
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
            $this->companyModel->deleteById($id);
            $_SESSION['flash_success'] = "Company deleted.";
        }

        header('Location: /admin/companies');
        exit;
    }

    private function validate(array $post): array
    {
        $errors = [];

        $old = [
            'name' => trim((string)($post['name'] ?? '')),
            'sector' => trim((string)($post['sector'] ?? '')),
            'location' => trim((string)($post['location'] ?? '')),
            'email' => trim((string)($post['email'] ?? '')),
            'phone' => trim((string)($post['phone'] ?? '')),
            'avatar' => trim((string)($post['avatar'] ?? '')),
        ];

        if ($old['name'] === '') $errors['name'] = 'Name is required.';
        if ($old['email'] !== '' && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';

        $clean = [
            'name' => Security::sanitize($old['name']),
            'sector' => Security::sanitize($old['sector']),
            'location' => Security::sanitize($old['location']),
            'email' => Security::sanitize($old['email']),
            'phone' => Security::sanitize($old['phone']),
            'avatar' => Security::sanitize($old['avatar']),
        ];

        return [
            'errors' => $errors,
            'old' => $old,
            'clean' => $clean,
        ];
    }
}
