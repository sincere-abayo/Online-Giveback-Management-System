<?php
require_once(__DIR__ . '/../config.php');
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/MessagingService.php';

class PayPackHandler extends DBConnection
{
    private $clientId;
    private $clientSecret;
    private $apiUrl = 'https://payments.paypack.rw/api';
    private $messagingService;

    public function __construct()
    {
        error_log("=== PAYPACK HANDLER CONSTRUCTOR ===");
        parent::__construct();
        $this->messagingService = new MessagingService();
        $this->loadSettings();
        error_log("PayPack handler initialized");
    }

    /**
     * Initiate PayPack payment for donation
     */
    public function initiateDonationPayment($donationId, $amount, $phoneNumber)
    {
        try {
            error_log("=== PAYPACK DONATION PAYMENT INITIATION ===");
            error_log("Donation ID: " . $donationId);
            error_log("Amount: " . $amount);
            error_log("Original phone input: '" . $phoneNumber . "'");

            // Format phone number
            $formattedPhone = $this->formatPhoneForPayPack($phoneNumber);
            error_log("Formatted phone for PayPack: " . $formattedPhone);

            // Get authentication token
            $token = $this->getPaypackToken();
            if (!$token) {
                error_log("Failed to get authentication token");
                return [
                    'success' => false,
                    'message' => 'Failed to authenticate with PayPack - check credentials and API connectivity'
                ];
            }

            error_log("Token obtained, proceeding with payment");

            // Generate unique reference
            $reference = 'donation_' . $donationId . '_' . time();
            error_log("Payment reference: " . $reference);

            // Create payment transaction record
            try {
                $stmt = $this->conn->prepare("
                    INSERT INTO payment_transactions (donation_id, payment_method, amount, currency, status, gateway_reference, created_at)
                    VALUES (?, 'paypack', ?, 'RWF', 'pending', ?, NOW())
                ");
                $stmt->bind_param("ids", $donationId, $amount, $reference);
                $stmt->execute();
                $transactionId = $this->conn->insert_id;
                error_log("Transaction record created with ID: " . $transactionId);
            } catch (Exception $dbError) {
                error_log("Database error creating transaction: " . $dbError->getMessage());
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $dbError->getMessage()
                ];
            }

            // Make payment request
            $curl = curl_init();

            $headers = [
                "Authorization: Bearer $token",
                'Content-Type: application/json'
            ];

            $paymentData = [
                "amount" => floatval($amount),
                "number" => $formattedPhone
            ];

            $jsonPayload = json_encode($paymentData);

            error_log("=== PAYPACK API REQUEST DEBUG ===");
            error_log("Payment request URL: " . $this->apiUrl . '/transactions/cashin');
            error_log("Payment request headers: " . json_encode($headers));
            error_log("Payment request data (JSON): " . $jsonPayload);

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->apiUrl . '/transactions/cashin',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 30,
            ));

            $response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($curl);

            error_log("=== PAYPACK API RESPONSE DEBUG ===");
            error_log("HTTP Code: " . $http_code);
            error_log("Raw Response: " . $response);
            error_log("CURL Error: " . $curl_error);

            curl_close($curl);

            // Handle response
            if ($curl_error) {
                error_log("CURL error: " . $curl_error);

                // Update transaction as failed
                $stmt = $this->conn->prepare("
                    UPDATE payment_transactions 
                    SET status = 'failed', failure_reason = ?, updated_at = NOW()
                    WHERE transaction_id = ?
                ");
                $stmt->bind_param("si", $curl_error, $transactionId);
                $stmt->execute();

                return [
                    'success' => false,
                    'message' => 'Network error: ' . $curl_error
                ];
            }

            $responseData = json_decode($response, true);
            error_log("Decoded response data: " . json_encode($responseData));

            // Update transaction record based on response
            if ($http_code == 200 || $http_code == 201) {
                $gatewayId = $responseData['ref'] ?? $reference;

                $stmt = $this->conn->prepare("
                    UPDATE payment_transactions 
                    SET status = 'processing', gateway_transaction_id = ?, gateway_response = ?, updated_at = NOW()
                    WHERE transaction_id = ?
                ");
                $stmt->bind_param("ssi", $gatewayId, $response, $transactionId);
                $stmt->execute();

                // Update donation status
                $stmt = $this->conn->prepare("
                    UPDATE donations 
                    SET status = 'processing', payment_id = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->bind_param("si", $gatewayId, $donationId);
                $stmt->execute();

                error_log("Payment successful - Transaction ID: " . $transactionId . ", Gateway Ref: " . $gatewayId);

                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'gateway_reference' => $gatewayId,
                    'message' => 'Payment initiated successfully. Please check your phone for the payment prompt.'
                ];
            } else {
                $failureReason = $responseData['message'] ?? 'Payment initiation failed (HTTP ' . $http_code . ')';

                $stmt = $this->conn->prepare("
                    UPDATE payment_transactions 
                    SET status = 'failed', failure_reason = ?, gateway_response = ?, updated_at = NOW()
                    WHERE transaction_id = ?
                ");
                $stmt->bind_param("ssi", $failureReason, $response, $transactionId);
                $stmt->execute();

                // Update donation status
                $stmt = $this->conn->prepare("
                    UPDATE donations 
                    SET status = 'failed', updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->bind_param("i", $donationId);
                $stmt->execute();

                error_log("Payment failed - Reason: " . $failureReason);

                return [
                    'success' => false,
                    'message' => $failureReason,
                    'debug_info' => [
                        'http_code' => $http_code,
                        'response' => $responseData,
                        'curl_error' => $curl_error
                    ]
                ];
            }

        } catch (Exception $e) {
            error_log("PayPack Exception: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Payment processing error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check payment status and send notifications
     */
    public function checkPaymentStatus($transactionId)
    {
        try {
            error_log("=== CHECKING PAYMENT STATUS ===");
            error_log("Transaction ID: " . $transactionId);

            // Get the gateway reference from database
            $stmt = $this->conn->prepare("
                SELECT pt.gateway_transaction_id, pt.gateway_reference, pt.status, pt.donation_id,
                       d.fullname, d.email, d.phone, d.amount
                FROM payment_transactions pt
                JOIN donations d ON pt.donation_id = d.id
                WHERE pt.transaction_id = ?
            ");
            $stmt->bind_param("i", $transactionId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result) {
                error_log("Transaction not found in database");
                return ['success' => false, 'message' => 'Transaction not found'];
            }

            $reference = $result['gateway_transaction_id'] ?? $result['gateway_reference'];
            if (!$reference) {
                error_log("No gateway reference found");
                return ['success' => false, 'message' => 'No gateway reference found'];
            }

            error_log("Checking status for reference: " . $reference);

            // Get authentication token
            $token = $this->getPaypackToken();
            if (!$token) {
                error_log("Failed to get authentication token for status check");
                return ['success' => false, 'message' => 'Authentication failed'];
            }

            // Make status check request
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->apiUrl . "/transactions/find/" . $reference,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer $token"
                ),
            ));

            $response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($curl);

            error_log("Status check HTTP Code: " . $http_code);
            error_log("Status check response: " . $response);

            curl_close($curl);

            if ($curl_error) {
                error_log("CURL error during status check: " . $curl_error);
                return ['success' => false, 'message' => 'Network error during status check'];
            }

            $paypackStatus = null;
            if ($http_code == 200) {
                $responseData = json_decode($response, true);

                if ($responseData && isset($responseData['status'])) {
                    $paypackStatus = $responseData['status'];
                    error_log("PayPack status: " . $paypackStatus);

                    // Map PayPack status to our system status
                    $newStatus = null;
                    switch (strtolower($paypackStatus)) {
                        case 'successful':
                        case 'completed':
                            $newStatus = 'completed';
                            break;
                        case 'failed':
                        case 'cancelled':
                            $newStatus = 'failed';
                            break;
                        case 'pending':
                        case 'processing':
                            $newStatus = 'processing';
                            break;
                    }

                    // Update transaction status if changed
                    if ($newStatus && $newStatus !== $result['status']) {
                        $stmt = $this->conn->prepare("
                            UPDATE payment_transactions 
                            SET status = ?, updated_at = NOW(), gateway_response = ?
                            WHERE transaction_id = ?
                        ");
                        $stmt->bind_param("ssi", $newStatus, $response, $transactionId);
                        $stmt->execute();

                        // Update donation status
                        $stmt = $this->conn->prepare("
                            UPDATE donations 
                            SET status = ?, updated_at = NOW()
                            WHERE id = ?
                        ");
                        $stmt->bind_param("si", $newStatus, $result['donation_id']);
                        $stmt->execute();

                        error_log("Transaction status updated to: " . $newStatus);

                        // If completed, send notifications
                        $notifications = null;
                        if ($newStatus === 'completed') {
                            $notifications = $this->sendDonationNotifications($result['donation_id']);
                            error_log("Notification result: " . json_encode($notifications));
                        }

                        return [
                            'success' => true,
                            'status_updated' => true,
                            'new_status' => $newStatus,
                            'notifications_sent' => $notifications,
                            'message' => 'Payment status updated'
                        ];
                    } else {
                        // If already completed, send notifications if not sent yet
                        $notifications = null;
                        if ($newStatus === 'completed') {
                            $notifications = $this->sendDonationNotifications($result['donation_id']);
                            error_log("Notification result: " . json_encode($notifications));
                        }
                        return [
                            'success' => true,
                            'status_updated' => false,
                            'current_status' => $result['status'],
                            'notifications_sent' => $notifications,
                            'message' => 'No status change'
                        ];
                    }
                }
            }

            // If no status or not found, try events endpoint
            if (!$paypackStatus) {
                $eventResult = $this->checkPaypackEvents($token, $reference);
                if ($eventResult['http_code'] == 200 && !empty($eventResult['decoded']['transactions'])) {
                    foreach ($eventResult['decoded']['transactions'] as $event) {
                        if ($event['event_kind'] === 'transaction:processed' && isset($event['data']['status'])) {
                            $eventStatus = strtolower($event['data']['status']);
                            $newStatus = null;
                            if ($eventStatus === 'successful') {
                                $newStatus = 'completed';
                            } elseif ($eventStatus === 'failed') {
                                $newStatus = 'failed';
                            }
                            if ($newStatus && $newStatus !== $result['status']) {
                                $stmt = $this->conn->prepare("
                                    UPDATE payment_transactions 
                                    SET status = ?, updated_at = NOW(), gateway_response = ?
                                    WHERE transaction_id = ?
                                ");
                                $stmt->bind_param("ssi", $newStatus, json_encode($eventResult['decoded']), $transactionId);
                                $stmt->execute();

                                // Update donation status
                                $stmt = $this->conn->prepare("
                                    UPDATE donations 
                                    SET status = ?, updated_at = NOW()
                                    WHERE id = ?
                                ");
                                $stmt->bind_param("si", $newStatus, $result['donation_id']);
                                $stmt->execute();

                                // If completed, send notifications
                                $notifications = null;
                                if ($newStatus === 'completed') {
                                    $notifications = $this->sendDonationNotifications($result['donation_id']);
                                }
                                return [
                                    'success' => true,
                                    'status_updated' => true,
                                    'new_status' => $newStatus,
                                    'notifications_sent' => $notifications,
                                    'message' => 'Payment status updated from events endpoint'
                                ];
                            }
                        }
                    }
                }
            }

            error_log("Status check failed - HTTP Code: " . $http_code);
            return ['success' => false, 'message' => 'Failed to check payment status'];

        } catch (Exception $e) {
            error_log("Exception during status check: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error checking payment status: ' . $e->getMessage()];
        }
    }

    /**
     * Send donation notifications
     */
    private function sendDonationNotifications($donationId)
    {
        try {
            // Get donation details
            $stmt = $this->conn->prepare("
                SELECT d.*, pt.transaction_id, pt.gateway_reference
                FROM donations d
                LEFT JOIN payment_transactions pt ON d.id = pt.donation_id
                WHERE d.id = ?
            ");
            $stmt->bind_param("i", $donationId);
            $stmt->execute();
            $donation = $stmt->get_result()->fetch_assoc();

            if (!$donation) {
                return ['success' => false, 'message' => 'Donation not found'];
            }

            // Send email notification
            $email_sent = $this->sendDonationEmail($donation);

            // Send SMS notification
            $sms_sent = $this->sendDonationSMS($donation);

            // Update notification status
            $stmt = $this->conn->prepare("
                UPDATE donations 
                SET email_sent = ?, sms_sent = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param("iii", $email_sent, $sms_sent, $donationId);
            $stmt->execute();

            return [
                'success' => true,
                'email_sent' => $email_sent,
                'sms_sent' => $sms_sent
            ];

        } catch (Exception $e) {
            error_log("Error sending notifications: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send donation email
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
                        <a href='" . $_ENV['BASE_URL'] . "/donation_success.php?donation_id={$donation['id']}' style='background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; display: inline-block;'>View Donation Details</a>
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
     * Send donation SMS
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

    /**
     * Format phone number for PayPack
     */
    private function formatPhoneForPayPack($phoneNumber)
    {
        error_log("=== PHONE FORMATTING FOR PAYPACK ===");
        error_log("Original input: '" . $phoneNumber . "'");

        // Remove non-digits
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        error_log("After removing non-digits: '" . $phone . "'");

        if (strlen($phone) == 12 && substr($phone, 0, 3) == '250') {
            $formatted = '0' . substr($phone, 3);
        } elseif (strlen($phone) == 11 && substr($phone, 0, 2) == '25') {
            $formatted = '0' . substr($phone, 2);
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $formatted = $phone;
        } elseif (strlen($phone) == 9) {
            $formatted = '0' . $phone;
        } else {
            $formatted = $phone; // Fallback
        }

        error_log("Final formatted number: " . $formatted);
        return $formatted;
    }

    /**
     * Load PayPack settings
     */
    private function loadSettings()
    {
        error_log("=== LOADING PAYPACK SETTINGS ===");

        // First try environment variables
        $this->clientId = $_ENV['PAYPACK_CLIENT_ID'] ?? '';
        $this->clientSecret = $_ENV['PAYPACK_CLIENT_SECRET'] ?? '';
        $this->apiUrl = $_ENV['PAYPACK_API_URL'] ?? 'https://payments.paypack.rw/api';

        // If not in env, try database
        if (empty($this->clientId) || empty($this->clientSecret)) {
            $stmt = $this->conn->prepare("SELECT setting_key, setting_value FROM payment_settings WHERE gateway = 'paypack' AND is_active = 1");
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                switch ($row['setting_key']) {
                    case 'client_id':
                        $this->clientId = trim($row['setting_value']);
                        break;
                    case 'client_secret':
                        $this->clientSecret = trim($row['setting_value']);
                        break;
                    case 'api_url':
                        $this->apiUrl = trim($row['setting_value']);
                        break;
                }
            }
        }

        // Fallback to hardcoded values if not found (for testing)
        if (empty($this->clientId) || empty($this->clientSecret)) {
            error_log("Using fallback credentials");
            $this->clientId = 'cb3de54e-4900-11f0-ab56-dead131a2dd9';
            $this->clientSecret = '522f8f6a27effc0a15a685e455d8a0bfda39a3ee5e6b4b0d3255bfef95601890afd80709';
        }

        error_log("Final settings - Client ID: " . ($this->clientId ? 'SET' : 'NOT SET'));
        error_log("Final settings - Client Secret: " . ($this->clientSecret ? 'SET' : 'NOT SET'));
        error_log("Final settings - API URL: " . $this->apiUrl);
    }

    /**
     * Get PayPack authentication token
     */
    private function getPaypackToken()
    {
        error_log("=== GETTING PAYPACK TOKEN ===");

        if (empty($this->clientId) || empty($this->clientSecret)) {
            error_log("PayPack credentials not set");
            return false;
        }

        $curl = curl_init();

        $postData = json_encode([
            "client_id" => $this->clientId,
            "client_secret" => $this->clientSecret
        ]);

        error_log("Auth request URL: " . $this->apiUrl . '/auth/agents/authorize');

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . '/auth/agents/authorize',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);

        error_log("Auth response HTTP Code: " . $http_code);
        if ($curl_error) {
            error_log("Auth CURL Error: " . $curl_error);
        }

        curl_close($curl);

        if ($curl_error) {
            error_log("CURL error occurred: " . $curl_error);
            return false;
        }

        if ($http_code == 200) {
            $data = json_decode($response, true);
            if ($data && isset($data['access'])) {
                error_log("Token obtained successfully");
                return $data['access'];
            } else {
                error_log("No access token in response");
                return false;
            }
        }

        error_log("Auth failed - HTTP Code: " . $http_code);
        return false;
    }

    /**
     * Check PayPack events
     */
    private function checkPaypackEvents($token, $reference)
    {
        $curl = curl_init();
        $url = $this->apiUrl . "/events/transactions?ref=" . urlencode($reference);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array("Authorization: Bearer $token", "Accept: application/json"),
            CURLOPT_TIMEOUT => 30,
        ));
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $decoded = json_decode($response, true);
        return [
            'http_code' => $http_code,
            'response' => $response,
            'decoded' => $decoded
        ];
    }
}
?>