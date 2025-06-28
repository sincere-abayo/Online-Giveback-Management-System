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
            $stmt = $this->conn->prepare("
                UPDATE donations 
                SET email_sent = ?, sms_sent = ?, updated_at = NOW()
                WHERE id = ?
            ");
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