<?php
require_once __DIR__ . '/../../app/core/session.php';
require_once __DIR__ . '/../../app/helpers/helper.php';
require_once __DIR__ . '/../../app/models/Log.php';

Session::start();
Session::requireRole('admin');

$logModel = new Log();
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$logs = $logModel->getRecent($limit);

$title = 'Activity Logs';
require_once __DIR__ . '/../../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8 animate-fade-in">
    <!-- Colorful Header Section -->
    <div class="bg-gradient-to-r from-slate-700 via-gray-700 to-zinc-700 rounded-2xl p-8 shadow-2xl mb-8 animate-scale-in">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">Activity Logs</h1>
                        <p class="mt-1 text-gray-300">System activity and audit trail</p>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2 mt-4 sm:mt-0">
                <select onchange="window.location.href='?limit='+this.value" class="bg-white text-gray-700 px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 border-2 border-white/50 cursor-pointer">
                    <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>Last 50</option>
                    <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>Last 100</option>
                    <option value="500" <?= $limit == 500 ? 'selected' : '' ?>>Last 500</option>
                </select>
            </div>
        </div>
    </div>

    <div class="mt-8 card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left">Date & Time</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Activity</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-left">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 text-sm"><?= date('M d, Y h:i A', strtotime($log['created_at'])) ?></td>
                        <td class="px-4 py-3">
                            <div class="font-medium"><?= escape($log['fullname'] ?? 'System') ?></div>
                            <div class="text-xs text-gray-500"><?= escape($log['email'] ?? '') ?></div>
                        </td>
                        <td class="px-4 py-3 font-medium"><?= escape($log['activity']) ?></td>
                        <td class="px-4 py-3 text-sm"><?= escape($log['description'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-sm"><?= escape($log['ip_address'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../resources/views/layouts/footer.php'; ?>
