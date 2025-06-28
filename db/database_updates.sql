-- Database Updates for GMS System
-- Date: 2025-06-27
-- Description: Adding password field to volunteer_list table and other improvements

-- Add password field to volunteer_list table
ALTER TABLE `volunteer_list` 
ADD COLUMN `password` VARCHAR(255) NOT NULL DEFAULT '' AFTER `email`;

-- Add index for better performance on email field
ALTER TABLE `volunteer_list` 
ADD INDEX `idx_email` (`email`);

-- Add index for better performance on roll field
ALTER TABLE `volunteer_list` 
ADD INDEX `idx_roll` (`roll`);

-- Update existing records to have proper roll numbers (if not already done)
UPDATE `volunteer_list` 
SET `roll` = CONCAT('2025', LPAD(id, 3, '0')) 
WHERE `roll` = '' OR `roll` IS NULL;

-- Add a unique constraint on email to prevent duplicate registrations
ALTER TABLE `volunteer_list` 
ADD UNIQUE INDEX `unique_email` (`email`);

-- Add a unique constraint on roll to ensure unique roll numbers
ALTER TABLE `volunteer_list` 
ADD UNIQUE INDEX `unique_roll` (`roll`);

-- Add status field for better volunteer management (if not exists)
-- Note: This field already exists in the current structure

-- Add delete_flag field for soft deletes (if not exists)
-- Note: This field already exists in the current structure

-- Add date_created and date_updated fields (if not exists)
-- Note: These fields already exist in the current structure

-- Create a view for active volunteers
CREATE OR REPLACE VIEW `active_volunteers` AS
SELECT 
    id,
    roll,
    firstname,
    middlename,
    lastname,
    contact,
    email,
    motivation,
    comment,
    status,
    date_created,
    date_updated
FROM `volunteer_list` 
WHERE `delete_flag` = 0 AND `status` = 1;

-- Create a view for pending volunteers
CREATE OR REPLACE VIEW `pending_volunteers` AS
SELECT 
    id,
    roll,
    firstname,
    middlename,
    lastname,
    contact,
    email,
    motivation,
    comment,
    status,
    date_created,
    date_updated
FROM `volunteer_list` 
WHERE `delete_flag` = 0 AND `status` = 0;

-- Add comments to table for documentation
ALTER TABLE `volunteer_list` 
COMMENT = 'Volunteer registration and management table';

-- Add comments to columns for documentation
ALTER TABLE `volunteer_list` 
MODIFY COLUMN `roll` VARCHAR(100) NOT NULL COMMENT 'Unique volunteer roll number (YYYY + 3-digit sequence)',
MODIFY COLUMN `firstname` TEXT NOT NULL COMMENT 'Volunteer first name',
MODIFY COLUMN `middlename` TEXT NULL COMMENT 'Volunteer middle name (optional)',
MODIFY COLUMN `lastname` TEXT NOT NULL COMMENT 'Volunteer last name',
MODIFY COLUMN `contact` TEXT NOT NULL COMMENT 'Volunteer phone number',
MODIFY COLUMN `email` VARCHAR(100) NOT NULL COMMENT 'Volunteer email address (unique)',
MODIFY COLUMN `password` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Hashed password for volunteer login',
MODIFY COLUMN `motivation` TEXT NOT NULL COMMENT 'Volunteer motivation statement',
MODIFY COLUMN `comment` TEXT NOT NULL COMMENT 'Admin comments about volunteer',
MODIFY COLUMN `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Approved, 2=Rejected',
MODIFY COLUMN `delete_flag` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=Active, 1=Deleted (soft delete)',
MODIFY COLUMN `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() COMMENT 'Registration date',
MODIFY COLUMN `date_updated` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP() COMMENT 'Last update date';

-- ========================================
-- SUMMARY OF CHANGES MADE
-- ========================================
-- 
-- 1. DATABASE CHANGES:
--    - Added 'password' field to volunteer_list table (VARCHAR(255))
--    - Added unique constraints on email and roll fields
--    - Added indexes for better performance
--    - Created views for active and pending volunteers
--    - Added comprehensive column comments for documentation
--    - Updated existing records with proper roll numbers
--
-- 2. NEW FILES CREATED:
--    - volunteer_login.php: Volunteer login system
--    - volunteer_dashboard.php: Volunteer dashboard after login
--    - database_updates.sql: This file documenting all changes
--
-- 3. FILES MODIFIED:
--    - registration.php: Added password fields, validation, and improved UI
--    - registersucceed.php: Completely redesigned success page with modern UI
--    - inc/topBarNav.php: Added volunteer login button to navigation
--    - volunteer_login.php: Modified to allow pending volunteers to access dashboard
--    - volunteer_dashboard.php: Enhanced with status banners and better UX for pending volunteers
--
-- 4. FEATURES ADDED:
--    - Password-based volunteer authentication
--    - Email uniqueness validation
--    - Password strength checking
--    - Modern, responsive UI design
--    - Volunteer dashboard with profile and activity tracking
--    - Session management for volunteers
--    - Improved form validation and user feedback
--    - Pending volunteer access to dashboard with clear status display
--    - Status banners and alerts for different account states
--
-- 5. SECURITY IMPROVEMENTS:
--    - Password hashing using PHP's password_hash()
--    - Prepared statements to prevent SQL injection
--    - Input validation and sanitization
--    - Session-based authentication
--
-- 6. UI/UX IMPROVEMENTS:
--    - Modern gradient backgrounds
--    - Responsive design
--    - Interactive elements with hover effects
--    - Progress bars and animations
--    - Font Awesome icons
--    - Better form layout and styling
--    - Status-aware dashboard with conditional content
--    - Clear visual indicators for account status
--
-- 7. LOGIN SYSTEM ENHANCEMENTS:
--    - Volunteers can now login regardless of approval status
--    - Pending volunteers see clear status indicators
--    - Dashboard adapts content based on account status
--    - Helpful messaging for pending volunteers
--
-- 8. BUG FIXES:
--    - Fixed volunteer dashboard 500 error (removed non-existent vh.delete_flag column)
--    - Fixed session handling to prevent session_start() warnings
--    - Improved error handling and session management
--
-- ========================================
-- USAGE INSTRUCTIONS
-- ========================================
--
-- 1. Volunteers can register at: registration.php
-- 2. Volunteers can login at: volunteer_login.php (regardless of approval status)
-- 3. After login, volunteers access dashboard at: volunteer_dashboard.php
-- 4. Pending volunteers will see status banners and helpful information
-- 5. Admin can manage volunteers through the admin panel
-- 6. All database changes are recorded in this SQL file
--
-- ======================================== 