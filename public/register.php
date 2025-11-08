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

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = validate($_POST, [
        'fullname' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'confirm_password' => 'required'
    ]);
    
    if ($validator->validate($_POST)) {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match';
        } else {
            $userModel = new User();
            $userId = $userModel->createUser([
                'fullname' => sanitize($_POST['fullname']),
                'email' => sanitize($_POST['email']),
                'password' => $_POST['password'],
                'role' => 'applicant',
                'status' => 'active'
            ]);
            
            if ($userId) {
                $logModel = new Log();
                $logModel->add($userId, 'User Registration', 'New user registered');
                
                Session::flash('success', 'Registration successful! Please login.');
                redirect('/permit/public/login.php');
            } else {
                $errors['general'] = 'Registration failed. Please try again.';
            }
        }
    } else {
        $errors = $validator->getErrors();
    }
}

$title = 'Register - Valencia City Motorela Permit System';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="/permit/public/css/tailwind.css" rel="stylesheet">
</head>
<body class="h-full">
    <div class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                    Create Account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Register for a motorela permit
                </p>
            </div>
            
            <div class="card">
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-error mb-4"><?= escape($errors['general']) ?></div>
                <?php endif; ?>
                
                <form class="space-y-6" method="POST">
                    <div>
                        <label for="fullname" class="label">Full Name</label>
                        <input id="fullname" name="fullname" type="text" required 
                               class="input" value="<?= escape(old('fullname')) ?>"
                               placeholder="Enter your full name">
                        <?php if (!empty($errors['fullname'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $errors['fullname'][0] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="email" class="label">Email address</label>
                        <input id="email" name="email" type="email" required 
                               class="input" value="<?= escape(old('email')) ?>"
                               placeholder="Enter your email">
                        <?php if (!empty($errors['email'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $errors['email'][0] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="password" class="label">Password</label>
                        <input id="password" name="password" type="password" required 
                               class="input" placeholder="Minimum 6 characters">
                        <?php if (!empty($errors['password'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $errors['password'][0] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="confirm_password" class="label">Confirm Password</label>
                        <input id="confirm_password" name="confirm_password" type="password" required 
                               class="input" placeholder="Re-enter your password">
                        <?php if (!empty($errors['confirm_password'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $errors['confirm_password'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <button type="submit" class="w-full btn btn-primary">
                            Register
                        </button>
                    </div>
                    
                    <div class="text-center text-sm">
                        <span class="text-gray-600">Already have an account?</span>
                        <a href="/permit/public/login.php" class="font-medium text-primary-600 hover:text-primary-500">
                            Sign in
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
