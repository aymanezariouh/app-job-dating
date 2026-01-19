<?php
namespace App\core;

class Session
{
    public static function start($lifetime = 3600)
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', $lifetime);
            session_set_cookie_params($lifetime);
            session_start();
        }
    }
    
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }
    
    public static function destroy()
    {
        self::start();
        session_destroy();
    }
}