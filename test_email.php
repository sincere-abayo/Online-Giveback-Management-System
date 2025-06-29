<?php
// Test script for email functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Email Service Test</h2>";

try {
    require_once 'classes/SimpleEmailService.php';
    $emailService = new SimpleEmailService();

    // Test email parameters
    $testEmail = 'abayosincere11@gmail.com'; // Replace with your test email
    $firstName = 'Test';
    $lastName = 'User';
    $rollNumber = '2024001';

    echo "<p>Testing email service...</p>";
    echo "<p>Target email: $testEmail</p>";

    $result = $emailService->sendWelcomeEmail($testEmail, $firstName, $lastName, $rollNumber);

    echo "<h3>Result:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";

    if ($result['success']) {
        echo "<p style='color: green;'>✅ Email sent successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Email failed: " . $result['message'] . "</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> Replace 'test@example.com' with your actual email address to test the functionality.</p>";
?>