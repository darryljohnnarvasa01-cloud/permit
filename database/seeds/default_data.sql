-- Default Data for Valencia City Motorela Permit System
USE motorela_permit_db;

-- Default Admin and Staff Users
-- Password: Admin123! (for admin)
-- Password: Staff123! (for staff)
INSERT INTO users (fullname, email, password, role, status) VALUES
('System Administrator', 'admin@valenciacity.gov.ph', '$2y$10$UVe5wxxjAUOHc82MUlKws.xmKbiZ95avVn93fgVa/lh9TWetUX2km', 'admin', 'active'),
('Staff User', 'staff@valenciacity.gov.ph', '$2y$10$EweK.YxdYuy2K03ELQW9wevvoeeyPkU1LseVALLBvuHnK5/4.kH1a', 'staff', 'active');

-- Default Categories - Vehicle Types
INSERT INTO categories (name, description, type, status) VALUES
('Motorela', 'Standard motorela vehicle', 'vehicle_type', 'active'),
('Tricycle', 'Standard tricycle vehicle', 'vehicle_type', 'active'),
('E-Trike', 'Electric tricycle', 'vehicle_type', 'active');

-- Default Categories - Renewal Types
INSERT INTO categories (name, description, type, status) VALUES
('New Application', 'First time application', 'renewal_type', 'active'),
('Renewal', 'Permit renewal', 'renewal_type', 'active'),
('Transfer of Ownership', 'Transfer to new owner', 'renewal_type', 'active');

-- Default Categories - Fare Zones
INSERT INTO categories (name, description, type, status) VALUES
('City Proper', 'Valencia City proper routes', 'fare_zone', 'active'),
('Rural Area', 'Rural and barangay routes', 'fare_zone', 'active'),
('Special Zone', 'Special economic zones', 'fare_zone', 'active');

-- Default Categories - Color Groups
INSERT INTO categories (name, description, type, status) VALUES
('Yellow', 'Yellow coded vehicles', 'color_group', 'active'),
('Orange', 'Orange coded vehicles', 'color_group', 'active'),
('Red', 'Red coded vehicles', 'color_group', 'active'),
('Blue', 'Blue coded vehicles', 'color_group', 'active');

-- Default Permit Types
INSERT INTO permit_types (name, description, requirements, fee, validity_months, status) VALUES
('Standard Motorela Permit', 'Standard permit for motorela operation', 
'Valid ID,Driver\'s License,OR/CR (Official Receipt/Certificate of Registration),Barangay Clearance,Police Clearance,Vehicle Photos', 
500.00, 12, 'active'),

('Franchise Motorela Permit', 'Franchise permit for commercial motorela operation',
'Valid ID,Driver\'s License,OR/CR,Barangay Clearance,Police Clearance,Vehicle Photos,Business Permit,Franchise Agreement',
1000.00, 12, 'active'),

('Renewal Permit', 'Permit renewal for existing operators',
'Valid ID,Driver\'s License,Previous Permit,OR/CR,Vehicle Photos',
300.00, 12, 'active');

-- Default Settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('system_name', 'Valencia City Motorela Permit System', 'System name'),
('contact_email', 'info@valenciacity.gov.ph', 'Contact email'),
('contact_phone', '(088) 000-0000', 'Contact phone'),
('office_address', 'Valencia City Hall, Bukidnon', 'Office address'),
('permit_prefix', 'VC-MPR', 'Permit number prefix'),
('qr_enabled', '1', 'Enable QR code generation'),
('email_enabled', '0', 'Enable email notifications');
