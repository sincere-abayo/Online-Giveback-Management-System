<?php
/**
 * SMS Test File
 * 
 * Use this file to test your SMS configuration
 * Make sure to update the credentials in .env file first
 */

include 'sms_config.php';

// Test phone number (update this with your actual phone number)
$test_phone = '0786729283'; // Replace with your actual phone number

echo "<h2>SMS Test for Dufatanye Charity Foundation</h2>";

// Check if credentials are set
if (AFRICASTALKING_USERNAME === 'your_username_here' || AFRICASTALKING_API_KEY === 'your_api_key_here') {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "<strong>Error:</strong> Please update your Africa's Talking credentials in .env file first!";
    echo "</div>";
    echo "<p>Steps to configure:</p>";
    echo "<ol>";
    echo "<li>Copy .env.example to .env: <code>cp .env.example .env</code></li>";
    echo "<li>Update AFRICASTALKING_USERNAME and AFRICASTALKING_API_KEY in .env</li>";
    echo "<li>Request approval for sender ID 'DUFATANYE'</li>";
    echo "<li>Run <a href='update_config.php'>update_config.php</a> to sync settings</li>";
    echo "</ol>";
    exit;
}

// Test SMS sending
echo "<div style='background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Testing SMS to: " . $test_phone . "</h3>";
echo "<p><strong>Note:</strong> Please update the phone number above with your actual number to test SMS.</p>";

$result = testSMS($test_phone, 'Test SMS from Dufatanye Charity Foundation. If you receive this, SMS is working correctly!');

if ($result['success']) {
    echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
    echo "<strong>Success!</strong> " . $result['message'];
    if (isset($result['cost'])) {
        echo "<br>Cost: " . $result['cost'];
    }
    echo "</div>";
} else {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "<strong>Error:</strong> " . $result['message'];
    if (isset($result['error'])) {
        echo "<br>Details: " . $result['error'];
    }
    echo "<br><br><strong>Troubleshooting:</strong>";
    echo "<ul>";
    echo "<li>Update the phone number above with your actual number</li>";
    echo "<li>Check your Africa's Talking account balance</li>";
    echo "<li>Verify your API credentials are correct</li>";
    echo "<li>Make sure the phone number is in correct format (e.g., 0781234567)</li>";
    echo "</ul>";
    echo "</div>";
}

echo "</div>";

// Show configuration info
echo "<div style='background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Current Configuration:</h3>";
echo "<p><strong>Username:</strong> " . AFRICASTALKING_USERNAME . "</p>";
echo "<p><strong>API Key:</strong> " . substr(AFRICASTALKING_API_KEY, 0, 10) . "...</p>";
echo "</div>";

// Show available templates
echo "<div style='background: #f3e5f5; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Available SMS Templates:</h3>";
foreach ($sms_templates as $name => $template) {
    echo "<div style='margin: 10px 0; padding: 10px; background: white; border-radius: 3px;'>";
    echo "<strong>" . ucfirst(str_replace('_', ' ', $name)) . ":</strong><br>";
    echo "<em>" . $template['description'] . "</em><br>";
    echo "<code>" . htmlspecialchars($template['message']) . "</code>";
    echo "</div>";
}
echo "</div>";

echo "<p><a href='donation.php'>← Back to Donation Page</a></p>";
?>