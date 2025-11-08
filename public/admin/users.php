<?php
require_once __DIR__ . '/../../app/core/session.php';
require_once __DIR__ . '/../../app/helpers/helper.php';
require_once __DIR__ . '/../../app/models/User.php';
require_once __DIR__ . '/../../app/models/Log.php';

Session::start();
Session::requireRole('admin');

$userModel = new User();
$logModel = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create') {
    $data = [
        'fullname' => sanitize($_POST['fullname']),
        'email' => sanitize($_POST['email']),
        'password' => $_POST['password'],
        'role' => $_POST['role'],
        'status' => 'active'
    ];
    
    if ($userModel->createUser($data)) {
        $logModel->add(Session::getUserId(), 'User Created', "Created user: {$data['email']}");
        success('User created successfully');
    }
    redirect('/permit/public/admin/users.php');
}

if (isset($_GET['toggle'])) {
    if ($userModel->toggleStatus($_GET['toggle'])) {
        success('User status updated');
    }
    redirect('/permit/public/admin/users.php');
}

$users = $userModel->all();

$title = 'User Management';
require_once __DIR__ . '/../../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8 animate-fade-in">
    <!-- Colorful Header Section -->
    <div class="bg-gradient-to-r from-pink-600 via-rose-600 to-red-600 rounded-2xl p-8 shadow-2xl mb-8 animate-scale-in">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">User Management</h1>
                        <p class="mt-1 text-pink-100">Manage system users and staff</p>
                    </div>
                </div>
            </div>
            <button onclick="openModal('createModal')" class="mt-4 sm:mt-0 bg-white text-pink-600 hover:bg-pink-50 px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New User
            </button>
        </div>
    </div>

    <div class="mt-8 card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Created</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium"><?= escape($user['fullname']) ?></td>
                        <td class="px-4 py-3"><?= escape($user['email']) ?></td>
                        <td class="px-4 py-3">
                            <span class="badge <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : ($user['role'] === 'staff' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3"><?= getStatusBadge($user['status']) ?></td>
                        <td class="px-4 py-3"><?= formatDate($user['created_at']) ?></td>
                        <td class="px-4 py-3">
                            <a href="?toggle=<?= $user['id'] ?>" class="text-yellow-600 hover:text-yellow-900">Toggle Status</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md sm:max-w-lg mx-4 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-semibold mb-4">Add New User</h3>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="space-y-4">
                <div>
                    <label class="label">Full Name</label>
                    <input type="text" name="fullname" class="input" required>
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" name="email" class="input" required>
                </div>
                <div>
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input" required minlength="6">
                </div>
                <div>
                    <label class="label">Role</label>
                    <select name="role" class="input" required>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                        <option value="applicant">Applicant</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('createModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>

<?php require_once __DIR__ . '/../../resources/views/layouts/footer.php'; ?>
