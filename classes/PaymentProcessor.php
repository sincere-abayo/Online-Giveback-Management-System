<?php
require_once(__DIR__ . '/../config.php');
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/CurrencyConverter.php';

use AfricasTalking\SDK\AfricasTalking;

class PaymentProcessor extends DBConnection
{
    private $settings;

    public function __construct()
    {
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }

    /**
     * Get payment setting value
     */
    private function getPaymentSetting($method, $key)
    {
        // First try to get from environment variables
        $env_key = strtoupper($method . '_' . $key);
        if (isset($_ENV[$env_key])) {
            return $_ENV[$env_key];
        }

        // Fallback to database settings
        $sql = "SELECT setting_value FROM payment_settings WHERE payment_method = ? AND setting_key = ? AND is_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $method, $key);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['setting_value'];
        }
        return null;
    }

    /**
     * Process Stripe payment (Legacy method - prefer Stripe Checkout)
     */
    public function processStripePayment($donation_id, $token, $amount)
    {
        try {
            // Get donation details for currency information
            $sql = "SELECT original_currency, original_amount FROM donations WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $donation_id);
            $stmt->execute();
            $donation_result = $stmt->get_result()->fetch_assoc();
            
            $original_currency = $donation_result['original_currency'] ?? 'RWF';
            $original_amount = $donation_result['original_amount'] ?? $amount;
            
            // Get Stripe settings
            $secret_key = $this->getPaymentSetting('stripe', 'secret_key');
            if (!$secret_key) {
                throw new Exception('Stripe configuration not found');
            }

            // Initialize Stripe
            \Stripe\Stripe::setApiKey($secret_key);
            
            // Calculate correct amount for Stripe (smallest currency unit)
            $stripe_currency = strtolower($original_currency);
            if ($original_currency === 'USD') {
                $stripe_amount = round($original_amount * 100); // Convert to cents
            } else {
                $stripe_amount = round($original_amount); // RWF is already smallest unit
            }

            // Create charge
            $charge = \Stripe\Charge::create([
                'amount' => $stripe_amount,
                'currency' => $stripe_currency,
                'source' => $token,
                'description' => 'Donation to Dufatanye Charity Foundation',
                'metadata' => [
                    'donation_id' => $donation_id,
                    'original_currency' => $original_currency,
                    'original_amount' => $original_amount
                ]
            ]);

            if ($charge->status === 'succeeded') {
                // Update donation status
                $this->updateDonationStatus($donation_id, 'completed', $charge->id);

                // Send notifications
                $this->sendDonationNotifications($donation_id);

                return [
                    'success' => true,
                    'transaction_id' => $charge->id,
                    'message' => 'Payment processed successfully'
                ];
            } else {
                throw new Exception('Payment failed: ' . $charge->failure_message);
            }

        } catch (Exception $e) {
            $this->updateDonationStatus($donation_id, 'failed');
            return [
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process PayPack Mobile Money payment (unified MTN/Airtel)
     */
    public function processPayPackPayment($donation_id, $phone, $amount)
    {
        try {
            require_once __DIR__ . '/PayPackHandler.php';

            // Initialize PayPack handler
            $paypackHandler = new PayPackHandler();

            // Initiate payment
            $result = $paypackHandler->initiateDonationPayment($donation_id, $amount, $phone);

            if ($result['success']) {
                return [
                    'success' => true,
                    'transaction_id' => $result['transaction_id'],
                    'gateway_reference' => $result['gateway_reference'],
                    'message' => $result['message']
                ];
            } else {
                // Update donation status to failed
                $this->updateDonationStatus($donation_id, 'failed');
                return [
                    'success' => false,
                    'message' => $result['message']
                ];
            }

        } catch (Exception $e) {
            $this->updateDonationStatus($donation_id, 'failed');
            return [
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process MTN Mobile Money payment (deprecated - use PayPack instead)
     */
    public function processMTNPayment($donation_id, $phone, $amount)
    {
        // Redirect to PayPack for unified mobile money processing
        return $this->processPayPackPayment($donation_id, $phone, $amount);
    }

    /**
     * Process Airtel Money payment (deprecated - use PayPack instead)
     */
    public function processAirtelPayment($donation_id, $phone, $amount)
    {
        // Redirect to PayPack for unified mobile money processing
        return $this->processPayPackPayment($donation_id, $phone, $amount);
    }

    /**
     * Update donation status
     */
    private function updateDonationStatus($donation_id, $status, $payment_id = null)
    {
        $sql = "UPDATE donations SET status = ?, payment_id = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $payment_id, $donation_id);
        $stmt->execute();

        // Add to donation history if volunteer is logged in
        if ($status === 'completed') {
            $this->addToDonationHistory($donation_id);
        }
    }

    /**
     * Add donation to volunteer history
     */
    private function addToDonationHistory($donation_id)
    {
        $sql = "SELECT volunteer_id, amount FROM donations WHERE id = ? AND volunteer_id IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $donation = $result->fetch_assoc();
            $sql = "INSERT INTO donation_history (volunteer_id, donation_id, amount, status, created_at) 
                    VALUES (?, ?, ?, 'completed', NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iid", $donation['volunteer_id'], $donation_id, $donation['amount']);
            $stmt->execute();
        }
    }

    /**
     * Send donation notifications (email and SMS)
     */
    private function sendDonationNotifications($donation_id)
    {
        $sql = "SELECT * FROM donations WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $donation_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $donation = $result->fetch_assoc();

        // Send email notification
        $email_sent = $this->sendDonationEmail($donation);

        // Send SMS notification
        $sms_sent = $this->sendDonationSMS($donation);

        // Update notification status
        $sql = "UPDATE donations SET email_sent = ?, sms_sent = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $email_sent, $sms_sent, $donation_id);
        $stmt->execute();
    }

    /**
     * Send donation email notification
     */
    private function sendDonationEmail($donation)
    {
        try {
            // Get email template
            $sql = "SELECT * FROM email_templates WHERE template_name = 'donation_confirmation' AND is_active = 1";
            $result = $this->conn->query($sql);

            if ($result->num_rows === 0) {
                return false;
            }

            $template = $result->fetch_assoc();

            // Replace placeholders
            $subject = $template['subject'];
            $html_content = $template['html_content'];
            $text_content = $template['text_content'];

            $replacements = [
                '{donation_ref}' => $donation['donation_ref'],
                '{amount}' => number_format($donation['amount'], 0),
                '{date}' => date('F j, Y', strtotime($donation['created_at'])),
                '{payment_method}' => ucfirst($donation['payment_method']),
                '{fullname}' => $donation['fullname'],
                '{email}' => $donation['email'],
                '{phone}' => $donation['phone'],
                '{payment_id}' => $donation['payment_id'],
                '{dashboard_url}' => 'http://' . $_SERVER['HTTP_HOST'] . '/volunteer_dashboard.php'
            ];

            $subject = str_replace(array_keys($replacements), array_values($replacements), $subject);
            $html_content = str_replace(array_keys($replacements), array_values($replacements), $html_content);
            $text_content = str_replace(array_keys($replacements), array_values($replacements), $text_content);

            // Send email using PHPMailer
            require_once __DIR__ . '/../vendor/autoload.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            // Configure email settings from environment variables
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'] ?? 'your-email@gmail.com';
            $mail->Password = $_ENV['SMTP_PASSWORD'] ?? 'your-password';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['SMTP_PORT'] ?? 587;

            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@dufatanye.org', $_ENV['SMTP_FROM_NAME'] ?? 'Dufatanye Charity Foundation');
            $mail->addAddress($donation['email'], $donation['fullname']);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_content;
            $mail->AltBody = $text_content;

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('Email error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send donation SMS notification using Africa's Talking
     */
    private function sendDonationSMS($donation)
    {
        try {
            // Get SMS settings
            $username = $this->getPaymentSetting('sms', 'africas_talking_username');
            $api_key = $this->getPaymentSetting('sms', 'africas_talking_api_key');

            if (!$username || !$api_key) {
                throw new Exception('SMS configuration not found');
            }

            // Format phone number
            $phone = $donation['phone'];
            if (!preg_match('/^\+/', $phone)) {
                $phone = '+250' . ltrim($phone, '0');
            }

            // Get SMS template
            $sql = "SELECT * FROM sms_templates WHERE template_name = 'donation_confirmation' AND is_active = 1";
            $result = $this->conn->query($sql);

            if ($result->num_rows === 0) {
                // Default message if no template
                $message = sprintf(
                    'Thank you %s! Your donation of %s RWF (Ref: %s) has been received. Dufatanye Charity Foundation',
                    $donation['fullname'],
                    number_format($donation['amount'], 0),
                    $donation['donation_ref']
                );
            } else {
                $template = $result->fetch_assoc();
                $message = $template['message'];

                // Replace placeholders
                $replacements = [
                    '{fullname}' => $donation['fullname'],
                    '{amount}' => number_format($donation['amount'], 0),
                    '{donation_ref}' => $donation['donation_ref'],
                    '{date}' => date('M j, Y', strtotime($donation['created_at']))
                ];

                $message = str_replace(array_keys($replacements), array_values($replacements), $message);
            }

            // Limit message to 160 characters
            if (strlen($message) > 160) {
                $message = substr($message, 0, 157) . '...';
            }

            // Initialize Africa's Talking
            $AT = new AfricasTalking($username, $api_key);
            $sms = $AT->sms();

            $response = $sms->send([
                'to' => $phone,
                'message' => $message
            ]);

            $recipients = $response['data']->SMSMessageData->Recipients ?? [];
            return !empty($recipients) && $recipients[0]->status === 'Success';

        } catch (Exception $e) {
            error_log('SMS error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process webhook payment completion (for Stripe Checkout)
     */
    public function processWebhookPayment($donation_id, $payment_intent_id)
    {
        try {
            // Update donation status
            $this->updateDonationStatus($donation_id, 'completed', $payment_intent_id);

            // Send notifications
            $this->sendDonationNotifications($donation_id);

            return [
                'success' => true,
                'message' => 'Payment processed successfully via webhook'
            ];
        } catch (Exception $e) {
            error_log('Webhook payment processing error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test email notification (public method for debugging)
     */
    public function testEmailNotification($donation)
    {
        return $this->sendDonationEmail($donation);
    }

    /**
     * Test SMS notification (public method for debugging)
     */
    public function testSMSNotification($donation)
    {
        return $this->sendDonationSMS($donation);
    }

    /**
     * Get donation statistics
     */
    public function getDonationStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total_donations,
                    SUM(amount) as total_amount,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_donations,
                    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_amount
                FROM donations";

        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    /**
     * Get volunteer donations
     */
    public function getVolunteerDonations($volunteer_id)
    {
        $sql = "SELECT d.*, dh.status as history_status 
                FROM donations d 
                LEFT JOIN donation_history dh ON d.id = dh.donation_id 
                WHERE d.volunteer_id = ? 
                ORDER BY d.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $volunteer_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>