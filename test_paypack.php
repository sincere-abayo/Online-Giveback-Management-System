<?php
/**
 * PayPack Integration Test Script
 * 
 * This script tests the PayPack mobile money integration
 * Run this to verify everything is working correctly
 */

require_once 'config.php';
require_once 'classes/PayPackHandler.php';

echo "<h2>PayPack Integration Test</h2>";

// Test 1: Check if PayPackHandler can be instantiated
echo "<h3>Test 1: PayPackHandler Initialization</h3>";
try {
    $paypackHandler = new PayPackHandler();
    echo "<div style='color: green;'>✓ PayPackHandler initialized successfully</div>";
} catch (Exception $e) {
    echo "<div style='color: red;'>✗ PayPackHandler initialization failed: " . $e->getMessage() . "</div>";
    exit;
}

// Test 2: Check PayPack credentials
echo "<h3>Test 2: PayPack Credentials</h3>";
try {
    $reflection = new ReflectionClass($paypackHandler);
    $clientIdProperty = $reflection->getProperty('clientId');
    $clientIdProperty->setAccessible(true);
    $clientId = $clientIdProperty->getValue($paypackHandler);
    
    $clientSecretProperty = $reflection->getProperty('clientSecret');
    $clientSecretProperty->setAccessible(true);
    $clientSecret = $clientSecretProperty->getValue($paypackHandler);
    
    if ($clientId && $clientSecret) {
        echo "<div style='color: green;'>✓ PayPack credentials loaded</div>";
        echo "<div style='color: blue;'>Client ID: " . substr($clientId, 0, 8) . "...</div>";
        echo "<div style='color: blue;'>Client Secret: " . substr($clientSecret, 0, 8) . "...</div>";
    } else {
        echo "<div style='color: red;'>✗ PayPack credentials not found</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>✗ Error checking credentials: " . $e->getMessage() . "</div>";
}

// Test 3: Test authentication
echo "<h3>Test 3: PayPack Authentication</h3>";
try {
    $reflection = new ReflectionClass($paypackHandler);
    $getTokenMethod = $reflection->getMethod('getPaypackToken');
    $getTokenMethod->setAccessible(true);
    
    $token = $getTokenMethod->invoke($paypackHandler);
    
    if ($token) {
        echo "<div style='color: green;'>✓ PayPack authentication successful</div>";
        echo "<div style='color: blue;'>Token: " . substr($token, 0, 20) . "...</div>";
    } else {
        echo "<div style='color: red;'>✗ PayPack authentication failed</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>✗ Error testing authentication: " . $e->getMessage() . "</div>";
}

// Test 4: Test phone number formatting
echo "<h3>Test 4: Phone Number Formatting</h3>";
try {
    $reflection = new ReflectionClass($paypackHandler);
    $formatMethod = $reflection->getMethod('formatPhoneForPayPack');
    $formatMethod->setAccessible(true);
    
    $testNumbers = [
        '250781234567' => '0781234567',
        '0781234567' => '0781234567',
        '781234567' => '0781234567',
        '0721234567' => '0721234567',
        '250721234567' => '0721234567'
    ];
    
    foreach ($testNumbers as $input => $expected) {
        $formatted = $formatMethod->invoke($paypackHandler, $input);
        if ($formatted === $expected) {
            echo "<div style='color: green;'>✓ {$input} → {$formatted}</div>";
        } else {
            echo "<div style='color: red;'>✗ {$input} → {$formatted} (expected: {$expected})</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>✗ Error testing phone formatting: " . $e->getMessage() . "</div>";
}

// Test 5: Test database connection
echo "<h3>Test 5: Database Connection</h3>";
try {
    $sql = "SELECT COUNT(*) as count FROM payment_settings WHERE payment_method = 'paypack'";
    $result = $conn->query($sql);
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        echo "<div style='color: green;'>✓ PayPack settings found in database ({$count} records)</div>";
        
        // Show settings
        $sql = "SELECT setting_key, setting_value FROM payment_settings WHERE payment_method = 'paypack'";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $value = $row['setting_value'];
            if (strpos($row['setting_key'], 'secret') !== false || strpos($row['setting_key'], 'key') !== false) {
                $value = substr($value, 0, 8) . '...';
            }
            echo "<div style='color: blue;'>- {$row['setting_key']}: {$value}</div>";
        }
    } else {
        echo "<div style='color: orange;'>⚠ No PayPack settings found in database</div>";
        echo "<div style='color: blue;'>Run 'php update_config.php' to sync settings</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>✗ Error checking database: " . $e->getMessage() . "</div>";
}

// Test 6: Test donation table structure
echo "<h3>Test 6: Database Tables</h3>";
try {
    $tables = ['donations', 'payment_transactions'];
    foreach ($tables as $table) {
        $sql = "SHOW TABLES LIKE '{$table}'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<div style='color: green;'>✓ Table '{$table}' exists</div>";
        } else {
            echo "<div style='color: red;'>✗ Table '{$table}' not found</div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>✗ Error checking tables: " . $e->getMessage() . "</div>";
}

// Test 7: Environment Variables
echo "<h3>Test 7: Environment Variables</h3>";
$envVars = ['PAYPACK_CLIENT_ID', 'PAYPACK_CLIENT_SECRET', 'PAYPACK_API_URL'];
foreach ($envVars as $var) {
    if (isset($_ENV[$var])) {
        $value = $_ENV[$var];
        if (strpos($var, 'SECRET') !== false || strpos($var, 'KEY') !== false) {
            $value = substr($value, 0, 8) . '...';
        }
        echo "<div style='color: green;'>✓ {$var}: {$value}</div>";
    } else {
        echo "<div style='color: orange;'>⚠ {$var} not set in environment</div>";
    }
}

// Test 8: File Structure
echo "<h3>Test 8: Required Files</h3>";
$files = [
    'classes/PayPackHandler.php',
    'payment_paypack.php',
    'donation_payment_status.php',
    'paypack.png'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<div style='color: green;'>✓ {$file} exists</div>";
    } else {
        echo "<div style='color: red;'>✗ {$file} not found</div>";
    }
}

// Summary
echo "<hr>";
echo "<h3>Test Summary</h3>";
echo "<p>If all tests pass, your PayPack integration is ready to use!</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Test a real donation with PayPack payment</li>";
echo "<li>Verify email and SMS notifications</li>";
echo "<li>Check payment status updates</li>";
echo "<li>Monitor error logs for any issues</li>";
echo "</ul>";

echo "<p><a href='donation.php'>← Go to Donation Page</a></p>";
echo "<p><a href='PAYPACK_INTEGRATION_GUIDE.md'>📖 View Integration Guide</a></p>";
?> 