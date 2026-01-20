<?php
namespace App\core;

class Security
{
    public static function sanitize($data)
    {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public static function hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

   public static function csrfToken()
{
    $csrfSession = Session::get('csrf_token', null);
    if (!$csrfSession) {
        $csrfSession = self::generateToken();
        Session::set('csrf_token', $csrfSession);
    }
    return $csrfSession;
}

    public static function verifyCsrfToken($token)
    {
        $csrfSession = Session::get('csrf_token', null);
        return $csrfSession && hash_equals($csrfSession, $token);
    }
}
