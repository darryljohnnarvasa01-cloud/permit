<?php
require_once __DIR__ . '/../app/core/session.php';
require_once __DIR__ . '/../app/helpers/helper.php';
require_once __DIR__ . '/../app/models/Motorela.php';
require_once __DIR__ . '/../app/models/PermitType.php';
require_once __DIR__ . '/../app/models/Log.php';

Session::start();
Session::requireLogin();

$motorelaModel = new Motorela();
$permitTypeModel = new PermitType();
$logModel = new Log();

// Get user's approved permits for renewal
$myPermits = array_filter(
    $motorelaModel->getByUser(Session::getUserId()),
    fn($p) => $p['status'] === 'approved'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPermit = $motorelaModel->find($_POST['old_permit_id']);
    $permitType = $permitTypeModel->first('name', 'Renewal Permit') ?? $permitTypeModel->find($_POST['permit_type_id']);
    
    $expirationDate = date('Y-m-d', strtotime("+{$permitType['validity_months']} months"));
    
    $data = [
        'permit_number' => generatePermitNumber(),
        'user_id' => Session::getUserId(),
        'owner_name' => $oldPermit['owner_name'],
        'owner_address' => $oldPermit['owner_address'],
        'owner_contact' => sanitize($_POST['owner_contact']),
        'driver_name' => sanitize($_POST['driver_name']),
        'driver_license' => sanitize($_POST['driver_license']),
        'driver_contact' => sanitize($_POST['driver_contact']),
        'plate_number' => $oldPermit['plate_number'],
        'chassis_number' => $oldPermit['chassis_number'],
        'engine_number' => $oldPermit['engine_number'],
        'vehicle_make' => $oldPermit['vehicle_make'],
        'vehicle_model' => $oldPermit['vehicle_model'],
        'vehicle_year' => $oldPermit['vehicle_year'],
        'vehicle_color' => $oldPermit['vehicle_color'],
        'vehicle_type_id' => $oldPermit['vehicle_type_id'],
        'renewal_type_id' => $oldPermit['renewal_type_id'],
        'fare_zone_id' => $oldPermit['fare_zone_id'],
        'color_group_id' => $oldPermit['color_group_id'],
        'permit_type_id' => $permitType['id'],
        'application_type' => 'renewal',
        'expiration_date' => $expirationDate,
        'payment_amount' => $permitType['fee'],
        'status' => 'pending',
        'remarks' => 'Renewal of permit: ' . $oldPermit['permit_number']
    ];
    
    $id = $motorelaModel->create($data);
    if ($id) {
        $logModel->add(Session::getUserId(), 'Permit Renewal', "Renewed permit: {$data['permit_number']}");
        success('Renewal application submitted successfully!');
        redirect('/permit/public/my_permits.php');
    }
}

$renewalPermitType = $permitTypeModel->first('name', 'Renewal Permit');

$title = 'Renew Permit';
require_once __DIR__ . '/../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Renew Motorela Permit</h1>
        <p class="mt-2 text-sm text-gray-700">Select an existing permit to renew</p>

        <?php if (empty($myPermits)): ?>
        <div class="mt-8 card">
            <p class="text-center text-gray-500">You don't have any approved permits to renew.</p>
            <div class="text-center mt-4">
                <a href="/permit/public/application_new.php" class="btn btn-primary">Apply for New Permit</a>
            </div>
        </div>
        <?php else: ?>
        <form method="POST" class="mt-8 space-y-6">
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Permit to Renew</h2>
                <div class="space-y-3">
                    <?php foreach ($myPermits as $permit): ?>
                    <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="old_permit_id" value="<?= $permit['id'] ?>" class="mt-1" required onclick="fillRenewalForm(<?= htmlspecialchars(json_encode($permit)) ?>)">
                        <div class="ml-3 flex-1">
                            <div class="flex justify-between">
                                <div>
                                    <div class="font-medium"><?= escape($permit['permit_number']) ?></div>
                                    <div class="text-sm text-gray-600"><?= escape($permit['owner_name']) ?> - <?= escape($permit['plate_number']) ?></div>
                                    <div class="text-xs text-gray-500">Expires: <?= formatDate($permit['expiration_date']) ?></div>
                                </div>
                            </div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Information (if changed)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Owner Contact</label>
                        <input type="text" name="owner_contact" id="owner_contact" class="input" required>
                    </div>
                    <div>
                        <label class="label">Driver Name</label>
                        <input type="text" name="driver_name" id="driver_name" class="input" required>
                    </div>
                    <div>
                        <label class="label">Driver License</label>
                        <input type="text" name="driver_license" id="driver_license" class="input" required>
                    </div>
                    <div>
                        <label class="label">Driver Contact</label>
                        <input type="text" name="driver_contact" id="driver_contact" class="input" required>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Renewal Fee</h2>
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-medium"><?= escape($renewalPermitType['name'] ?? 'Renewal Permit') ?></div>
                        <div class="text-sm text-gray-600"><?= escape($renewalPermitType['description'] ?? '') ?></div>
                    </div>
                    <div class="text-2xl font-bold text-primary-600"><?= formatMoney($renewalPermitType['fee'] ?? 300) ?></div>
                </div>
                <input type="hidden" name="permit_type_id" value="<?= $renewalPermitType['id'] ?? 3 ?>">
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/permit/public/dashboard.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Renewal</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
function fillRenewalForm(permit) {
    document.getElementById('owner_contact').value = permit.owner_contact || '';
    document.getElementById('driver_name').value = permit.driver_name || '';
    document.getElementById('driver_license').value = permit.driver_license || '';
    document.getElementById('driver_contact').value = permit.driver_contact || '';
}
</script>

<?php require_once __DIR__ . '/../resources/views/layouts/footer.php'; ?>
