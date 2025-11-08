-- Add payment_method column to motorela_permits table if not exists
USE motorela_permit_db;

ALTER TABLE motorela_permits 
ADD COLUMN IF NOT EXISTS payment_method ENUM('cash', 'gcash', 'paymaya', 'bank_transfer', 'over_counter') DEFAULT 'cash' AFTER payment_amount;

ALTER TABLE motorela_permits 
MODIFY COLUMN payment_receipt VARCHAR(255) NULL COMMENT 'Path to payment proof/receipt';
