-- Fix Orphaned Logs and User Issues
-- Run this in phpMyAdmin if you have issues with logs or sessions

USE motorela_permit_db;

-- Step 1: Check for logs with non-existent users
SELECT l.id, l.user_id, l.activity, l.created_at
FROM logs l
LEFT JOIN users u ON l.user_id = u.id
WHERE l.user_id IS NOT NULL AND u.id IS NULL;

-- Step 2: Fix orphaned logs by setting user_id to NULL
UPDATE logs l
LEFT JOIN users u ON l.user_id = u.id
SET l.user_id = NULL
WHERE l.user_id IS NOT NULL AND u.id IS NULL;

-- Step 3: Check which user IDs exist
SELECT id, fullname, email, role, status 
FROM users 
ORDER BY id;

-- Step 4: Optional - Clean up old logs (older than 6 months)
-- DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);

-- Step 5: Verify the fix
SELECT COUNT(*) as total_logs,
       SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as anonymous_logs,
       SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as user_logs
FROM logs;
