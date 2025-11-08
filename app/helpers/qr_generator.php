<?php
/**
 * QR Code Generator Helper
 */

// Load Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QRGenerator {
    
    public static function generate($data, $filename = null) {
        try {
            if (!$filename) {
                $filename = uniqid() . '.png';
            }
            
            $qrCode = QrCode::create($data)
                ->setSize(300)
                ->setMargin(10);
            
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            $directory = __DIR__ . '/../../public/qr/qr_images/';
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            $filepath = $directory . $filename;
            $result->saveToFile($filepath);
            
            return 'qr/qr_images/' . $filename;
        } catch (Exception $e) {
            error_log("QR Code generation failed: " . $e->getMessage());
            return null;
        }
    }
    
    public static function generatePermitQR($permitNumber, $ownerName, $expirationDate) {
        $data = json_encode([
            'permit_number' => $permitNumber,
            'owner' => $ownerName,
            'expires' => $expirationDate,
            'issued_by' => 'Valencia City Government'
        ]);
        
        $filename = 'permit_' . $permitNumber . '.png';
        return self::generate($data, $filename);
    }
}
