<?php
require_once('config.php');
require_once('classes/Master.php');

// Test volunteer SMS functionality
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background: #f9f9f9; border-radius: 10px;'>";
echo "<h2 style='color: #333;'>Volunteer SMS Notification Test</h2>";

// Test data
$test_volunteer_data = [
    'firstname' => 'John',
    'lastname' => 'Doe',
    'email' => 'test@example.com',
    'contact' => '0781234567', // Test phone number
    'motivation' => 'I want to help the community',
    'roll' => 'VOL001'
];

$master = new Master();

// Test new volunteer registration SMS
echo "<div style='background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Testing New Volunteer Registration SMS:</h3>";

$result = $master->send_volunteer_email();
$response = json_decode($result, true);

if ($response) {
    echo "<p><strong>Email Sent:</strong> " . ($response['email_sent'] ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>SMS Sent:</strong> " . ($response['sms_sent'] ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($response['message']) . "</p>";
    echo "<p><strong>Success:</strong> " . ($response['success'] ? 'Yes' : 'No') . "</p>";
} else {
    echo "<p style='color: red;'>Failed to decode response</p>";
}

echo "</div>";

// Test volunteer status update SMS
echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Testing Volunteer Status Update SMS:</h3>";

// Simulate status update
$_POST['volunteer_data'] = json_encode($test_volunteer_data);
$_POST['is_new'] = 'false';
$_POST['status'] = '1'; // Approved

$result = $master->send_volunteer_email();
$response = json_decode($result, true);

if ($response) {
    echo "<p><strong>Email Sent:</strong> " . ($response['email_sent'] ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>SMS Sent:</strong> " . ($response['sms_sent'] ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($response['message']) . "</p>";
    echo "<p><strong>Success:</strong> " . ($response['success'] ? 'Yes' : 'No') . "</p>";
} else {
    echo "<p style='color: red;'>Failed to decode response</p>";
}

echo "</div>";

// Show configuration info
echo "<div style='background: #f3e5f5; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Current Configuration:</h3>";
echo "<p><strong>Africa's Talking Username:</strong> " . ($_ENV['AFRICASTALKING_USERNAME'] ?? 'Not configured') . "</p>";
echo "<p><strong>Africa's Talking API Key:</strong> " . (isset($_ENV['AFRICASTALKING_API_KEY']) ? substr($_ENV['AFRICASTALKING_API_KEY'], 0, 10) . '...' : 'Not configured') . "</p>";
echo "<p><strong>SMTP Host:</strong> " . ($_ENV['SMTP_HOST'] ?? 'Not configured') . "</p>";
echo "<p><strong>SMTP Username:</strong> " . ($_ENV['SMTP_USERNAME'] ?? 'Not configured') . "</p>";
echo "</div>";

// Show available SMS templates
echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Available SMS Templates:</h3>";

try {
    $conn = new mysqli($_ENV['DB_HOST'] ?? 'localhost', $_ENV['DB_USERNAME'] ?? 'root', $_ENV['DB_PASSWORD'] ?? '', $_ENV['DB_NAME'] ?? 'gms');

    if ($conn->connect_error) {
        echo "<p style='color: red;'>Database connection failed: " . $conn->connect_error . "</p>";
    } else {
        $result = $conn->query("SELECT template_name, message, is_active FROM sms_templates WHERE template_name LIKE 'volunteer_%' ORDER BY template_name");

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div style='margin: 10px 0; padding: 10px; background: white; border-radius: 3px;'>";
                echo "<strong>" . ucfirst(str_replace('_', ' ', $row['template_name'])) . ":</strong> ";
                echo "<span style='color: " . ($row['is_active'] ? 'green' : 'red') . ";'>" . ($row['is_active'] ? 'Active' : 'Inactive') . "</span><br>";
                echo "<code>" . htmlspecialchars($row['message']) . "</code>";
                echo "</div>";
            }
        } else {
            echo "<p style='color: orange;'>No volunteer SMS templates found in database.</p>";
            echo "<p>Please run the volunteer_sms_templates.sql file to add the required templates.</p>";
        }

        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error loading SMS templates: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Instructions
echo "<div style='background: #ffeaa7; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Setup Instructions:</h3>";
echo "<ol>";
echo "<li>Ensure Africa's Talking credentials are set in environment variables</li>";
echo "<li>Run the volunteer_sms_templates.sql file to add SMS templates</li>";
echo "<li>Test with a real phone number (replace the test number above)</li>";
echo "<li>Check error logs for any issues</li>";
echo "</ol>";
echo "</div>";

echo "</div>";
?>