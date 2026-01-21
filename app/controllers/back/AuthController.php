<?php

namespace App\controllers\back;

use App\core\Controller;
use App\core\Security;

class AuthController extends Controller
{
    public function login()
    {
        $csrf = Security::csrfToken();
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        return $this->render('/login.twig', [
            'csrf_token' => $csrf,
            'error' => $error
        ]);
    }

    public function loginSubmit()
    {
        $email = Security::sanitize($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $token = (string)($_POST['csrf_token'] ?? '');

        if (!Security::verifyCsrfToken($token)) {
            http_response_code(419);
            echo "CSRF token invalid";
            exit;
        }

        if ($this->isLocked($email)) {
            $_SESSION['flash_error'] = "Too many attempts. Try again later.";
            header('Location: /login');
            exit;
        }

        if ($this->auth->login($email, $password)) {
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
