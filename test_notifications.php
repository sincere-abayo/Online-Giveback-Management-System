<?php
/**
 * Test Notifications for Donation
 */

include 'config.php';
require_once 'classes/PaymentProcessor.php';

echo "<h2>Testing Notifications for Donation</h2>";

// Test donation ID
$donation_id = 14;

// Get donation details
$sql = "SELECT * FROM donations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ Donation ID {$donation_id} not found<br>";
    exit;
}

$donation = $result->fetch_assoc();
echo "✅ Found donation: {$donation['donation_ref']} - {$donation['amount']} RWF<br>";
echo "📧 Email: {$donation['email']}<br>";
echo "📱 Phone: {$donation['phone']}<br>";

// Test PaymentProcessor
try {
    $paymentProcessor = new PaymentProcessor();
    echo "✅ PaymentProcessor loaded successfully<br>";

    // Test email notification
    echo "<h3>Testing Email Notification</h3>";
    $email_result = $paymentProcessor->testEmailNotification($donation);
    if ($email_result) {
        echo "✅ Email sent successfully<br>";
    } else {
        echo "❌ Email failed to send<br>";
    }

    // Test SMS notification
    echo "<h3>Testing SMS Notification</h3>";
    $sms_result = $paymentProcessor->testSMSNotification($donation);
    if ($sms_result) {
        echo "✅ SMS sent successfully<br>";
    } else {
        echo "❌ SMS failed to send<br>";
    }

    // Update notification status in database
    $update_sql = "UPDATE donations SET email_sent = ?, sms_sent = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iii", $email_result, $sms_result, $donation_id);
    $update_stmt->execute();

    echo "<h3>Database Updated</h3>";
    echo "✅ Email sent status: " . ($email_result ? 'Yes' : 'No') . "<br>";
    echo "✅ SMS sent status: " . ($sms_result ? 'Yes' : 'No') . "<br>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><a href='donation_success.php?donation_id={$donation_id}'>← View Success Page</a></p>";
?>