<?php
/**
 * Test Payment Flow
 * This file helps debug the payment processing flow
 */

include 'config.php';

echo "<h2>Payment Flow Test</h2>";

// Test database connection
echo "<h3>1. Database Connection Test</h3>";
try {
    $test_sql = "SELECT COUNT(*) as count FROM donations";
    $result = $conn->query($test_sql);
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "✅ Database connected successfully. Found {$count} donations.<br>";
    } else {
        echo "❌ Database query failed.<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test PaymentProcessor class
echo "<h3>2. PaymentProcessor Class Test</h3>";
try {
    require_once 'classes/PaymentProcessor.php';
    $paymentProcessor = new PaymentProcessor();
    echo "✅ PaymentProcessor class loaded successfully.<br>";
} catch (Exception $e) {
    echo "❌ PaymentProcessor error: " . $e->getMessage() . "<br>";
}

// Test donation table structure
echo "<h3>3. Donation Table Structure Test</h3>";
try {
    $columns_sql = "SHOW COLUMNS FROM donations";
    $result = $conn->query($columns_sql);
    if ($result) {
        echo "✅ Donation table columns:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']} ({$row['Type']})<br>";
        }
    } else {
        echo "❌ Could not get table structure.<br>";
    }
} catch (Exception $e) {
    echo "❌ Table structure error: " . $e->getMessage() . "<br>";
}

// Test environment variables
echo "<h3>4. Environment Variables Test</h3>";
$env_vars = [
    'STRIPE_SECRET_KEY',
    'STRIPE_PUBLISHABLE_KEY',
    'AFRICASTALKING_USERNAME',
    'AFRICASTALKING_API_KEY'
];

foreach ($env_vars as $var) {
    if (isset($_ENV[$var])) {
        echo "✅ {$var}: " . substr($_ENV[$var], 0, 10) . "...<br>";
    } else {
        echo "❌ {$var}: Not set<br>";
    }
}

// Test file permissions
echo "<h3>5. File Permissions Test</h3>";
$files = [
    'donation_success.php',
    'classes/PaymentProcessor.php',
    'config.php'
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

echo "<hr>";
echo "<p><a href='donation.php'>← Back to Donation Page</a></p>";
?>