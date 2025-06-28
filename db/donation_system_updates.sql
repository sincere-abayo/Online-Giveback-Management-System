-- Enhanced Donation System Database Updates
-- Date: 2025-01-27
-- Description: Upgrading donation system with new features

-- Drop old donation table and create new enhanced version
DROP TABLE IF EXISTS `donation`;

-- Create new donations table with enhanced features
CREATE TABLE `donations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `donation_ref` varchar(50) NOT NULL COMMENT 'Unique donation reference',
  `volunteer_id` int(11) DEFAULT NULL COMMENT 'Linked volunteer ID if logged in',
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('stripe','paypal','mtn','airtel') NOT NULL,
  `message` text DEFAULT NULL COMMENT 'Donor message',
  `status` enum('pending','processing','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_id` varchar(255) DEFAULT NULL COMMENT 'Payment gateway transaction ID',
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sms_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `donation_ref` (`donation_ref`),
  KEY `volunteer_id` (`volunteer_id`),
  KEY `status` (`status`),
  KEY `payment_method` (`payment_method`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create donation history table for logged-in users
CREATE TABLE `donation_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `volunteer_id` int(11) NOT NULL,
  `donation_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `volunteer_id` (`volunteer_id`),
  KEY `donation_id` (`donation_id`),
  CONSTRAINT `fk_donation_history_volunteer` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteer_list` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_donation_history_donation` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create payment settings table
CREATE TABLE `payment_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method` varchar(50) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_setting` (`payment_method`, `setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default payment settings
INSERT INTO `payment_settings` (`payment_method`, `setting_key`, `setting_value`) VALUES
('stripe', 'publishable_key', 'pk_test_your_stripe_publishable_key'),
('stripe', 'secret_key', 'sk_test_your_stripe_secret_key'),
('stripe', 'webhook_secret', 'whsec_your_webhook_secret'),
('paypal', 'client_id', 'your_paypal_client_id'),
('paypal', 'client_secret', 'your_paypal_client_secret'),
('paypal', 'mode', 'sandbox'),
('sms', 'africas_talking_api_key', 'your_africas_talking_api_key'),
('sms', 'africas_talking_username', 'your_africas_talking_username'),
('sms', 'africas_talking_sender_id', 'DUFATANYE');

-- Create email templates table
CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `html_content` text NOT NULL,
  `text_content` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_name` (`template_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default email templates
INSERT INTO `email_templates` (`template_name`, `subject`, `html_content`, `text_content`) VALUES
('donation_confirmation', 'Thank you for your donation - Dufatanye Charity Foundation', 
'<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Donation Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px;">
            <h1>Thank You for Your Donation!</h1>
            <p>Your generosity makes a real difference in our community.</p>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <h3>Donation Details:</h3>
            <p><strong>Reference:</strong> {donation_ref}</p>
            <p><strong>Amount:</strong> {amount} RWF</p>
            <p><strong>Date:</strong> {date}</p>
            <p><strong>Payment Method:</strong> {payment_method}</p>
        </div>
        
        <p>Your donation will be used to support our various programs including education, health, and community development initiatives.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{dashboard_url}" style="background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; display: inline-block;">View Your Dashboard</a>
        </div>
        
        <p>Thank you for your continued support!</p>
        <p>Best regards,<br>Dufatanye Charity Foundation Team</p>
    </div>
</body>
</html>',
'Thank you for your donation - Dufatanye Charity Foundation

Your generosity makes a real difference in our community.

Donation Details:
Reference: {donation_ref}
Amount: {amount} RWF
Date: {date}
Payment Method: {payment_method}

Your donation will be used to support our various programs including education, health, and community development initiatives.

View your dashboard at: {dashboard_url}

Thank you for your continued support!

Best regards,
Dufatanye Charity Foundation Team'),

('donation_receipt', 'Donation Receipt - Dufatanye Charity Foundation',
'<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Donation Receipt</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="text-align: center; background: #28a745; color: white; padding: 30px; border-radius: 10px;">
            <h1>Donation Receipt</h1>
            <p>Dufatanye Charity Foundation</p>
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <h3>Receipt Details:</h3>
            <p><strong>Receipt Number:</strong> {donation_ref}</p>
            <p><strong>Date:</strong> {date}</p>
            <p><strong>Donor Name:</strong> {fullname}</p>
            <p><strong>Email:</strong> {email}</p>
            <p><strong>Phone:</strong> {phone}</p>
            <p><strong>Amount:</strong> {amount} RWF</p>
            <p><strong>Payment Method:</strong> {payment_method}</p>
            <p><strong>Transaction ID:</strong> {payment_id}</p>
        </div>
        
        <p>This receipt serves as proof of your charitable donation to Dufatanye Charity Foundation.</p>
        
        <p>Thank you for your generosity!</p>
        <p>Best regards,<br>Dufatanye Charity Foundation Team</p>
    </div>
</body>
</html>',
'Donation Receipt - Dufatanye Charity Foundation

Receipt Details:
Receipt Number: {donation_ref}
Date: {date}
Donor Name: {fullname}
Email: {email}
Phone: {phone}
Amount: {amount} RWF
Payment Method: {payment_method}
Transaction ID: {payment_id}

This receipt serves as proof of your charitable donation to Dufatanye Charity Foundation.

Thank you for your generosity!

Best regards,
Dufatanye Charity Foundation Team');

-- Create SMS templates table
CREATE TABLE `sms_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_name` (`template_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default SMS templates
INSERT INTO `sms_templates` (`template_name`, `message`) VALUES
('donation_confirmation', 'Thank you for your donation of {amount} RWF to Dufatanye Charity Foundation. Reference: {donation_ref}. Your generosity makes a difference!'),
('donation_receipt', 'Receipt for {amount} RWF donation. Ref: {donation_ref}. Date: {date}. Thank you for supporting Dufatanye Charity Foundation!');

-- Add indexes for better performance
ALTER TABLE `donations` ADD INDEX `idx_email` (`email`);
ALTER TABLE `donations` ADD INDEX `idx_phone` (`phone`);
ALTER TABLE `donations` ADD INDEX `idx_payment_id` (`payment_id`);

-- Create view for donation statistics
CREATE OR REPLACE VIEW `donation_stats` AS
SELECT 
    COUNT(*) as total_donations,
    SUM(amount) as total_amount,
    AVG(amount) as avg_amount,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_donations,
    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_amount,
    COUNT(CASE WHEN volunteer_id IS NOT NULL THEN 1 END) as logged_in_donations,
    COUNT(CASE WHEN volunteer_id IS NULL THEN 1 END) as guest_donations
FROM `donations`;

-- Create view for volunteer donation history
CREATE OR REPLACE VIEW `volunteer_donations` AS
SELECT 
    v.id as volunteer_id,
    v.roll,
    v.firstname,
    v.lastname,
    v.email,
    d.donation_ref,
    d.amount,
    d.payment_method,
    d.status,
    d.created_at
FROM `volunteer_list` v
LEFT JOIN `donations` d ON v.id = d.volunteer_id
WHERE d.id IS NOT NULL
ORDER BY d.created_at DESC;

-- Add comments for documentation
ALTER TABLE `donations` COMMENT = 'Enhanced donation records with payment integration';
ALTER TABLE `donation_history` COMMENT = 'Donation history for logged-in volunteers';
ALTER TABLE `payment_settings` COMMENT = 'Payment gateway configuration settings';
ALTER TABLE `email_templates` COMMENT = 'Email templates for donation notifications';
ALTER TABLE `sms_templates` COMMENT = 'SMS templates for donation notifications';

-- ========================================
-- SUMMARY OF ENHANCED DONATION SYSTEM
-- ========================================
--
-- 1. NEW TABLES CREATED:
--    - donations: Enhanced donation records with payment integration
--    - donation_history: Track donation history for volunteers
--    - payment_settings: Configuration for payment gateways
--    - email_templates: Email templates for notifications
--    - sms_templates: SMS templates for notifications
--
-- 2. FEATURES ADDED:
--    - Unique donation references
--    - Multiple payment methods (Stripe, PayPal, MTN, Airtel)
--    - Email and SMS notifications
--    - Donation history for logged-in users
--    - Payment status tracking
--    - Template-based notifications
--    - Performance indexes
--    - Statistical views
--
-- 3. PAYMENT INTEGRATION READY:
--    - Stripe payment processing
--    - PayPal payment processing
--    - Mobile money integration
--    - Webhook handling
--    - Transaction tracking
--
-- 4. NOTIFICATION SYSTEM:
--    - Email confirmations
--    - SMS notifications via Africa's Talking
--    - Template-based messages
--    - Multi-language support ready
--
-- 5. USER EXPERIENCE:
--    - Guest donations allowed
--    - Login/register motivation
--    - Donation history tracking
--    - Receipt generation
--    - Impact visualization
--
-- ======================================== 