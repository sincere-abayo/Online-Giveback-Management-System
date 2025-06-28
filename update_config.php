<?php
/**
 * Configuration Update Script
 * 
 * This script updates the database payment settings with environment variables
 * Run this script once after setting up your .env file
 */

require_once 'config.php';

echo "<h2>Updating Payment Settings from Environment Variables</h2>";

// Payment settings to update
$settings = [
    // Stripe settings
    ['stripe', 'publishable_key', 'STRIPE_PUBLISHABLE_KEY'],
    ['stripe', 'secret_key', 'STRIPE_SECRET_KEY'],
    ['stripe', 'webhook_secret', 'STRIPE_WEBHOOK_SECRET'],

    // PayPal settings
    ['paypal', 'client_id', 'PAYPAL_CLIENT_ID'],
    ['paypal', 'client_secret', 'PAYPAL_CLIENT_SECRET'],
    ['paypal', 'mode', 'PAYPAL_MODE'],

    // SMS settings
    ['sms', 'africas_talking_username', 'AFRICASTALKING_USERNAME'],
    ['sms', 'africas_talking_api_key', 'AFRICASTALKING_API_KEY'],
    ['sms', 'africas_talking_sender_id', 'AFRICASTALKING_SENDER_ID'],

    // MTN settings
    ['mtn', 'api_key', 'MTN_API_KEY'],
    ['mtn', 'api_secret', 'MTN_API_SECRET'],

    // Airtel settings
    ['airtel', 'api_key', 'AIRTEL_API_KEY'],
    ['airtel', 'api_secret', 'AIRTEL_API_SECRET'],
];

$updated = 0;
$errors = 0;

foreach ($settings as $setting) {
    $payment_method = $setting[0];
    $setting_key = $setting[1];
    $env_key = $setting[2];

    $value = $_ENV[$env_key] ?? null;

    if ($value && $value !== 'your_' . strtolower($setting_key)) {
        // Update or insert the setting
        $sql = "INSERT INTO payment_settings (payment_method, setting_key, setting_value, is_active) 
                VALUES (?, ?, ?, 1) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), is_active = VALUES(is_active)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $payment_method, $setting_key, $value);

        if ($stmt->execute()) {
            echo "<div style='color: green; margin: 5px 0;'>✓ Updated {$payment_method}.{$setting_key}</div>";
            $updated++;
        } else {
            echo "<div style='color: red; margin: 5px 0;'>✗ Error updating {$payment_method}.{$setting_key}: " . $stmt->error . "</div>";
            $errors++;
        }
    } else {
        echo "<div style='color: orange; margin: 5px 0;'>⚠ Skipped {$payment_method}.{$setting_key} (not set in .env)</div>";
    }
}

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "<p><strong>Updated:</strong> {$updated} settings</p>";
echo "<p><strong>Errors:</strong> {$errors} settings</p>";

if ($errors === 0) {
    echo "<div style='color: green; padding: 10px; background: #d4edda; border-radius: 5px;'>";
    echo "<strong>Success!</strong> All available settings have been updated.";
    echo "</div>";
} else {
    echo "<div style='color: red; padding: 10px; background: #f8d7da; border-radius: 5px;'>";
    echo "<strong>Warning!</strong> Some settings could not be updated. Check the errors above.";
    echo "</div>";
}

echo "<hr>";
echo "<h3>Current Environment Variables:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
foreach ($_ENV as $key => $value) {
    if (
        strpos($key, 'STRIPE_') === 0 || strpos($key, 'PAYPAL_') === 0 ||
        strpos($key, 'AFRICASTALKING_') === 0 || strpos($key, 'MTN_') === 0 ||
        strpos($key, 'AIRTEL_') === 0 || strpos($key, 'SMTP_') === 0
    ) {
        $display_value = $value;
        if (strpos($key, 'SECRET') !== false || strpos($key, 'KEY') !== false || strpos($key, 'PASSWORD') !== false) {
            $display_value = substr($value, 0, 10) . '...';
        }
        echo "<strong>{$key}:</strong> {$display_value}<br>";
    }
}
echo "</div>";

echo "<p><a href='donation.php'>← Back to Donation Page</a></p>";
?>