<?php
require_once __DIR__ . '/../app/core/session.php';
require_once __DIR__ . '/../app/helpers/helper.php';
require_once __DIR__ . '/../app/models/Motorela.php';
require_once __DIR__ . '/../app/models/Category.php';
require_once __DIR__ . '/../app/models/PermitType.php';
require_once __DIR__ . '/../app/models/Log.php';

Session::start();
Session::requireLogin();

$categoryModel = new Category();
$permitTypeModel = new PermitType();
$motorelaModel = new Motorela();
$logModel = new Log();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle individual file uploads for each requirement
    $requirements = [];
    $requiredDocs = [
        'valid_id' => 'Valid ID',
        'drivers_license' => 'Driver\'s License',
        'vehicle_or' => 'Vehicle OR (Official Receipt)',
        'vehicle_cr' => 'Vehicle CR (Certificate of Registration)',
        'barangay_clearance' => 'Barangay Clearance',
        'police_clearance' => 'Police Clearance',
        'cedula' => 'Cedula',
        'proof_ownership' => 'Proof of Ownership',
        'insurance' => 'Insurance Certificate (if available)',
        'other_docs' => 'Other Supporting Documents'
    ];
    
    foreach ($requiredDocs as $docKey => $docName) {
        if (!empty($_FILES[$docKey]['name'])) {
            if ($_FILES[$docKey]['error'] === 0) {
                $uploaded = uploadFile($_FILES[$docKey], 'requirements');
                if ($uploaded) {
                    $requirements[$docName] = $uploaded;
                }
            }
        }
    }
    
    // Handle payment proof upload
    $paymentReceipt = null;
    if (!empty($_FILES['payment_proof']['name'])) {
        if ($_FILES['payment_proof']['error'] === 0) {
            $paymentReceipt = uploadFile($_FILES['payment_proof'], 'payment_proofs');
        }
    }
    
    $permitType = $permitTypeModel->find($_POST['permit_type_id']);
    $expirationDate = date('Y-m-d', strtotime("+{$permitType['validity_months']} months"));
    
    $data = [
        'permit_number' => generatePermitNumber(),
        'user_id' => Session::getUserId(),
        'owner_name' => sanitize($_POST['owner_name']),
        'owner_address' => sanitize($_POST['owner_address']),
        'owner_contact' => sanitize($_POST['owner_contact']),
        'driver_name' => sanitize($_POST['driver_name']),
        'driver_license' => sanitize($_POST['driver_license']),
        'driver_contact' => sanitize($_POST['driver_contact']),
        'plate_number' => sanitize($_POST['plate_number']),
        'chassis_number' => sanitize($_POST['chassis_number']),
        'engine_number' => sanitize($_POST['engine_number']),
        'vehicle_make' => sanitize($_POST['vehicle_make']),
        'vehicle_model' => sanitize($_POST['vehicle_model']),
        'vehicle_year' => intval($_POST['vehicle_year']),
        'vehicle_color' => sanitize($_POST['vehicle_color']),
        'vehicle_type_id' => !empty($_POST['vehicle_type_id']) ? intval($_POST['vehicle_type_id']) : null,
        'renewal_type_id' => !empty($_POST['renewal_type_id']) ? intval($_POST['renewal_type_id']) : null,
        'fare_zone_id' => !empty($_POST['fare_zone_id']) ? intval($_POST['fare_zone_id']) : null,
        'color_group_id' => !empty($_POST['color_group_id']) ? intval($_POST['color_group_id']) : null,
        'permit_type_id' => intval($_POST['permit_type_id']),
        'application_type' => 'new',
        'expiration_date' => $expirationDate,
        'requirements_json' => json_encode($requirements),
        'payment_amount' => $permitType['fee'],
        'payment_method' => !empty($_POST['payment_method']) ? sanitize($_POST['payment_method']) : 'cash',
        'payment_receipt' => $paymentReceipt,
        'payment_status' => $paymentReceipt ? 'paid' : 'unpaid',
        'status' => 'pending'
    ];
    
    $id = $motorelaModel->create($data);
    if ($id) {
        $logModel->add(Session::getUserId(), 'New Permit Application', "Applied for new permit: {$data['permit_number']}");
        success('Application submitted successfully! Please wait for approval.');
        redirect('/permit/public/my_permits.php');
    } else {
        error('Failed to submit application');
    }
}

$vehicleTypes = $categoryModel->getVehicleTypes();
$renewalTypes = $categoryModel->getRenewalTypes();
$fareZones = $categoryModel->getFareZones();
$colorGroups = $categoryModel->getColorGroups();
$permitTypes = $permitTypeModel->getActive();

$title = 'Apply for New Permit';
require_once __DIR__ . '/../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-semibold text-gray-900">Apply for New Motorela Permit</h1>
        <p class="mt-2 text-sm text-gray-700">Complete the form below to apply for a new motorela permit</p>

        <form method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">
            <!-- Owner Information -->
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Owner Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="label">Full Name *</label>
                        <input type="text" name="owner_name" class="input" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="label">Address *</label>
                        <textarea name="owner_address" class="input" rows="2" required></textarea>
                    </div>
                    <div>
                        <label class="label">Contact Number *</label>
                        <input type="text" name="owner_contact" class="input" required>
                    </div>
                </div>
            </div>

            <!-- Driver Information -->
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Driver Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Driver Name *</label>
                        <input type="text" name="driver_name" class="input" required>
                    </div>
                    <div>
                        <label class="label">License Number *</label>
                        <input type="text" name="driver_license" class="input" required>
                    </div>
                    <div>
                        <label class="label">Contact Number *</label>
                        <input type="text" name="driver_contact" class="input" required>
                    </div>
                </div>
            </div>

            <!-- Vehicle Information -->
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Vehicle Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Plate Number *</label>
                        <input type="text" name="plate_number" class="input" required>
                    </div>
                    <div>
                        <label class="label">Chassis Number</label>
                        <input type="text" name="chassis_number" class="input">
                    </div>
                    <div>
                        <label class="label">Engine Number</label>
                        <input type="text" name="engine_number" class="input">
                    </div>
                    <div>
                        <label class="label">Make/Brand</label>
                        <input type="text" name="vehicle_make" class="input" placeholder="e.g., Honda">
                    </div>
                    <div>
                        <label class="label">Model</label>
                        <input type="text" name="vehicle_model" class="input" placeholder="e.g., TMX 155">
                    </div>
                    <div>
                        <label class="label">Year</label>
                        <input type="number" name="vehicle_year" class="input" min="1900" max="<?= date('Y') ?>">
                    </div>
                    <div>
                        <label class="label">Color</label>
                        <input type="text" name="vehicle_color" class="input">
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Classification</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Vehicle Type</label>
                        <select name="vehicle_type_id" class="input">
                            <option value="">Select Type</option>
                            <?php foreach ($vehicleTypes as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= escape($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="label">Renewal Type</label>
                        <select name="renewal_type_id" class="input">
                            <option value="">Select Type</option>
                            <?php foreach ($renewalTypes as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= escape($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="label">Fare Zone</label>
                        <select name="fare_zone_id" class="input">
                            <option value="">Select Zone</option>
                            <?php foreach ($fareZones as $zone): ?>
                            <option value="<?= $zone['id'] ?>"><?= escape($zone['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="label">Color Group</label>
                        <select name="color_group_id" class="input">
                            <option value="">Select Group</option>
                            <?php foreach ($colorGroups as $group): ?>
                            <option value="<?= $group['id'] ?>"><?= escape($group['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Permit Type -->
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Permit Type *</h2>
                <div class="space-y-3">
                    <?php foreach ($permitTypes as $type): ?>
                    <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="permit_type_id" value="<?= $type['id'] ?>" class="mt-1" required>
                        <div class="ml-3 flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium"><?= escape($type['name']) ?></div>
                                    <div class="text-sm text-gray-600"><?= escape($type['description']) ?></div>
                                    <?php if ($type['requirements']): ?>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Requirements: <?= escape($type['requirements']) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-lg font-bold text-primary-600"><?= formatMoney($type['fee']) ?></div>
                            </div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Requirements Upload -->
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Upload Requirements</h2>
                <p class="text-sm text-gray-600 mb-4">Upload clear scanned copies or photos of each required document</p>
                
                <div class="space-y-4">
                    <!-- Valid ID -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Valid ID * <span class="text-xs text-red-600 font-normal">(Required)</span></label>
                                <input type="file" name="valid_id" class="input" accept="image/*,.pdf" required>
                                <p class="text-xs text-gray-500 mt-1">UMID, Driver's License, Passport, Voter's ID, SSS, PhilHealth, etc.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Driver's License -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Driver's License * <span class="text-xs text-red-600 font-normal">(Required)</span></label>
                                <input type="file" name="drivers_license" class="input" accept="image/*,.pdf" required>
                                <p class="text-xs text-gray-500 mt-1">Valid driver's license with restriction code 1, 2, or 3</p>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle OR -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Vehicle OR (Official Receipt) * <span class="text-xs text-red-600 font-normal">(Required)</span></label>
                                <input type="file" name="vehicle_or" class="input" accept="image/*,.pdf" required>
                                <p class="text-xs text-gray-500 mt-1">Current year's Official Receipt from LTO</p>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle CR -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Vehicle CR (Certificate of Registration) * <span class="text-xs text-red-600 font-normal">(Required)</span></label>
                                <input type="file" name="vehicle_cr" class="input" accept="image/*,.pdf" required>
                                <p class="text-xs text-gray-500 mt-1">Certificate of Registration from LTO</p>
                            </div>
                        </div>
                    </div>

                    <!-- Barangay Clearance -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Barangay Clearance <span class="text-xs text-blue-600 font-normal">(Recommended)</span></label>
                                <input type="file" name="barangay_clearance" class="input" accept="image/*,.pdf">
                                <p class="text-xs text-gray-500 mt-1">Barangay clearance from your area</p>
                            </div>
                        </div>
                    </div>

                    <!-- Police Clearance -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Police Clearance <span class="text-xs text-blue-600 font-normal">(Recommended)</span></label>
                                <input type="file" name="police_clearance" class="input" accept="image/*,.pdf">
                                <p class="text-xs text-gray-500 mt-1">National Police Clearance</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cedula -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Cedula (Community Tax Certificate) <span class="text-xs text-gray-600 font-normal">(Optional)</span></label>
                                <input type="file" name="cedula" class="input" accept="image/*,.pdf">
                                <p class="text-xs text-gray-500 mt-1">Current year's Community Tax Certificate</p>
                            </div>
                        </div>
                    </div>

                    <!-- Proof of Ownership -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Proof of Ownership <span class="text-xs text-gray-600 font-normal">(Optional)</span></label>
                                <input type="file" name="proof_ownership" class="input" accept="image/*,.pdf">
                                <p class="text-xs text-gray-500 mt-1">Deed of sale or other proof of vehicle ownership</p>
                            </div>
                        </div>
                    </div>

                    <!-- Insurance -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Insurance Certificate <span class="text-xs text-gray-600 font-normal">(Optional)</span></label>
                                <input type="file" name="insurance" class="input" accept="image/*,.pdf">
                                <p class="text-xs text-gray-500 mt-1">Vehicle insurance certificate if available</p>
                            </div>
                        </div>
                    </div>

                    <!-- Other Documents -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Other Supporting Documents <span class="text-xs text-gray-600 font-normal">(Optional)</span></label>
                                <input type="file" name="other_docs" class="input" accept="image/*,.pdf">
                                <p class="text-xs text-gray-500 mt-1">Any other relevant documents</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Upload Guidelines:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Accepted formats: JPG, PNG, GIF, PDF</li>
                                <li>Ensure documents are clear and readable</li>
                                <li>File size should not exceed 5MB per document</li>
                                <li>Make sure all text and details are visible</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">💳 Payment Information</h2>
                <p class="text-sm text-gray-600 mb-4">Upload proof of payment to expedite your application approval</p>
                
                <div class="space-y-4">
                    <!-- Payment Method -->
                    <div>
                        <label class="label">Payment Method *</label>
                        <select name="payment_method" class="input" required>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="paymaya">PayMaya</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="over_counter">Over the Counter</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select how you paid or will pay the permit fee</p>
                    </div>

                    <!-- Payment Proof Upload -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-green-300 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label class="label">Upload Payment Proof <span class="text-xs text-green-600 font-normal">(Highly Recommended)</span></label>
                                <input type="file" name="payment_proof" class="input" accept="image/*,.pdf">
                                <p class="text-xs text-gray-500 mt-1">Upload receipt, screenshot, or proof of payment (JPG, PNG, PDF)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-green-800">
                            <p class="font-semibold mb-1">Why Upload Payment Proof Now?</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Faster application processing</li>
                                <li>Immediate verification by admin</li>
                                <li>Avoid delays in permit approval</li>
                                <li>Complete application in one step</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/permit/public/dashboard.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../resources/views/layouts/footer.php'; ?>
