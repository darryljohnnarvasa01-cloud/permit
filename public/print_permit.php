<?php
require_once __DIR__ . '/../app/core/session.php';
require_once __DIR__ . '/../app/models/Motorela.php';
require_once __DIR__ . '/../app/helpers/helper.php';

Session::start();
Session::requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    die('Invalid permit ID');
}

$motorelaModel = new Motorela();
$permit = $motorelaModel->getWithDetails($id);

if (!$permit || $permit['status'] !== 'approved') {
    die('Permit not found or not approved');
}

// Check permission
if (!Session::isAdmin() && !Session::isStaff() && $permit['user_id'] != Session::getUserId()) {
    die('Unauthorized access');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorela Permit - <?= escape($permit['permit_number']) ?></title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .permit-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0284c7;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0284c7;
            margin: 0;
            font-size: 28px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 20px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .permit-number {
            text-align: center;
            background: #0284c7;
            color: white;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background: #e0f2fe;
            color: #0369a1;
            padding: 10px;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            padding: 10px 0;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            font-size: 12px;
            text-transform: uppercase;
        }
        .info-value {
            color: #000;
            font-size: 16px;
            margin-top: 5px;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            border: 2px dashed #0284c7;
        }
        .qr-section img {
            width: 150px;
            height: 150px;
        }
        .validity {
            text-align: center;
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .validity strong {
            color: #d97706;
            font-size: 18px;
        }
        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 2px solid #000;
            margin-top: 50px;
            padding-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            color: #666;
            font-size: 12px;
        }
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            background: #0284c7;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #0369a1;
        }
    </style>
</head>
<body>
    <div class="print-button no-print">
        <button onclick="window.print()" class="btn">Print Permit</button>
        <button onclick="window.close()" class="btn" style="background: #6b7280;">Close</button>
    </div>

    <div class="permit-container">
        <div class="header">
            <h1>REPUBLIC OF THE PHILIPPINES</h1>
            <h2>Valencia City, Bukidnon</h2>
            <p>City Transportation Office</p>
            <p style="font-size: 20px; font-weight: bold; color: #0284c7; margin-top: 15px;">MOTORELA OPERATING PERMIT</p>
        </div>

        <div class="permit-number">
            PERMIT NO: <?= escape($permit['permit_number']) ?>
        </div>

        <div class="section">
            <div class="section-title">OWNER INFORMATION</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?= escape($permit['owner_name']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Contact Number</div>
                    <div class="info-value"><?= escape($permit['owner_contact']) ?></div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Address</div>
                    <div class="info-value"><?= escape($permit['owner_address']) ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">DRIVER INFORMATION</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Driver Name</div>
                    <div class="info-value"><?= escape($permit['driver_name']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">License Number</div>
                    <div class="info-value"><?= escape($permit['driver_license']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Contact Number</div>
                    <div class="info-value"><?= escape($permit['driver_contact']) ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">VEHICLE INFORMATION</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Plate Number</div>
                    <div class="info-value" style="font-weight: bold; font-size: 20px; color: #0284c7;"><?= escape($permit['plate_number']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Vehicle Type</div>
                    <div class="info-value"><?= escape($permit['vehicle_type_name'] ?? 'N/A') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Make & Model</div>
                    <div class="info-value"><?= escape($permit['vehicle_make']) ?> <?= escape($permit['vehicle_model']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Year</div>
                    <div class="info-value"><?= escape($permit['vehicle_year']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Color</div>
                    <div class="info-value"><?= escape($permit['vehicle_color']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Chassis Number</div>
                    <div class="info-value"><?= escape($permit['chassis_number']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Engine Number</div>
                    <div class="info-value"><?= escape($permit['engine_number']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fare Zone</div>
                    <div class="info-value"><?= escape($permit['fare_zone_name'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>

        <div class="validity">
            <strong>VALID UNTIL: <?= strtoupper(formatDate($permit['expiration_date'], 'F d, Y')) ?></strong>
        </div>

        <?php if ($permit['qr_code']): ?>
        <div class="qr-section">
            <img src="/permit/public/<?= escape($permit['qr_code']) ?>" alt="QR Code">
            <p style="margin-top: 10px; font-size: 12px; color: #666;">Scan to verify permit authenticity</p>
        </div>
        <?php endif; ?>

        <div class="section" style="margin-top: 30px;">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Date Issued</div>
                    <div class="info-value"><?= formatDate($permit['approved_at'] ?? $permit['created_at']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Permit Type</div>
                    <div class="info-value"><?= escape($permit['permit_type_name']) ?></div>
                </div>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <strong><?= escape($permit['approved_by_name'] ?? 'Authorized Officer') ?></strong><br>
                    <span style="font-size: 12px;">Approving Officer</span>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <strong>City Transportation Officer</strong><br>
                    <span style="font-size: 12px;">Valencia City</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>IMPORTANT NOTICE:</strong></p>
            <p>This permit must be displayed prominently on the vehicle at all times during operation.</p>
            <p>Any alteration or misuse of this permit is punishable by law.</p>
            <p style="margin-top: 15px;">For inquiries, contact: Valencia City Hall Transportation Office</p>
        </div>
    </div>
</body>
</html>
