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