<?php
/**
 * Session Management Class
 */

class Session {
    
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public static function destroy() {
        self::start();
        session_unset();
        session_destroy();
    }
    
    public static function flash($key, $value = null) {
        self::start();
        if ($value === null) {
            $flash = self::get('_flash_' . $key);
            self::remove('_flash_' . $key);
            return $flash;
        }
        self::set('_flash_' . $key, $value);
    }
    
    public static function isLoggedIn() {
        return self::has('user_id') && self::has('user_role');
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: /permit/public/login.php');
            exit;
        }
    }
    
    public static function requireRole($roles) {
        self::requireLogin();
        $userRole = self::get('user_role');
        if (!in_array($userRole, (array)$roles)) {
            self::flash('error', 'Unauthorized access');
            header('Location: /permit/public/dashboard.php');
            exit;
        }
    }
    
    public static function getUserId() {
        return self::get('user_id');
    }
    
    public static function getUserRole() {
        return self::get('user_role');
    }
    
    public static function getUserName() {
        return self::get('user_name');
    }
    
    public static function isAdmin() {
        return self::get('user_role') === 'admin';
    }
    
    public static function isStaff() {
        return self::get('user_role') === 'staff';
    }
    
    public static function isApplicant() {
        return self::get('user_role') === 'applicant';
    }
}
