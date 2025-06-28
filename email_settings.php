<?php
/**
 * Email Configuration for GMS
 * 
 * IMPORTANT: Update these settings with your actual email credentials
 * For Gmail, you need to:
 * 1. Enable 2-Factor Authentication
 * 2. Generate an App Password
 * 3. Use the App Password instead of your regular password
 */

// Email Server Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls'); // or 'ssl'

// Email Credentials - REPLACE WITH YOUR ACTUAL CREDENTIALS
define('SMTP_USERNAME', 'infofonepo@gmail.com'); // Your Gmail address
define('SMTP_PASSWORD', 'zaoxwuezfjpglwjb');    // Your Gmail app password

// Sender Information
define('FROM_EMAIL', 'dufatanyecharity@gmail.com');
define('FROM_NAME', 'Dufatanye Charity Foundation');

// Email Settings
define('ENABLE_EMAIL', true); // Set to false to disable email sending during development

// Optional: Email Templates Directory
define('EMAIL_TEMPLATES_DIR', __DIR__ . '/email_templates/');

// Optional: Admin Email for notifications
define('ADMIN_EMAIL', 'admin@dufatanye.org');

// Debug Settings
define('EMAIL_DEBUG', false); // Set to true for debugging
?>