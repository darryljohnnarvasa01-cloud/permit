-- Fix admin account: update password and set status to active
USE motorela_permit_db;

UPDATE users 
SET 
    password = '$2y$10$UVe5wxxjAUOHc82MUlKws.xmKbiZ95avVn93fgVa/lh9TWetUX2km',
    status = 'active'
WHERE email = 'admin@valenciacity.gov.ph';

UPDATE users 
SET 
    password = '$2y$10$EweK.YxdYuy2K03ELQW9wevvoeeyPkU1LseVALLBvuHnK5/4.kH1a',
    status = 'active'
WHERE email = 'staff@valenciacity.gov.ph';

-- Verify the update
SELECT id, fullname, email, role, status FROM users WHERE role IN ('admin', 'staff');
