-- Password Upgrade Migration for GMS System
-- Date: 2025-01-27
-- Description: Upgrading user passwords from md5 to password_hash format

-- This migration script will help upgrade existing user passwords
-- from the old md5 format to the new password_hash format

-- Note: This script should be run manually for each user that needs password upgrade
-- The Login.php class will automatically upgrade passwords when users log in

-- Example of how to upgrade a specific user's password:
-- UPDATE users SET password = '$2y$10$...' WHERE id = 1;

-- To check current password format:
-- SELECT id, username, LEFT(password, 7) as password_start FROM users;

-- To identify md5 passwords (they start with specific characters):
-- SELECT id, username FROM users WHERE password REGEXP '^[a-f0-9]{32}$';

-- To identify password_hash passwords (they start with $2y$):
-- SELECT id, username FROM users WHERE password LIKE '$2y$%';

-- Migration completed - the Login.php class will handle automatic upgrades
-- when users log in with their existing passwords 