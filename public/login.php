<?php
require_once __DIR__ . '/../app/core/session.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Log.php';
require_once __DIR__ . '/../app/helpers/helper.php';
require_once __DIR__ . '/../app/helpers/validation.php';

Session::start();

// Redirect if already logged in
if (Session::isLoggedIn()) {
    redirect('/permit/public/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $validator = validate($_POST, [
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    if ($validator->validate($_POST)) {
        $userModel = new User();
        $user = $userModel->authenticate($email, $password);
        
        if ($user) {
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['fullname']);
            Session::set('user_email', $user['email']);
            Session::set('user_role', $user['role']);
            
            // Log the login
            $logModel = new Log();
            $logModel->add($user['id'], 'User Login', 'User logged in successfully');
            
            redirect('/permit/public/dashboard.php');
        } else {
            $error = 'Invalid email or password';
        }
    } else {
        $error = $validator->getFirstError();
    }
}

$title = 'Login - Valencia City Motorela Permit System';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="/permit/public/css/tailwind.css" rel="stylesheet">
</head>
<body class="h-full">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="w-full max-w-md space-y-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <div class="bg-primary-600 p-3 rounded-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    Valencia City
                </h2>
                <p class="text-lg font-medium text-gray-600 mb-1">
                    Motorela Permit System
                </p>
                <p class="text-sm text-gray-500">
                    Sign in to access your account
                </p>
            </div>
            
            <!-- Login Card -->
            <div class="card">
                <?php if ($error): ?>
                    <div class="alert alert-error mb-4">
                        <?= escape($error) ?>
                    </div>
                <?php endif; ?>
                
                <form class="space-y-6" method="POST">
                    <div>
                        <label for="email" class="label">Email Address</label>
                        <input id="email" name="email" type="email" required 
                               class="input" value="<?= escape(old('email')) ?>"
                               placeholder="your.email@example.com">
                    </div>

                    <div>
                        <label for="password" class="label">Password</label>
                        <input id="password" name="password" type="password" required 
                               class="input" placeholder="Enter your password">
                    </div>

                    <div>
                        <button type="submit" class="w-full btn btn-primary">
                            Sign in
                        </button>
                    </div>
                    
                    <div class="text-center text-sm">
                        <span class="text-gray-600">Don't have an account?</span>
                        <a href="/permit/public/register.php" class="font-medium text-primary-600 hover:text-primary-700 ml-1">
                            Register here
                        </a>
                    </div>
                </form>
                
                <!-- Demo Credentials -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <p class="text-xs font-semibold text-blue-900 mb-2">
                            Demo Accounts Available
                        </p>
                        <div class="space-y-1 text-xs text-gray-700">
                            <p><strong>Admin:</strong> admin@valenciacity.gov.ph / Admin123!</p>
                            <p><strong>Staff:</strong> staff@valenciacity.gov.ph / Staff123!</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    © 2025 Valencia City Government. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
