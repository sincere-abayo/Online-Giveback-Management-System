-- SMS Templates for Volunteer Notifications
-- This file adds the necessary SMS templates for volunteer registration and status updates

-- Template for new volunteer registration
INSERT INTO `sms_templates` (`template_name`, `message`, `is_active`, `created_at`) VALUES
('volunteer_registration', 'Hello {firstname}! Thank you for registering as a volunteer with Dufatanye Charity Foundation. Your application is under review. We will notify you of your status soon. Welcome to our community!', 1, NOW())
ON DUPLICATE KEY UPDATE 
    message = VALUES(message),
    is_active = VALUES(is_active),
    updated_at = NOW();

-- Template for volunteer status updates
INSERT INTO `sms_templates` (`template_name`, `message`, `is_active`, `created_at`) VALUES
('volunteer_status_update', 'Hello {firstname}! Your volunteer application status has been updated to: {status}. Volunteer ID: {roll}. Date: {date}. Dufatanye Charity Foundation', 1, NOW())
ON DUPLICATE KEY UPDATE 
    message = VALUES(message),
    is_active = VALUES(is_active),
    updated_at = NOW();

-- Template for volunteer approval (specific)
INSERT INTO `sms_templates` (`template_name`, `message`, `is_active`, `created_at`) VALUES
('volunteer_approved', 'Congratulations {firstname}! Your volunteer application has been approved. Your Volunteer ID is {roll}. You can now login to your dashboard and start making a difference. Dufatanye Charity Foundation', 1, NOW())
ON DUPLICATE KEY UPDATE 
    message = VALUES(message),
    is_active = VALUES(is_active),
    updated_at = NOW();

-- Template for volunteer denial (specific)
INSERT INTO `sms_templates` (`template_name`, `message`, `is_active`, `created_at`) VALUES
('volunteer_denied', 'Hello {firstname}, thank you for your interest in volunteering with Dufatanye Charity Foundation. Unfortunately, your application was not approved at this time. You may reapply in the future. We appreciate your understanding.', 1, NOW())
ON DUPLICATE KEY UPDATE 
    message = VALUES(message),
    is_active = VALUES(is_active),
    updated_at = NOW(); 