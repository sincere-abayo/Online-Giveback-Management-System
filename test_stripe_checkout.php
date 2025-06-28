<?php
/**
 * Test Stripe Checkout Configuration
 */

include 'config.php';

echo "<h2>Stripe Checkout Configuration Test</h2>";

// Test 1: Check environment variables
echo "<h3>1. Environment Variables</h3>";
$stripe_publishable = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? 'Not set';
$stripe_secret = $_ENV['STRIPE_SECRET_KEY'] ?? 'Not set';

echo "✅ Stripe Publishable Key: " . substr($stripe_publishable, 0, 20) . "...<br>";
echo "✅ Stripe Secret Key: " . substr($stripe_secret, 0, 20) . "...<br>";

// Test 2: Check database settings
echo "<h3>2. Database Settings</h3>";
$sql = "SELECT setting_key, setting_value FROM payment_settings WHERE payment_method = 'stripe' AND is_active = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "✅ Found " . $result->num_rows . " Stripe settings in database:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['setting_key']}: " . substr($row['setting_value'], 0, 20) . "...<br>";
    }
} else {
    echo "❌ No Stripe settings found in database<br>";
}

// Test 3: Check donation table
echo "<h3>3. Donation Table</h3>";
$sql = "SELECT COUNT(*) as count FROM donations";
$result = $conn->query($sql);
$count = $result->fetch_assoc()['count'];
echo "✅ Found {$count} donations in database<br>";

// Test 4: Check file permissions
echo "<h3>4. File Permissions</h3>";
$files = [
    'create_stripe_checkout.php',
    'stripe_webhook.php',
    'donation_success.php',
    'payment_stripe.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "✅ {$file}: Readable<br>";
        } else {
            echo "❌ {$file}: Not readable<br>";
        }
    } else {
        echo "❌ {$file}: File not found<br>";
    }
}

// Test 5: Check Stripe PHP library
echo "<h3>5. Stripe PHP Library</h3>";
if (file_exists('vendor/autoload.php')) {
    echo "✅ Stripe PHP library found<br>";
} else {
    echo "❌ Stripe PHP library not found<br>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Make sure your Stripe webhook is configured to point to: <code>https://yourdomain.com/stripe_webhook.php</code></li>";
echo "<li>Set up the webhook events: <code>checkout.session.completed</code> and <code>payment_intent.payment_failed</code></li>";
echo "<li>Test the payment flow by making a donation</li>";
echo "</ol>";

echo "<p><a href='donation.php'>← Back to Donation Page</a></p>";
?>