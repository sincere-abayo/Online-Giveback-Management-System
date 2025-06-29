<?php
/**
 * Volunteer Assignment Notification Test File
 * 
 * Use this file to test the volunteer assignment notification system
 * Make sure to update the credentials in .env file first
 */

require_once 'classes/MessagingService.php';

echo "<h2>Volunteer Assignment Notification Test for Dufatanye Charity Foundation</h2>";

// Check if credentials are set
if (empty($_ENV['AFRICASTALKING_USERNAME']) || empty($_ENV['AFRICASTALKING_API_KEY'])) {
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

// Test with a sample assignment ID (you can change this to test with real data)
$test_assignment_id = 36; // Change this to a real assignment ID from your database

echo "<div style='background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Testing Volunteer Assignment Notification for Assignment ID: " . $test_assignment_id . "</h3>";
echo "<p><strong>Note:</strong> This will send real notifications to the volunteer. Make sure the assignment ID exists in your database.</p>";

try {
    $messagingService = new MessagingService();
    $result = $messagingService->sendVolunteerAssignmentNotification($test_assignment_id);

    if ($result['success']) {
        echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
        echo "<strong>Success!</strong> Notification sent successfully.<br>";
        echo "Email sent: " . ($result['email_sent'] ? 'Yes' : 'No') . "<br>";
        echo "SMS sent: " . ($result['sms_sent'] ? 'Yes' : 'No');
        echo "</div>";
    } else {
        echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
        echo "<strong>Error:</strong> " . $result['message'];
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "<strong>Exception:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "</div>";

// Show configuration info
echo "<div style='background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Current Configuration:</h3>";
echo "<p><strong>Africa's Talking Username:</strong> " . $_ENV['AFRICASTALKING_USERNAME'] . "</p>";
echo "<p><strong>Africa's Talking API Key:</strong> " . substr($_ENV['AFRICASTALKING_API_KEY'], 0, 10) . "...</p>";
echo "<p><strong>SMTP Host:</strong> " . ($_ENV['SMTP_HOST'] ?? 'Not configured') . "</p>";
echo "<p><strong>SMTP Username:</strong> " . ($_ENV['SMTP_USERNAME'] ?? 'Not configured') . "</p>";
echo "</div>";

// Show available SMS templates
echo "<div style='background: #f3e5f5; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Available SMS Templates:</h3>";

try {
    $conn = new mysqli($_ENV['DB_HOST'] ?? 'localhost', $_ENV['DB_USERNAME'] ?? 'root', $_ENV['DB_PASSWORD'] ?? '', $_ENV['DB_NAME'] ?? 'gms');

    if ($conn->connect_error) {
        echo "<p style='color: red;'>Database connection failed: " . $conn->connect_error . "</p>";
    } else {
        $result = $conn->query("SELECT template_name, message, is_active FROM sms_templates ORDER BY template_name");

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div style='margin: 10px 0; padding: 10px; background: white; border-radius: 3px;'>";
                echo "<strong>" . ucfirst(str_replace('_', ' ', $row['template_name'])) . ":</strong> ";
                echo "<span style='color: " . ($row['is_active'] ? 'green' : 'red') . ";'>" . ($row['is_active'] ? 'Active' : 'Inactive') . "</span><br>";
                echo "<code>" . htmlspecialchars($row['message']) . "</code>";
                echo "</div>";
            }
        } else {
            echo "<p style='color: orange;'>No SMS templates found in database.</p>";
        }

        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error loading SMS templates: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Show recent volunteer assignments
echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
echo "<h3>Recent Volunteer Assignments:</h3>";

try {
    $conn = new mysqli($_ENV['DB_HOST'] ?? 'localhost', $_ENV['DB_USERNAME'] ?? 'root', $_ENV['DB_PASSWORD'] ?? '', $_ENV['DB_NAME'] ?? 'gms');

    if ($conn->connect_error) {
        echo "<p style='color: red;'>Database connection failed: " . $conn->connect_error . "</p>";
    } else {
        $result = $conn->query("
            SELECT vh.id, vh.volunteer_id, vh.activity_id, vh.year, vh.s, vh.email_sent, vh.sms_sent,
                   v.firstname, v.lastname, v.email, v.contact,
                   a.name as activity_name,
                   p.name as program_name
            FROM volunteer_history vh
            JOIN volunteer_list v ON vh.volunteer_id = v.id
            JOIN activity_list a ON vh.activity_id = a.id
            JOIN program_list p ON a.program_id = p.id
            ORDER BY vh.date_created DESC
            LIMIT 5
        ");

        if ($result && $result->num_rows > 0) {
            echo "<table style='width: 100%; border-collapse: collapse; margin-top: 10px;'>";
            echo "<tr style='background: #f8f9fa;'>";
            echo "<th style='border: 1px solid #ddd; padding: 8px;'>ID</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px;'>Volunteer</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px;'>Activity</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px;'>Date</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px;'>Email</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px;'>SMS</th>";
            echo "</tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $row['id'] . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['activity_name']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . date('M j, Y', strtotime($row['year'])) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>";
                echo $row['email_sent'] ? "<span style='color: green;'>✓ Sent</span>" : "<span style='color: red;'>✗ Not Sent</span>";
                echo "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>";
                echo $row['sms_sent'] ? "<span style='color: green;'>✓ Sent</span>" : "<span style='color: red;'>✗ Not Sent</span>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>No volunteer assignments found.</p>";
        }

        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error loading volunteer assignments: " . $e->getMessage() . "</p>";
}

echo "</div>";

echo "<p><a href='admin/volunteer/view_volunteer.php'>← Back to Volunteer Management</a></p>";
?>