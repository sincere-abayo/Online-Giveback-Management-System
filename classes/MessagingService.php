<?php
require_once(__DIR__ . '/../config.php');
require_once __DIR__ . '/../vendor/autoload.php';

use AfricasTalking\SDK\AfricasTalking;

class MessagingService extends DBConnection
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Send payment confirmation notifications
     */
    public function sendPaymentConfirmation($transactionId)
    {
        try {
            // Get transaction and donation details
            $stmt = $this->conn->prepare("
                SELECT pt.*, d.donation_ref, d.fullname, d.email, d.phone, d.amount, d.message
                FROM payment_transactions pt
                JOIN donations d ON pt.donation_id = d.id
                WHERE pt.transaction_id = ?
            ");
            $stmt->bind_param("i", $transactionId);
            $stmt->execute();
            $transaction = $stmt->get_result()->fetch_assoc();

            if (!$transaction) {
                return ['success' => false, 'message' => 'Transaction not found'];
            }

            // Send email notification
            $email_sent = $this->sendDonationEmail($transaction);

            // Send SMS notification
            $sms_sent = $this->sendDonationSMS($transaction);

            // Update notification status
            $stmt = $this->conn->prepare("UPDATE donations SET email_sent = ?, sms_sent = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("iii", $email_sent, $sms_sent, $transaction['donation_id']);
            $stmt->execute();

            return [
                'success' => true,
                'email_sent' => $email_sent,
                'sms_sent' => $sms_sent
            ];

        } catch (Exception $e) {
            error_log("Error sending payment confirmation: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send volunteer activity assignment notifications
     */
    public function sendVolunteerAssignmentNotification($assignmentId)
    {
        try {
            // Get assignment details with volunteer and activity information
            $stmt = $this->conn->prepare("
                SELECT vh.*, 
                       v.firstname, v.lastname, v.email, v.contact, v.roll,
                       a.name as activity_name,
                       p.name as program_name
                FROM volunteer_history vh
                JOIN volunteer_list v ON vh.volunteer_id = v.id
                JOIN activity_list a ON vh.activity_id = a.id
                JOIN program_list p ON a.program_id = p.id
                WHERE vh.id = ?
            ");
            $stmt->bind_param("i", $assignmentId);
            $stmt->execute();
            $assignment = $stmt->get_result()->fetch_assoc();

            if (!$assignment) {
                return ['success' => false, 'message' => 'Assignment not found'];
            }

            // Send email notification
            $email_sent = $this->sendVolunteerAssignmentEmail($assignment);

            // Send SMS notification
            $sms_sent = $this->sendVolunteerAssignmentSMS($assignment);

            // Update notification status
            $stmt = $this->conn->prepare("UPDATE volunteer_history SET email_sent = ?, sms_sent = ? WHERE id = ?");
            $stmt->bind_param("iii", $email_sent, $sms_sent, $assignmentId);
            $stmt->execute();

            return [
                'success' => true,
                'email_sent' => $email_sent,
                'sms_sent' => $sms_sent
            ];

        } catch (Exception $e) {
            error_log("Error sending volunteer assignment notification: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send volunteer assignment email notification
     */
    private function sendVolunteerAssignmentEmail($assignment)
    {
        try {
            // Get email settings
            $smtp_host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $smtp_port = $_ENV['SMTP_PORT'] ?? 587;
            $smtp_username = $_ENV['SMTP_USERNAME'] ?? '';
            $smtp_password = $_ENV['SMTP_PASSWORD'] ?? '';
            $from_email = $_ENV['FROM_EMAIL'] ?? 'noreply@dufatanye.org';
            $from_name = $_ENV['FROM_NAME'] ?? 'Dufatanye Charity Foundation';

            if (empty($smtp_username) || empty($smtp_password)) {
                error_log("SMTP credentials not configured");
                return false;
            }

            // Create PHPMailer instance
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = $smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_username;
            $mail->Password = $smtp_password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $smtp_port;

            // Recipients
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($assignment['email'], $assignment['firstname'] . ' ' . $assignment['lastname']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Volunteer Activity Assignment - Dufatanye Charity Foundation';

            // Email template
            $emailBody = $this->getVolunteerAssignmentEmailHTML($assignment);

            $mail->Body = $emailBody;
            $mail->AltBody = $this->getVolunteerAssignmentEmailText($assignment);

            $mail->send();
            error_log("Volunteer assignment email sent successfully to: " . $assignment['email']);
            return true;

        } catch (Exception $e) {
            error_log("Error sending volunteer assignment email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send volunteer assignment SMS notification
     */
    private function sendVolunteerAssignmentSMS($assignment)
    {
        try {
            // Get SMS settings - using same approach as payment system
            $username = $this->getPaymentSetting('sms', 'africas_talking_username');
            $api_key = $this->getPaymentSetting('sms', 'africas_talking_api_key');

            if (!$username || !$api_key) {
                error_log("SMS configuration not found in database, trying environment variables");
                // Fallback to environment variables
                $api_key = $_ENV['AFRICASTALKING_API_KEY'] ?? '';
                $username = $_ENV['AFRICASTALKING_USERNAME'] ?? '';
            }

            if (empty($api_key) || empty($username)) {
                error_log("Africa's Talking credentials not configured in database or environment");
                return false;
            }

            // Format phone number properly (like payment system)
            $phone = $assignment['contact'];
            if (!preg_match('/^\+/', $phone)) {
                $phone = '+250' . ltrim($phone, '0');
            }

            // Get SMS template from database
            $stmt = $this->conn->prepare("SELECT message FROM sms_templates WHERE template_name = 'volunteer_assignment' AND is_active = 1");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $template = $result->fetch_assoc();
                $message = $template['message'];

                // Replace template variables
                $replacements = [
                    '{firstname}' => $assignment['firstname'],
                    '{activity_name}' => $assignment['activity_name'],
                    '{program_name}' => $assignment['program_name'],
                    '{date}' => date('M j, Y', strtotime($assignment['year'])),
                    '{session}' => $assignment['s']
                ];

                $message = str_replace(array_keys($replacements), array_values($replacements), $message);
            } else {
                // Fallback message if template not found
                $message = "Hello {$assignment['firstname']}! You have been assigned to {$assignment['activity_name']} ({$assignment['program_name']}) on " . date('M j, Y', strtotime($assignment['year'])) . ". Session: {$assignment['s']}. Dufatanye Charity Foundation";
            }

            // Limit message to 160 characters
            if (strlen($message) > 160) {
                $message = substr($message, 0, 157) . '...';
            }

            // Initialize Africa's Talking
            $AT = new AfricasTalking($username, $api_key);
            $sms = $AT->sms();

            // Send SMS (without 'from' parameter - let Africa's Talking use default)
            $response = $sms->send([
                'to' => $phone,
                'message' => $message
            ]);

            // Validate response like payment system does
            $recipients = $response['data']->SMSMessageData->Recipients ?? [];
            $success = !empty($recipients) && $recipients[0]->status === 'Success';

            if ($success) {
                error_log("Volunteer assignment SMS sent successfully to: " . $phone);
            } else {
                error_log("Volunteer assignment SMS sending failed. Response: " . json_encode($response));
            }

            return $success;

        } catch (Exception $e) {
            error_log("Error sending volunteer assignment SMS: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get payment setting from database (same as PayPackHandler)
     */
    private function getPaymentSetting($method, $key)
    {
        $stmt = $this->conn->prepare("SELECT setting_value FROM payment_settings WHERE payment_method = ? AND setting_key = ? AND is_active = 1");
        $stmt->bind_param("ss", $method, $key);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return trim($row['setting_value']);
        }

        return null;
    }

    /**
     * Get volunteer assignment email HTML template
     */
    private function getVolunteerAssignmentEmailHTML($assignment)
    {
        $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
        $assignmentDate = date('F j, Y', strtotime($assignment['year']));
        $assignmentTime = date('h:i A', strtotime($assignment['year']));

        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>New Volunteer Activity Assignment - Dufatanye Charity Foundation</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                    background-color: #f4f4f4;
                }
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                }
                .email-header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 40px 30px;
                    text-align: center;
                }
                .email-header h1 {
                    margin: 0;
                    font-size: 28px;
                    font-weight: 600;
                }
                .email-header p {
                    margin: 10px 0 0 0;
                    font-size: 16px;
                    opacity: 0.9;
                }
                .email-body {
                    padding: 40px 30px;
                }
                .assignment-banner {
                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                    color: white;
                    padding: 25px;
                    border-radius: 15px;
                    text-align: center;
                    margin: 20px 0;
                }
                .assignment-banner h2 {
                    margin: 0 0 10px 0;
                    font-size: 24px;
                }
                .info-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin: 25px 0;
                }
                .info-item {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 10px;
                    border-left: 4px solid #667eea;
                }
                .info-item h4 {
                    margin: 0 0 10px 0;
                    color: #667eea;
                    font-size: 16px;
                }
                .info-item p {
                    margin: 0;
                    color: #555;
                    font-weight: 500;
                }
                .btn {
                    display: inline-block;
                    background: #667eea;
                    color: white;
                    padding: 12px 30px;
                    text-decoration: none;
                    border-radius: 25px;
                    margin: 10px 5px;
                    font-weight: 500;
                    transition: all 0.3s ease;
                }
                .btn:hover {
                    background: #5a6fd8;
                    transform: translateY(-2px);
                }
                .footer {
                    background: #333;
                    color: white;
                    padding: 20px;
                    text-align: center;
                    font-size: 14px;
                }
                .highlight {
                    background: #fff3cd;
                    border: 1px solid #ffeaa7;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                @media (max-width: 600px) {
                    .info-grid {
                        grid-template-columns: 1fr;
                    }
                    .email-header, .email-body {
                        padding: 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <h1>🎯 New Activity Assignment</h1>
                    <p>Dufatanye Charity Foundation</p>
                </div>
                
                <div class='email-body'>
                    <div class='assignment-banner'>
                        <h2>You've Been Assigned!</h2>
                        <p>Dear " . htmlentities($assignment['firstname'] . ' ' . $assignment['lastname']) . ",</p>
                    </div>
                    
                    <p>We're excited to inform you that you have been assigned to a new volunteer activity. Your dedication and commitment to our community are truly appreciated!</p>
                    
                    <div class='info-grid'>
                        <div class='info-item'>
                            <h4>📋 Activity</h4>
                            <p>" . htmlentities($assignment['activity_name']) . "</p>
                        </div>
                        <div class='info-item'>
                            <h4>🏢 Program</h4>
                            <p>" . htmlentities($assignment['program_name']) . "</p>
                        </div>
                        <div class='info-item'>
                            <h4>📅 Date</h4>
                            <p>$assignmentDate</p>
                        </div>
                        <div class='info-item'>
                            <h4>⏰ Session</h4>
                            <p>" . htmlentities($assignment['s']) . "</p>
                        </div>
                    </div>
                    
                    <div class='highlight'>
                        <strong>📝 Assignment Details:</strong><br>
                        <strong>Volunteer ID:</strong> " . htmlentities($assignment['roll']) . "<br>
                        <strong>Assignment Date:</strong> " . date('F j, Y', strtotime($assignment['date_created'])) . "<br>
                        " . (!empty($assignment['years']) ? "<strong>Additional Notes:</strong> " . htmlentities($assignment['years']) . "<br>" : "") . "
                    </div>
                    
                    <p><strong>What to expect:</strong></p>
                    <ul>
                        <li>Arrive 15 minutes before the scheduled time</li>
                        <li>Bring any required materials or equipment</li>
                        <li>Wear appropriate clothing for the activity</li>
                        <li>Check in with the activity coordinator upon arrival</li>
                    </ul>
                    
                    <p><strong>Important reminders:</strong></p>
                    <ul>
                        <li>Please confirm your attendance by logging into your dashboard</li>
                        <li>If you cannot attend, please notify us as soon as possible</li>
                        <li>Your participation makes a real difference in our community</li>
                    </ul>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$baseUrl}volunteer_login.php' class='btn'>🔐 Access Dashboard</a>
                        <a href='{$baseUrl}volunteer/programs.php' class='btn'>📋 View Activities</a>
                    </div>
                    
                    <p>Thank you for your continued support and dedication to making our community a better place!</p>
                    
                    <p>Best regards,<br>
                    <strong>The Dufatanye Charity Foundation Team</strong></p>
                </div>
                
                <div class='footer'>
                    <p>© " . date('Y') . " Dufatanye Charity Foundation. All rights reserved.</p>
                    <p>📞 Contact: +250 788445566 | 📧 Email: info@dufatanye.org</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Get volunteer assignment email text template
     */
    private function getVolunteerAssignmentEmailText($assignment)
    {
        $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
        $assignmentDate = date('F j, Y', strtotime($assignment['year']));

        return "
New Volunteer Activity Assignment - Dufatanye Charity Foundation

Dear " . $assignment['firstname'] . " " . $assignment['lastname'] . ",

We're excited to inform you that you have been assigned to a new volunteer activity. Your dedication and commitment to our community are truly appreciated!

ASSIGNMENT DETAILS:
- Activity: " . $assignment['activity_name'] . "
- Program: " . $assignment['program_name'] . "
- Date: $assignmentDate
- Session: " . $assignment['s'] . "
- Volunteer ID: " . $assignment['roll'] . "
- Assignment Date: " . date('F j, Y', strtotime($assignment['date_created'])) . "

" . (!empty($assignment['years']) ? "Additional Notes: " . $assignment['years'] . "\n\n" : "") . "

WHAT TO EXPECT:
- Arrive 15 minutes before the scheduled time
- Bring any required materials or equipment
- Wear appropriate clothing for the activity
- Check in with the activity coordinator upon arrival

IMPORTANT REMINDERS:
- Please confirm your attendance by logging into your dashboard
- If you cannot attend, please notify us as soon as possible
- Your participation makes a real difference in our community

Access your dashboard at: {$baseUrl}volunteer_login.php
View activities at: {$baseUrl}volunteer/programs.php

Thank you for your continued support and dedication to making our community a better place!

Best regards,
The Dufatanye Charity Foundation Team

Contact: +250 788445566 | Email: info@dufatanye.org";
    }

    /**
     * Send donation email notification
     */
    private function sendDonationEmail($donation)
    {
        try {
            // Get email settings
            $smtp_host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $smtp_port = $_ENV['SMTP_PORT'] ?? 587;
            $smtp_username = $_ENV['SMTP_USERNAME'] ?? '';
            $smtp_password = $_ENV['SMTP_PASSWORD'] ?? '';
            $from_email = $_ENV['FROM_EMAIL'] ?? 'noreply@dufatanye.org';
            $from_name = $_ENV['FROM_NAME'] ?? 'Dufatanye Charity Foundation';

            if (empty($smtp_username) || empty($smtp_password)) {
                error_log("SMTP credentials not configured");
                return false;
            }

            // Create PHPMailer instance
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = $smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_username;
            $mail->Password = $smtp_password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $smtp_port;

            // Recipients
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($donation['email'], $donation['fullname']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Thank You for Your Donation - Dufatanye Charity Foundation';

            // Email template
            $emailBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-size: 28px;'>Thank You for Your Donation!</h1>
                    <p style='margin: 10px 0 0 0; font-size: 16px;'>Your generosity makes a difference</p>
                </div>
                
                <div style='padding: 30px; background: #f8f9fa;'>
                    <h2 style='color: #333; margin-bottom: 20px;'>Donation Confirmation</h2>
                    
                    <div style='background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 8px 0; border-bottom: 1px solid #eee;'><strong>Donation Reference:</strong></td>
                                <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>{$donation['donation_ref']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; border-bottom: 1px solid #eee;'><strong>Amount:</strong></td>
                                <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>RWF " . number_format($donation['amount'], 0) . "</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; border-bottom: 1px solid #eee;'><strong>Payment Method:</strong></td>
                                <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Mobile Money (PayPack)</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; border-bottom: 1px solid #eee;'><strong>Status:</strong></td>
                                <td style='padding: 8px 0; border-bottom: 1px solid #eee;'><span style='color: #28a745; font-weight: bold;'>Completed</span></td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0;'><strong>Date:</strong></td>
                                <td style='padding: 8px 0;'>" . date('F d, Y h:i A', strtotime($donation['created_at'])) . "</td>
                            </tr>
                        </table>
                    </div>
                    
                    <p style='color: #666; line-height: 1.6;'>
                        Dear {$donation['fullname']},
                    </p>
                    
                    <p style='color: #666; line-height: 1.6;'>
                        Thank you for your generous donation to Dufatanye Charity Foundation. Your contribution of RWF " . number_format($donation['amount'], 0) . " will help us continue our mission to support communities in need.
                    </p>
                    
                    <p style='color: #666; line-height: 1.6;'>
                        Your donation has been successfully processed and will be used to fund our various programs and initiatives. We are committed to transparency and will keep you updated on how your donation is making a difference.
                    </p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . ($_ENV['BASE_URL'] ?? 'http://localhost/utb/GMS') . "/donation_success.php?donation_id={$donation['donation_id']}' style='background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; display: inline-block;'>View Donation Details</a>
                    </div>
                    
                    <p style='color: #666; line-height: 1.6;'>
                        If you have any questions about your donation, please don't hesitate to contact us.
                    </p>
                    
                    <p style='color: #666; line-height: 1.6;'>
                        Thank you again for your support!<br>
                        <strong>The Dufatanye Charity Foundation Team</strong>
                    </p>
                </div>
                
                <div style='background: #333; color: white; padding: 20px; text-align: center; font-size: 14px;'>
                    <p style='margin: 0;'>© " . date('Y') . " Dufatanye Charity Foundation. All rights reserved.</p>
                </div>
            </div>";

            $mail->Body = $emailBody;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $emailBody));

            $mail->send();
            error_log("Donation email sent successfully to: " . $donation['email']);
            return true;

        } catch (Exception $e) {
            error_log("Error sending donation email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send donation SMS notification
     */
    private function sendDonationSMS($donation)
    {
        try {
            // Get SMS settings
            $api_key = $_ENV['AFRICASTALKING_API_KEY'] ?? '';
            $username = $_ENV['AFRICASTALKING_USERNAME'] ?? '';
            $sender_id = $_ENV['AFRICASTALKING_SENDER_ID'] ?? 'DUFATANYE';

            if (empty($api_key) || empty($username)) {
                error_log("Africa's Talking credentials not configured");
                return false;
            }

            // Initialize Africa's Talking
            $AT = new AfricasTalking($username, $api_key);
            $sms = $AT->sms();

            // Prepare SMS message
            $message = "Thank you {$donation['fullname']}! Your donation of RWF " . number_format($donation['amount'], 0) . " has been received. Reference: {$donation['donation_ref']}. Dufatanye Charity Foundation";

            // Send SMS
            $result = $sms->send([
                'to' => $donation['phone'],
                'message' => $message,
                'from' => $sender_id
            ]);

            error_log("Donation SMS sent successfully to: " . $donation['phone']);
            return true;

        } catch (Exception $e) {
            error_log("Error sending donation SMS: " . $e->getMessage());
            return false;
        }
    }
}
?>