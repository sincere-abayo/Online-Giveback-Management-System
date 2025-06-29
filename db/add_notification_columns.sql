-- Add notification tracking columns to volunteer_history table
ALTER TABLE `volunteer_history` 
ADD COLUMN `email_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `end_status`,
ADD COLUMN `sms_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `email_sent`; 