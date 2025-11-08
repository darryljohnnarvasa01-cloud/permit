<?php
require_once __DIR__ . '/../app/core/session.php';
require_once __DIR__ . '/../app/helpers/helper.php';
require_once __DIR__ . '/../app/models/Motorela.php';

Session::start();
Session::requireLogin();

$motorelaModel = new Motorela();
$myPermits = $motorelaModel->getByUser(Session::getUserId());

$title = 'My Permits';
require_once __DIR__ . '/../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">My Permit Applications</h1>
            <p class="mt-2 text-sm text-gray-700">View and track your motorela permit applications</p>
        </div>
        <div class="space-x-2">
            <a href="/permit/public/application_new.php" class="btn btn-primary">New Application</a>
            <a href="/permit/public/application_renew.php" class="btn btn-success">Renew</a>
        </div>
    </div>

    <div class="mt-8 card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left">Permit Number</th>
                        <th class="px-4 py-3 text-left">Owner</th>
                        <th class="px-4 py-3 text-left">Plate #</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Payment</th>
                        <th class="px-4 py-3 text-left">Date Applied</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($myPermits)): ?>
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            No applications yet. <a href="/permit/public/application_new.php" class="text-primary-600">Apply now</a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($myPermits as $permit): ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium"><?= escape($permit['permit_number']) ?></td>
                        <td class="px-4 py-3"><?= escape($permit['owner_name']) ?></td>
                        <td class="px-4 py-3"><?= escape($permit['plate_number']) ?></td>
                        <td class="px-4 py-3"><?= escape(ucfirst($permit['application_type'])) ?></td>
                        <td class="px-4 py-3"><?= getStatusBadge($permit['status']) ?></td>
                        <td class="px-4 py-3"><?= getStatusBadge($permit['payment_status']) ?></td>
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
</div>

<?php require_once __DIR__ . '/../resources/views/layouts/footer.php'; ?>
