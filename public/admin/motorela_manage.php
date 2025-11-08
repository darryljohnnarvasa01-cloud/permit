<?php
require_once __DIR__ . '/../../app/core/session.php';
require_once __DIR__ . '/../../app/helpers/helper.php';
require_once __DIR__ . '/../../app/models/Motorela.php';
require_once __DIR__ . '/../../app/models/Log.php';
require_once __DIR__ . '/../../app/helpers/qr_generator.php';

Session::start();
Session::requireRole(['admin', 'staff']);

$motorelaModel = new Motorela();
$logModel = new Log();

// Handle Approve
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $permit = $motorelaModel->getWithDetails($id);
    
    if ($permit && $motorelaModel->approve($id, Session::getUserId())) {
        // Generate QR Code
        $qrPath = QRGenerator::generatePermitQR(
            $permit['permit_number'],
            $permit['owner_name'],
            $permit['expiration_date']
        );
        
        if ($qrPath) {
            $motorelaModel->updateQRCode($id, $qrPath);
        }
        
        $logModel->add(Session::getUserId(), 'Permit Approved', "Approved permit: {$permit['permit_number']}");
        success('Permit approved successfully');
    }
    redirect('/permit/public/admin/motorela_manage.php');
}

// Handle Reject
if (isset($_POST['reject'])) {
    $id = $_POST['permit_id'];
    $reason = sanitize($_POST['reason']);
    
    if ($motorelaModel->reject($id, $reason)) {
        $logModel->add(Session::getUserId(), 'Permit Rejected', "Rejected permit ID: $id");
        success('Permit rejected');
    }
    redirect('/permit/public/admin/motorela_manage.php');
}

// Handle Payment Verification
if (isset($_GET['verify_payment'])) {
    $id = $_GET['verify_payment'];
    $permit = $motorelaModel->find($id);
    
    if ($permit && $motorelaModel->updatePaymentStatus($id, 'paid')) {
        $logModel->add(Session::getUserId(), 'Payment Verified', "Verified payment for permit ID: $id");
        success('Payment verified successfully');
    }
    redirect('/permit/public/admin/motorela_manage.php');
}

$filter = $_GET['filter'] ?? 'all';
if ($filter === 'pending') {
    $permits = $motorelaModel->getPending();
} elseif ($filter === 'approved') {
    $permits = $motorelaModel->getApproved();
} else {
    $permits = $motorelaModel->getAllWithDetails();
}

$title = 'Manage Permits';
require_once __DIR__ . '/../../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8 animate-fade-in">
    <!-- Colorful Header Section -->
    <div class="bg-gradient-to-r from-orange-600 via-amber-600 to-yellow-600 rounded-2xl p-8 shadow-2xl mb-8 animate-scale-in">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">Manage Permit Applications</h1>
                        <p class="mt-1 text-orange-100">Review and process motorela permit applications</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 sm:mt-0">
                <select onchange="window.location.href='?filter='+this.value" class="bg-white text-orange-600 px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 border-2 border-white/50 cursor-pointer">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Permits</option>
                    <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                </select>
            </div>
        </div>
    </div>

    <div class="mt-8 card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left">Permit #</th>
                        <th class="px-4 py-3 text-left">Owner</th>
                        <th class="px-4 py-3 text-left">Plate #</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Payment</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($permits)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No permits found</td></tr>
                    <?php else: ?>
                    <?php foreach ($permits as $permit): ?>
                    <tr class="table-row">
                        <td class="px-4 py-3 font-medium"><?= escape($permit['permit_number']) ?></td>
                        <td class="px-4 py-3">
                            <div class="font-medium"><?= escape($permit['owner_name']) ?></div>
                            <div class="text-sm text-gray-500"><?= escape($permit['applicant_name']) ?></div>
                        </td>
                        <td class="px-4 py-3"><?= escape($permit['plate_number']) ?></td>
                        <td class="px-4 py-3"><?= escape($permit['permit_type_name']) ?></td>
                        <td class="px-4 py-3"><?= getStatusBadge($permit['status']) ?></td>
                        <td class="px-4 py-3"><?= getStatusBadge($permit['payment_status']) ?></td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="/permit/public/permit_view.php?id=<?= $permit['id'] ?>" class="text-primary-600 hover:text-primary-900">View</a>
                            <?php if ($permit['status'] === 'pending'): ?>
                            <a href="?approve=<?= $permit['id'] ?>" class="text-green-600 hover:text-green-900" onclick="return confirm('Approve this permit?')">Approve</a>
                            <button onclick="showRejectModal(<?= $permit['id'] ?>)" class="text-red-600 hover:text-red-900">Reject</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md sm:max-w-lg mx-4 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-semibold mb-4">Reject Permit</h3>
        <form method="POST">
            <input type="hidden" name="permit_id" id="reject_permit_id">
            <div class="space-y-4">
                <div>
                    <label class="label">Reason for Rejection</label>
                    <textarea name="reason" class="input" rows="4" required placeholder="Enter the reason..."></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('rejectModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" name="reject" class="btn btn-danger">Reject Permit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(id) {
    document.getElementById('reject_permit_id').value = id;
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/../../resources/views/layouts/footer.php'; ?>
