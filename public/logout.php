<?php
require_once __DIR__ . '/../app/core/session.php';
require_once __DIR__ . '/../app/models/Log.php';
require_once __DIR__ . '/../app/helpers/helper.php';

Session::start();

if (Session::isLoggedIn()) {
    // Try to log the logout, but don't fail if it doesn't work
    try {
        $logModel = new Log();
        $logModel->add(Session::getUserId(), 'User Logout', 'User logged out');
    } catch (Exception $e) {
        // Silently fail - logging shouldn't prevent logout
        // You could optionally log this to a file
        error_log('Failed to log logout: ' . $e->getMessage());
    }
    
    Session::destroy();
}

redirect('/permit/public/login.php');
