<?php
require_once __DIR__ . '/../app/core/session.php';
require_once __DIR__ . '/../app/helpers/helper.php';
require_once __DIR__ . '/../app/models/Motorela.php';

Session::start();
Session::requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('/permit/public/dashboard.php');
}

$motorelaModel = new Motorela();
$permit = $motorelaModel->getWithDetails($id);

if (!$permit) {
    error('Permit not found');
    redirect('/permit/public/dashboard.php');
}

// Check permission
if (!Session::isAdmin() && !Session::isStaff() && $permit['user_id'] != Session::getUserId()) {
    error('Unauthorized access');
    redirect('/permit/public/dashboard.php');
}

$requirements = json_decode($permit['requirements_json'] ?? '[]', true);

// Handle payment proof upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_payment_proof'])) {
    if (!empty($_FILES['payment_proof']['name'])) {
        $file = $_FILES['payment_proof'];
        $uploadPath = uploadFile($file, 'payment_proofs');
        
        if ($uploadPath) {
            $motorelaModel->update($id, [
                'payment_receipt' => $uploadPath,
                'payment_method' => sanitize($_POST['payment_method'])
            ]);
            
            require_once __DIR__ . '/../app/models/Log.php';
            $logModel = new Log();
            $logModel->add(Session::getUserId(), 'Payment Proof Uploaded', "Uploaded payment proof for permit: {$permit['permit_number']}");
            
            success('Payment proof uploaded successfully!');
            redirect('/permit/public/permit_view.php?id=' . $id);
        } else {
            error('Failed to upload payment proof');
        }
    }
}

// Refresh permit data after upload
$permit = $motorelaModel->getWithDetails($id);
$requirements = json_decode($permit['requirements_json'] ?? '[]', true);

$title = 'View Permit';
require_once __DIR__ . '/../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Permit Details</h1>
                <p class="mt-2 text-sm text-gray-700">Permit #<?= escape($permit['permit_number']) ?></p>
            </div>
            <div class="space-x-2">
                <?php if ($permit['status'] === 'approved'): ?>
                <a href="/permit/public/print_permit.php?id=<?= $permit['id'] ?>" target="_blank" class="btn btn-primary">Print Permit</a>
                <?php endif; ?>
                <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status Card -->
                <div class="card">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Application Status</h2>
                            <div class="mt-2 flex items-center space-x-3">
                                <?= getStatusBadge($permit['status']) ?>
                                <?= getStatusBadge($permit['payment_status']) ?>
                            </div>
                            <?php if ($permit['status'] === 'rejected' && $permit['rejected_reason']): ?>
                            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded">
                                <p class="text-sm font-medium text-red-800">Rejection Reason:</p>
                                <p class="text-sm text-red-700"><?= escape($permit['rejected_reason']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($permit['qr_code']): ?>
                        <img src="/permit/public/<?= escape($permit['qr_code']) ?>" alt="QR Code" class="w-24 h-24">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Owner Information -->
                <div class="card">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Owner Information</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['owner_name']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['owner_contact']) ?></dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['owner_address']) ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Driver Information -->
                <div class="card">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Driver Information</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Driver Name</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['driver_name']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">License Number</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['driver_license']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['driver_contact']) ?></dd>
                        </div>
                    </dl>
                </div>

                <!-- Vehicle Information -->
                <div class="card">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Vehicle Information</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Plate Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold"><?= escape($permit['plate_number']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Chassis Number</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['chassis_number']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Engine Number</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['engine_number']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Make & Model</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['vehicle_make']) ?> <?= escape($permit['vehicle_model']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Year</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['vehicle_year']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Color</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['vehicle_color']) ?></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Permit Details -->
                <div class="card">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Permit Details</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Permit Type</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['permit_type_name']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fee</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900"><?= formatMoney($permit['payment_amount']) ?></dd>
                        </div>
                        <?php if ($permit['expiration_date']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Expiration Date</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= formatDate($permit['expiration_date']) ?></dd>
                        </div>
                        <?php endif; ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Application Date</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= formatDate($permit['created_at']) ?></dd>
                        </div>
                        <?php if ($permit['approved_at']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Approved Date</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= formatDate($permit['approved_at']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Approved By</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= escape($permit['approved_by_name']) ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>

                <!-- Categories -->
                <div class="card">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Classification</h2>
                    <dl class="space-y-3">
                        <?php if ($permit['vehicle_type_name']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Vehicle Type</dt>
                            <dd class="mt-1"><span class="badge bg-blue-100 text-blue-800"><?= escape($permit['vehicle_type_name']) ?></span></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($permit['fare_zone_name']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fare Zone</dt>
                            <dd class="mt-1"><span class="badge bg-green-100 text-green-800"><?= escape($permit['fare_zone_name']) ?></span></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($permit['color_group_name']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Color Group</dt>
                            <dd class="mt-1"><span class="badge bg-purple-100 text-purple-800"><?= escape($permit['color_group_name']) ?></span></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>

                <?php if (!empty($requirements)): ?>
                <!-- Requirements -->
                <div class="card">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Uploaded Requirements</h2>
                    <div class="space-y-2">
                        <?php 
                        // Handle both old array format and new associative array format
                        foreach ($requirements as $docName => $req): 
                            // If it's old format (numeric keys), use generic document name
                            if (is_numeric($docName)) {
                                $displayName = "Document " . ($docName + 1);
                            } else {
                                $displayName = $docName;
                            }
                            
                            $fileName = basename($req);
                            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $isPdf = $fileExt === 'pdf';
                            $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']);
                        ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <?php if ($isPdf): ?>
                                    <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                                    </svg>
                                    <?php elseif ($isImage): ?>
                                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                    <?php else: ?>
                                    <svg class="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                    </svg>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= escape($displayName) ?></p>
                                    <p class="text-xs text-gray-500"><?= strtoupper($fileExt) ?> File</p>
                                </div>
                            </div>
                            <a href="/permit/public/uploads/<?= escape($req) ?>" target="_blank" 
                               class="btn btn-sm btn-primary">
                                View
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Payment Information -->
                <div class="card">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Amount</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900"><?= formatMoney($permit['payment_amount']) ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1"><?= getStatusBadge($permit['payment_status']) ?></dd>
                        </div>
                        <?php if ($permit['payment_method']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?= ucwords(str_replace('_', ' ', escape($permit['payment_method']))) ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if ($permit['payment_receipt']): ?>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-2">Payment Proof</dt>
                            <dd class="mt-1">
                                <?php 
                                $proofExt = strtolower(pathinfo($permit['payment_receipt'], PATHINFO_EXTENSION));
                                $isProofImage = in_array($proofExt, ['jpg', 'jpeg', 'png', 'gif']);
                                ?>
                                <?php if ($isProofImage): ?>
                                <a href="/permit/public/uploads/<?= escape($permit['payment_receipt']) ?>" target="_blank">
                                    <img src="/permit/public/uploads/<?= escape($permit['payment_receipt']) ?>" 
                                         alt="Payment Proof" 
                                         class="w-full h-32 object-cover rounded-lg border hover:opacity-75 transition-opacity">
                                </a>
                                <?php else: ?>
                                <a href="/permit/public/uploads/<?= escape($permit['payment_receipt']) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-secondary w-full">
                                    View Payment Proof
                                </a>
                                <?php endif; ?>
                                <?php if ((Session::isAdmin() || Session::isStaff()) && $permit['payment_status'] !== 'paid'): ?>
                                <a href="/permit/public/admin/motorela_manage.php?verify_payment=<?= $permit['id'] ?>" 
                                   class="btn btn-sm btn-success w-full mt-2"
                                   onclick="return confirm('Verify this payment as paid?')">
                                    Verify Payment
                                </a>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <?php elseif (!Session::isAdmin() && !Session::isStaff() && $permit['status'] === 'approved'): ?>
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-sm text-yellow-800 mb-3">Please upload your payment proof to complete the process.</p>
                            <button onclick="openModal('paymentProofModal')" class="btn btn-sm btn-primary w-full">
                                Upload Payment Proof
                            </button>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Proof Upload Modal -->
<div id="paymentProofModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Upload Payment Proof</h3>
            <button onclick="closeModal('paymentProofModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="space-y-4">
                <div>
                    <label class="label">Payment Method *</label>
                    <select name="payment_method" class="input" required>
                        <option value="">Select Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                        <option value="paymaya">PayMaya</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="over_counter">Over the Counter</option>
                    </select>
                </div>
                <div>
                    <label class="label">Upload Payment Proof *</label>
                    <input type="file" name="payment_proof" class="input" accept="image/*,.pdf" required>
                    <p class="text-xs text-gray-500 mt-1">Upload a clear photo or PDF of your payment receipt</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded p-3">
                    <p class="text-sm text-blue-800">
                        <strong>Amount to Pay:</strong> <?= formatMoney($permit['payment_amount']) ?>
                    </p>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeModal('paymentProofModal')" class="btn btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" name="upload_payment_proof" class="btn btn-primary">
                        Upload Proof
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/../resources/views/layouts/footer.php'; ?>
