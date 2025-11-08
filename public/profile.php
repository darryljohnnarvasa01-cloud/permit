<?php
require_once __DIR__ . '/../app/core/session.php';
require_once __DIR__ . '/../app/helpers/helper.php';
require_once __DIR__ . '/../app/models/User.php';

Session::start();
Session::requireLogin();

$userModel = new User();
$user = $userModel->find(Session::getUserId());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $data = [
            'fullname' => sanitize($_POST['fullname']),
            'email' => sanitize($_POST['email'])
        ];
        
        if ($userModel->update(Session::getUserId(), $data)) {
            Session::set('user_name', $data['fullname']);
            Session::set('user_email', $data['email']);
            success('Profile updated successfully');
        }
    } elseif (isset($_POST['change_password'])) {
        if (password_verify($_POST['current_password'], $user['password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                if ($userModel->updatePassword(Session::getUserId(), $_POST['new_password'])) {
                    success('Password changed successfully');
                }
            } else {
                error('New passwords do not match');
            }
        } else {
            error('Current password is incorrect');
        }
    }
    redirect('/permit/public/profile.php');
}

$title = 'Profile';
require_once __DIR__ . '/../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold text-gray-900">My Profile</h1>

        <!-- Profile Information -->
        <div class="mt-8 card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
            <form method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="label">Full Name</label>
                        <input type="text" name="fullname" class="input" value="<?= escape($user['fullname']) ?>" required>
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input type="email" name="email" class="input" value="<?= escape($user['email']) ?>" required>
                    </div>
                    <div>
                        <label class="label">Role</label>
                        <input type="text" class="input" value="<?= ucfirst($user['role']) ?>" disabled>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="mt-8 card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h2>
            <form method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="label">Current Password</label>
                        <input type="password" name="current_password" class="input" required>
                    </div>
                    <div>
                        <label class="label">New Password</label>
                        <input type="password" name="new_password" class="input" required minlength="6">
                    </div>
                    <div>
                        <label class="label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="input" required minlength="6">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../resources/views/layouts/footer.php'; ?>
