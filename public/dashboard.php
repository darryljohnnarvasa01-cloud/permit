<?php
require_once __DIR__ . '/../app/core/session.php';
require_once __DIR__ . '/../app/helpers/helper.php';
require_once __DIR__ . '/../app/models/Motorela.php';
require_once __DIR__ . '/../app/models/User.php';

Session::start();
Session::requireLogin();

$motorelaModel = new Motorela();
$userModel = new User();

if (Session::isAdmin() || Session::isStaff()) {
    $stats = $motorelaModel->getStatistics();
    $recentPermits = array_slice($motorelaModel->getAllWithDetails(), 0, 10);
    $pendingCount = $motorelaModel->count("status = 'pending'");
    $totalUsers = $userModel->count();
} else {
    $myPermits = $motorelaModel->getByUser(Session::getUserId());
    $pendingCount = count(array_filter($myPermits, fn($p) => $p['status'] === 'pending'));
    $approvedCount = count(array_filter($myPermits, fn($p) => $p['status'] === 'approved'));
}

$title = 'Dashboard';
require_once __DIR__ . '/../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-sm text-gray-700">
                Welcome back, <?= escape(Session::getUserName()) ?>!
            </p>
        </div>
    </div>

    <?php if (Session::isAdmin() || Session::isStaff()): ?>
    <!-- Admin/Staff Dashboard -->
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Permits Card -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-primary-600 rounded-lg p-2.5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Permits</p>
            <p class="text-3xl font-bold text-gray-900"><?= $stats['total'] ?? 0 ?></p>
        </div>

        <!-- Pending Card -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-yellow-600 rounded-lg p-2.5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Pending Review</p>
            <p class="text-3xl font-bold text-gray-900"><?= $stats['pending'] ?? 0 ?></p>
        </div>

        <!-- Approved Card -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-green-600 rounded-lg p-2.5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Approved</p>
            <p class="text-3xl font-bold text-gray-900"><?= $stats['approved'] ?? 0 ?></p>
        </div>

        <!-- Revenue Card -->
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-blue-600 rounded-lg p-2.5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
            <p class="text-3xl font-bold text-gray-900"><?= formatMoney($stats['total_revenue'] ?? 0) ?></p>
        </div>
    </div>

    <div class="mt-8 card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Permit Applications</h2>
        <div class="overflow-x-auto">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left">Permit Number</th>
                        <th class="px-4 py-3 text-left">Owner</th>
                        <th class="px-4 py-3 text-left">Vehicle Type</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Date Applied</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentPermits)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No permits found</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentPermits as $permit): ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium"><?= escape($permit['permit_number']) ?></td>
                        <td class="px-4 py-3"><?= escape($permit['owner_name']) ?></td>
                        <td class="px-4 py-3"><?= escape($permit['vehicle_type_name'] ?? 'N/A') ?></td>
                        <td class="px-4 py-3"><?= getStatusBadge($permit['status']) ?></td>
                        <td class="px-4 py-3"><?= formatDate($permit['created_at']) ?></td>
                        <td class="px-4 py-3">
                            <a href="/permit/public/permit_view.php?id=<?= $permit['id'] ?>" class="text-primary-600 hover:text-primary-900">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php else: ?>
    <!-- Applicant Dashboard -->
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-3">
        <div class="card">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Applications</p>
                    <p class="text-3xl font-bold text-gray-900"><?= count($myPermits) ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600"><?= $pendingCount ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Approved</p>
                    <p class="text-3xl font-bold text-green-600"><?= $approvedCount ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
        <a href="/permit/public/application_new.php" class="card hover:shadow-lg transition-shadow cursor-pointer">
            <div class="flex items-center">
                <div class="bg-primary-100 rounded-full p-4 mr-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Apply for New Permit</h3>
                    <p class="text-sm text-gray-600">Submit a new motorela permit application</p>
                </div>
            </div>
        </a>

        <a href="/permit/public/application_renew.php" class="card hover:shadow-lg transition-shadow cursor-pointer">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-4 mr-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Renew Existing Permit</h3>
                    <p class="text-sm text-gray-600">Renew your motorela permit</p>
                </div>
            </div>
        </a>
    </div>

    <div class="mt-8 card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">My Recent Applications</h2>
        <div class="overflow-x-auto">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left">Permit Number</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Date Applied</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($myPermits)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No applications yet</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach (array_slice($myPermits, 0, 5) as $permit): ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium"><?= escape($permit['permit_number']) ?></td>
                        <td class="px-4 py-3"><?= escape(ucfirst($permit['application_type'])) ?></td>
                        <td class="px-4 py-3"><?= getStatusBadge($permit['status']) ?></td>
                        <td class="px-4 py-3"><?= formatDate($permit['created_at']) ?></td>
                        <td class="px-4 py-3">
                            <a href="/permit/public/permit_view.php?id=<?= $permit['id'] ?>" class="text-primary-600 hover:text-primary-900">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../resources/views/layouts/footer.php'; ?>
