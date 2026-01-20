<?php

namespace App\controllers\back;

use App\core\Controller;
use App\core\Security;

class AuthController extends Controller
{
    private string $csrfKey = 'csrf_login';

    public function login()
    {
        $token = Security::csrfToken($this->csrfKey);
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
echo $this->render('back/auth/login.twig', [
    'csrf_token' => $token,
    'error' => $error
]);
exit;
        return $this->render('auth/login.twig', [
            'csrf_token' => $token,
            'error' => $error
        ]);
    }

    public function loginSubmit()
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $csrf = (string)($_POST['csrf_token'] ?? '');

        if (!Security::validateCsrf($csrf, $this->csrfKey)) {
            http_response_code(419);
            echo "CSRF token invalid";
            exit;
        }

        if ($this->isLocked($email)) {
            $_SESSION['flash_error'] = "Too many attempts. Try again later.";
            header('Location: /login');
            exit;
        }

        if ($this->auth->attempt($email, $password)) {
            $this->clearAttempts($email);

            if ($this->auth->role() === 'admin') {
                header('Location: /admin/dashboard');
                exit;
            }

            header('Location: /jobs');
            exit;
        }

        $this->addAttempt($email);
        $_SESSION['flash_error'] = "Invalid credentials";
        header('Location: /login');
        exit;
    }

    public function logout()
    {
        $this->auth->logout();
        Security::invalidateCsrf($this->csrfKey);
        header('Location: /login');
        exit;
    }

    private function attemptsKey(string $email): string
    {
        return 'login_attempts_' . sha1(strtolower($email));
    }

    private function lockKey(string $email): string
    {
        return 'login_lock_' . sha1(strtolower($email));
    }

    private function isLocked(string $email): bool
    {
        $lockUntil = (int)($_SESSION[$this->lockKey($email)] ?? 0);
        if ($lockUntil === 0) return false;

        if (time() >= $lockUntil) {
            unset($_SESSION[$this->lockKey($email)]);
            unset($_SESSION[$this->attemptsKey($email)]);
            return false;
        }

        return true;
    }

    private function addAttempt(string $email): void
    {
        $key = $this->attemptsKey($email);
        $count = (int)($_SESSION[$key] ?? 0);
        $count++;
        $_SESSION[$key] = $count;

        if ($count >= 5) {
            $_SESSION[$this->lockKey($email)] = time() + 600;
        }
    }

    private function clearAttempts(string $email): void
    {
        unset($_SESSION[$this->attemptsKey($email)]);
        unset($_SESSION[$this->lockKey($email)]);
    }
}
