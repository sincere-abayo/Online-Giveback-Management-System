<?php
/**
 * SMS Configuration for Dufatanye Charity Foundation
 * Uses Africa's Talking SMS API
 */

// Load environment variables
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Africa's Talking credentials from environment variables
define('AFRICASTALKING_USERNAME', $_ENV['AFRICASTALKING_USERNAME'] ?? 'your_username_here');
define('AFRICASTALKING_API_KEY', $_ENV['AFRICASTALKING_API_KEY'] ?? 'your_api_key_here');

// Include AfricasTalking SDK
use AfricasTalking\SDK\AfricasTalking;

// SMS Templates
$sms_templates = [
    'donation_confirmation' => [
        'description' => 'Sent when a donation is completed',
        'message' => 'Thank you {fullname}! Your donation of {amount} RWF (Ref: {donation_ref}) has been received. Dufatanye Charity Foundation'
    ],
    'donation_receipt' => [
        'description' => 'Detailed receipt message',
        'message' => 'Receipt: {donation_ref} | Amount: {amount} RWF | Date: {date} | Dufatanye Charity Foundation'
    ],
    'volunteer_welcome' => [
        'description' => 'Sent when volunteer account is approved',
        'message' => 'Welcome {fullname}! Your volunteer account has been approved. Login at our website to start making a difference.'
    ],
    'volunteer_assignment' => [
        'description' => 'Sent when volunteer is assigned to an activity',
        'message' => 'Hello {firstname}! You have been assigned to {activity_name} ({program_name}) on {date}. Session: {session}. Dufatanye Charity Foundation'
    ]
];

/**
 * Send SMS using Africa's Talking
 */
function sendSMS($phoneNumber, $message, $template = null, $variables = [])
{
    global $sms_templates;

    // Format phone number (ensure it has country code)
    if (!preg_match('/^\+/', $phoneNumber)) {
        // Add Rwanda country code if not present
        $phoneNumber = '+250' . ltrim($phoneNumber, '0');
    }

    // Replace template variables if template is provided
    if ($template && isset($sms_templates[$template])) {
        $message = $sms_templates[$template]['message'];
        foreach ($variables as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
    }

    // Truncate message if longer than 160 characters
    if (strlen($message) > 160) {
        $message = substr($message, 0, 157) . '...';
    }

    // Initialize the SDK
    $AT = new AfricasTalking(AFRICASTALKING_USERNAME, AFRICASTALKING_API_KEY);

    // Get the SMS service
    $sms = $AT->sms();

    try {
        // Send the message
        $result = $sms->send([
            'to' => $phoneNumber,
            'message' => $message
        ]);

        // Check if the message was sent successfully
        if ($result['status'] == 'success' && !empty($result['data']->SMSMessageData->Recipients)) {
            $recipient = $result['data']->SMSMessageData->Recipients[0];
            if ($recipient->status == 'Success') {
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'cost' => $recipient->cost ?? 'Unknown'
                ];
            }
        }

        error_log("SMS could not be sent. Status: " . json_encode($result));
        return [
            'success' => false,
            'message' => 'SMS failed to send',
            'error' => 'API returned failure status'
        ];
    } catch (Exception $e) {
        error_log("SMS could not be sent. Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'SMS failed to send',
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Test SMS functionality
 */
function testSMS($phoneNumber, $message)
{
    return sendSMS($phoneNumber, $message);
}

/**
 * Setup Instructions:
 * 
 * 1. Sign up for Africa's Talking account at: https://account.africastalking.com/
 * 2. Create a new app in your dashboard
 * 3. Get your API key from the app settings
 * 4. Update the constants above with your credentials
 * 5. Request approval for your sender ID (DUFATANYE)
 * 6. Test the SMS functionality using the testSMS() function
 * 
 * Example usage:
 * $result = testSMS('0781234567', 'Hello from Dufatanye!');
 * if ($result['success']) {
 *     echo "SMS sent successfully! Cost: " . $result['cost'];
 * } else {
 *     echo "SMS failed: " . $result['message'];
 * }
 */

// Database setup for SMS templates
$sms_templates_sql = "
-- Insert SMS templates into database
INSERT INTO sms_templates (template_name, message, is_active) VALUES
('donation_confirmation', 'Thank you {fullname}! Your donation of {amount} RWF (Ref: {donation_ref}) has been received. Dufatanye Charity Foundation', 1),
('donation_receipt', 'Receipt: {donation_ref} | Amount: {amount} RWF | Date: {date} | Dufatanye Charity Foundation', 1),
('volunteer_welcome', 'Welcome {fullname}! Your volunteer account has been approved. Login at our website to start making a difference.', 1),
('volunteer_assignment', 'Hello {firstname}! You have been assigned to {activity_name} ({program_name}) on {date}. Session: {session}. Dufatanye Charity Foundation', 1)
ON DUPLICATE KEY UPDATE message = VALUES(message), is_active = VALUES(is_active);
";

// Payment settings for SMS
$payment_settings_sql = "
-- Insert SMS payment settings
INSERT INTO payment_settings (payment_method, setting_key, setting_value, is_active) VALUES
('sms', 'africas_talking_username', '" . AFRICASTALKING_USERNAME . "', 1),
('sms', 'africas_talking_api_key', '" . AFRICASTALKING_API_KEY . "', 1)
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), is_active = VALUES(is_active);
";

// Uncomment the lines below to run the setup automatically
// include 'config.php';
// $conn->query($sms_templates_sql);
// $conn->query($payment_settings_sql);
?>